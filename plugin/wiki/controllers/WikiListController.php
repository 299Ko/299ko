<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Maxime Blanc <maximeblanc@flexcb.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') or exit ('Access denied!');

class WikiListController extends PublicController
{

    public function home($currentPage = 1)
    {
        // Charger les fichiers de langue du plugin
        $this->runPlugin->loadLangFile();
        
        $wikiPageManager = new WikiPageManager();
        $categoriesManager = new WikiCategoriesManager();
        $activityManager = new WikiActivityManager();
        
        // Mode d'affichage
        $mode = ($wikiPageManager->count() > 0) ? 'list' : 'list_empty';

        // Récupération de toutes les pages publiées (sans pagination)
        $pages = [];
        foreach ($wikiPageManager->getItems() as $k => $v) {
            if (!$v->getDraft()) {
                $date = $v->getDate();
                $pages[$k]['name'] = $v->getName();
                $pages[$k]['date'] = util::FormatDate($date, 'en', 'fr');
                $pages[$k]['id'] = $v->getId();
                $pages[$k]['cats'] = [];
                foreach ($categoriesManager->getCategories() as $cat) {
                    if (in_array($v->getId(), $cat->items)) {
                        $pages[$k]['cats'][] = [
                            'label' => $cat->label,
                            'url' => $this->router->generate('wiki-category', ['name' => util::strToUrl($cat->label), 'id' => $cat->id]),
                        ];
                    }
                }
                $pages[$k]['content'] = $v->getContent();
                $pages[$k]['intro'] = $v->getIntro();
                $pages[$k]['url'] = $this->runPlugin->getPublicUrl() . $v->getSlug() . '-' . $v->getId() . '.html';
                $pages[$k]['img'] = $v->getImg();
                $pages[$k]['imgUrl'] = $v->getImgUrl();
            }
        }

        // Dernière activité
        $lastActivity = null;
        if ($this->runPlugin->getConfigVal('showLastActivity')) {
            $lastActivity = $activityManager->getFormattedLastActivity();
            
            // Si aucune activité n'existe, créer une activité de bienvenue
            if (!$lastActivity && $wikiPageManager->count() > 0) {
                $activityManager->addActivity('welcome', 'Documentation', '', 0);
                $lastActivity = $activityManager->getFormattedLastActivity();
            }
        }

        // Table des matières (TOC) - Toujours affichée car essentielle
        $toc = $this->generateWikiTOC($categoriesManager);

        // Traitements divers : métas, fil d'ariane...
        $this->runPlugin->setMainTitle($this->pluginsManager->getPlugin('wiki')->getConfigVal('label'));
        $this->runPlugin->setTitleTag($this->pluginsManager->getPlugin('wiki')->getConfigVal('label'));
        if ($this->runPlugin->getIsDefaultPlugin()) {
            $this->runPlugin->setTitleTag($this->pluginsManager->getPlugin('wiki')->getConfigVal('label'));
            $this->runPlugin->setMetaDescriptionTag($this->core->getConfigVal('siteDescription'));
        }

        $response = new PublicResponse();
        $tpl = $response->createPluginTemplate('wiki', 'home');

        $tpl->set('pages', $pages);
        $tpl->set('wikiPageManager', $wikiPageManager);
        $tpl->set('mode', $mode);
        $tpl->set('lastActivity', $lastActivity);
        $tpl->set('toc', $toc);
        $tpl->set('categoriesManager', $categoriesManager);
        $response->addTemplate($tpl);
        return $response;
    }

    public function category($name,$id, $currentPage = 1)
    {
        // Charger les fichiers de langue du plugin
        $this->runPlugin->loadLangFile();
        
        $categoriesManager = new WikiCategoriesManager();
        $category = $categoriesManager->getCategory($id);
        if (!$category) {
            $this->core->error404();
        }
        $wikiPageManager = new WikiPageManager();
        $pages = [];

        $pagesByPage = $this->runPlugin->getConfigVal('itemsByPage') ?: 10; // Valeur par défaut si non définie

        $start = ($currentPage - 1) * $pagesByPage + 1;
        $end = $start + $pagesByPage - 1;
        $i = 1;

        foreach ($wikiPageManager->getItems() as $k => $v) {
            if ($v->getDraft()) {
                continue;
            }
            if (in_array($v->getId(), $category->items)) {
                $date = $v->getDate();
                if ($i >= $start && $i <= $end) {
                    $pages[$k]['name'] = $v->getName();
                    $pages[$k]['date'] = util::FormatDate($date, 'en', 'fr');
                    $pages[$k]['id'] = $v->getId();
                    $pages[$k]['cats'] = [];
                    foreach ($categoriesManager->getCategories() as $cat) {
                        if (in_array($v->getId(), $cat->items)) {
                            $pages[$k]['cats'][] = [
                                'label' => $cat->label,
                                'url' => $this->router->generate('wiki-category', ['name' => util::strToUrl($cat->label), 'id' => $cat->id]),
                            ];
                        }
                    }
                    $pages[$k]['content'] = $v->getContent();
                    $pages[$k]['intro'] = $v->getIntro();
                    $pages[$k]['url'] = $this->runPlugin->getPublicUrl() . $v->getSlug() . '-' . $v->getId() . '.html';
                    $pages[$k]['img'] = $v->getImg();
                    $pages[$k]['imgUrl'] = $v->getImgUrl();

                }
                $i++;
            }
        }
        $nbPages = $i - 1;
        $mode = ($nbPages > 0) ? 'list' : 'list_empty';
        if ($mode === 'list') {
            $nbPages = ceil($nbPages / $pagesByPage);
            if ($currentPage > $nbPages) {
                return $this->category($name,$id, 1);
            }
            if ($nbPages > 1) {
                $pagination = [];
                for ($i = 0; $i != $nbPages; $i++) {
                    if ($i != 0)
                        $pagination[$i]['url'] = $this->router->generate('wiki-category-page', ['name' => util::strToUrl($category->label), 'id' => $category->id, 'page' => $i + 1]);
                    else
                        $pagination[$i]['url'] = $this->router->generate('wiki-category', ['name' => util::strToUrl($category->label), 'id' => $category->id]);
                    $pagination[$i]['num'] = $i + 1;
                }
            } else {
                $pagination = false;
            }
        } else {
            $pagination = false;
        }



        // Traitements divers : métas, fil d'ariane...
        $this->runPlugin->setMainTitle('Pages de la catégorie ' . $category->label);
        $this->runPlugin->setTitleTag($this->pluginsManager->getPlugin('wiki')->getConfigVal('label') . ' : page ' . $currentPage);
        if ($this->runPlugin->getIsDefaultPlugin() && $currentPage == 1) {
            $this->runPlugin->setTitleTag($this->pluginsManager->getPlugin('wiki')->getConfigVal('label'));
            $this->runPlugin->setMetaDescriptionTag($this->core->getConfigVal('siteDescription'));
        }

        $response = new PublicResponse();
        $tpl = $response->createPluginTemplate('wiki', 'list');

        $tpl->set('pages', $pages);
        $tpl->set('wikiPageManager', $wikiPageManager);
        $tpl->set('pagination', $pagination);
        $tpl->set('mode', $mode);
        $response->addTemplate($tpl);
        return $response;
    }

