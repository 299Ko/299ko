<?php
/**
 * @copyright (C) 2025, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxime Blanc <nemstudio18@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 *
 * @package 299Ko https://github.com/299Ko/299ko
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */

/**
 * AdminMarketplaceController.php
 *
 * FR: Ce contrôleur gère la page d'accueil de la marketplace.
 *     Il affiche 5 plugins et 5 thèmes sélectionnés aléatoirement et fournit
 *     des liens vers les pages complètes des plugins et des thèmes.
 *
 * EN: This controller handles the marketplace homepage.
 *     It displays 5 randomly selected plugins and 5 themes and provides links
 *     to the full plugins and themes pages.
 */

defined('ROOT') or exit('Access denied!');

class AdminMarketplaceController extends AdminController
{
    // Chemins vers les fichiers de cache pour les plugins et les thèmes
    // Paths to the cache files for plugins and themes
    protected $pluginsCacheFile = DATA_PLUGIN . 'marketplace' . DS . 'cache' . DS . 'plugins.json';
    protected $themesCacheFile = DATA_PLUGIN . 'marketplace' . DS . 'cache' . DS . 'themes.json';

    /**
     * index
     *
     * FR: Affiche la page d'accueil de la marketplace.
     *     Charge les caches de plugins et thèmes, sélectionne aléatoirement 5 éléments
     *     de chaque et les transmet à la vue.
     *
     * EN: Displays the marketplace homepage.
     *     Loads the plugins and themes caches, randomly selects 5 items from each,
     *     and passes them to the view.
     *
     * @return AdminResponse La réponse à afficher.
     */
    public function index()
    {
        // Si le cache des plugins n'existe pas ou est périmé, on peut forcer la mise à jour
        if (!file_exists($this->pluginsCacheFile)) {
            // On instancie le contrôleur plugins et on met à jour le cache
            $pluginsController = new PluginsMarketController();
            $pluginsController->updatePluginsJson();
        }
        // Idem pour le cache des thèmes
        if (!file_exists($this->themesCacheFile)) {
            $themesController = new ThemesMarketController();
            $themesController->updateThemesJson();
        }

        // Charge les données du cache plugins
        $pluginsData = util::readJsonFile($this->pluginsCacheFile);
        //var_dump($pluginsData);
        $plugins = isset($pluginsData['plugins']) ? $pluginsData['plugins'] : [];
        // Sélection aléatoire de 5 plugins
        shuffle($plugins);
        $randomPlugins = array_slice($plugins, 0, 5);

        // Charge les données du cache thèmes
        $themesData = util::readJsonFile($this->themesCacheFile);
        $themes = isset($themesData['themes']) ? $themesData['themes'] : [];
        // Sélection aléatoire de 5 thèmes
        shuffle($themes);
        $randomThemes = array_slice($themes, 0, 5);

        // Prépare la réponse admin et passe les listes et liens à la vue
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('marketplace', 'admin-marketplace');
        $tpl->set('router', ROUTER::getInstance());
        $tpl->set('randomPlugins', $randomPlugins);
        $tpl->set('randomThemes', $randomThemes);
        // Les liens vers la page complète des plugins et des thèmes
        $tpl->set('pluginsPageUrl', ROUTER::getInstance()->generate('marketplace-plugins'));
        $tpl->set('themesPageUrl', ROUTER::getInstance()->generate('marketplace-themes'));
        $response->addTemplate($tpl);
        return $response;
    }
}


