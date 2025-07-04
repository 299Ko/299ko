<?php

/**
 * @copyright (C) 2023, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Maxime Blanc <maximeblanc@flexcb.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('Access denied!');

/**
 * Catégorie Wiki
 * 
 * Classe représentant une catégorie du plugin Wiki
 */
class WikiCategory extends Category {

    protected string $pluginId = 'wiki';
    protected string $name = 'categories';
    protected bool $nested = true;
    protected bool $chooseMany = true;

    /**
     * Générer l'affichage de la catégorie dans une liste
     * 
     * @return string HTML de la catégorie
     */
    public function outputAsList() {
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('wiki', 'wiki-categories-list');
        
        $tpl->set('catDisplay', 'sub');
        $tpl->set('this', $this);
        
        return $tpl->output();
    }

    /**
     * Générer l'affichage de la catégorie dans un sélecteur de parent
     * 
     * @param int $selectedParentId ID du parent sélectionné
     * @return string HTML de la catégorie
     */
    public function outputAsParentSelect($selectedParentId) {
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('wiki', 'selectParentCategory');
        
        $tpl->set('catDisplay', 'sub');
        $tpl->set('this', $this);
        $tpl->set('selectedParentId', $selectedParentId);
        
        return $tpl->output();
    }

    /**
     * Calculer le nombre total d'éléments d'une catégorie incluant tous les enfants
     * 
     * @return int Nombre total d'éléments
     */
    public function getTotalItemsCountRecursive() {
        $total = count($this->items);
        
        if ($this->hasChildren) {
            foreach ($this->children as $child) {
                $total += $child->getTotalItemsCountRecursive();
            }
        }
        
        return $total;
    }

}