    public function page(int $page)
    {
        $page = $page > 1 ? $page : 1;
        return $this->home($page);
    }

    public function categoryPage(int $id, string $name, int $page)
    {
        $page = $page > 1 ? $page : 1;
        return $this->category($name,$id, $page);
    }

    /**
     * Générer la table des matières du wiki avec flèches interactives
     */
    private function generateWikiTOC($categoriesManager) {
        $nestedCategories = $categoriesManager->getNestedCategories();
        
        // Vérifier s'il y a du contenu dans la TOC
        $hasContent = false;
        foreach ($nestedCategories as $category) {
            $hasChildren = !empty($category->items) || ($category->hasChildren && !empty($category->children));
            if ($hasChildren) {
                $hasContent = true;
                break;
            }
        }
        
        // Si pas de contenu, retourner une chaîne vide
        if (!$hasContent) {
            return '';
        }
        
        // Générer directement la TOC sans passer par util::generateTableOfContents
        $toc = '<details class="toc-container">';
        $toc .= '<summary><header><h4>Structure du wiki</h4></header>';
        $toc .= '<ol class="toc-level-1">';
        
        foreach ($nestedCategories as $category) {
            $hasChildren = !empty($category->items) || ($category->hasChildren && !empty($category->children));
            
            if ($hasChildren) {
                $toc .= '<li class="has-children">';
                $toc .= '<a href="' . $this->router->generate('wiki-category', ['name' => util::strToUrl($category->label), 'id' => $category->id]) . '">' . htmlspecialchars($category->label) . '</a>';
                $toc .= '<ol class="toc-level-2">';
                
                // Ajouter les pages de cette catégorie
                if (!empty($category->items)) {
                    $toc .= '<li class="has-children">';
                    $toc .= '<a href="#pages-' . $category->id . '">Pages</a>';
                    $toc .= '<ol class="toc-level-3">';
                    foreach ($category->items as $pageId) {
                        $wikiPageManager = new WikiPageManager();
                        $page = $wikiPageManager->create($pageId);
                        if ($page && !$page->getDraft()) {
                            $toc .= '<li><a href="' . $this->runPlugin->getPublicUrl() . $page->getSlug() . '-' . $page->getId() . '.html">' . htmlspecialchars($page->getName()) . '</a></li>';
                        }
                    }
                    $toc .= '</ol></li>';
                }
                
                // Ajouter les sous-catégories
                if ($category->hasChildren && !empty($category->children)) {
                    $toc .= '<li class="has-children">';
                    $toc .= '<a href="#subcategories-' . $category->id . '">Sous-catégories</a>';
                    $toc .= '<ol class="toc-level-3">';
                    foreach ($category->children as $child) {
                        $childHasPages = !empty($child->items);
                        
                        if ($childHasPages) {
                            $toc .= '<li class="has-children">';
                            $toc .= '<a href="' . $this->router->generate('wiki-category', ['name' => util::strToUrl($child->label), 'id' => $child->id]) . '">' . htmlspecialchars($child->label) . '</a>';
                            $toc .= '<ol class="toc-level-4">';
                            foreach ($child->items as $pageId) {
                                $wikiPageManager = new WikiPageManager();
                                $page = $wikiPageManager->create($pageId);
                                if ($page && !$page->getDraft()) {
                                    $toc .= '<li><a href="' . $this->runPlugin->getPublicUrl() . $page->getSlug() . '-' . $page->getId() . '.html">' . htmlspecialchars($page->getName()) . '</a></li>';
                                }
                            }
                            $toc .= '</ol></li>';
                        } else {
                            $toc .= '<li><a href="' . $this->router->generate('wiki-category', ['name' => util::strToUrl($child->label), 'id' => $child->id]) . '">' . htmlspecialchars($child->label) . '</a></li>';
                        }
                    }
                    $toc .= '</ol></li>';
                }
                
                $toc .= '</ol></li>';
            } else {
                $toc .= '<li><a href="' . $this->router->generate('wiki-category', ['name' => util::strToUrl($category->label), 'id' => $category->id]) . '">' . htmlspecialchars($category->label) . '</a></li>';
            }
        }
        
        $toc .= '</ol></summary></details>';
        
        return $toc;
    }
}