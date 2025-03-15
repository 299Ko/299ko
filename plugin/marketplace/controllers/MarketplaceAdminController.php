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
 * MarketplaceAdminController
 *
 * Ce contrôleur gère l'interface d'administration de la marketplace.
 * Il récupère les releases depuis GitHub avec mise en cache et authentification,
 * propose une recherche/filtrage, affiche des infos complémentaires et permet
 * l'installation automatique. Il intègre désormais une vraie solution de like
 * et une vérification périodique des mises à jour.
 */

defined('ROOT') or exit('Access denied!');

class MarketplaceAdminController extends AdminController
{

    protected $cacheDir = DATA_PLUGIN . 'marketplace' . DS . 'cache' . DS;

    protected $likesFile = DATA_PLUGIN . 'marketplace' . DS . 'likes.json';

    /**
     * Affiche la page marketplace en admin.
     *
     * @return AdminResponse
     */
    public function index()
    {

        // Chargement de la configuration du plugin
        $config = $this->runPlugin->getConfig();
        $repos = [
            'plugins' => $config['githubPluginsRepo'],
            'themes' => $config['githubThemesRepo']
        ];

        // Récupération du filtre de recherche et de la pagination depuis GET
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 5;

        // Lecture du fichier de likes
        $likesData = util::readJsonFile($this->likesFile);

        // Récupération des releases et ajout des likes et infos complémentaires
        $data = [];
        foreach ($repos as $type => $url) {
            $releases = $this->fetchGithubData($url, $config);
            if (!$releases) {
                $releases = [];
            }

            // Filtrage sur le terme de recherche uniquement
            if ($search !== '') {
                $releases = array_filter($releases, function ($release) use ($search) {
                    return (stripos($release['name'], $search) !== false) ||
                        (stripos($release['body'], $search) !== false);
                });
            }

            // Tri par date décroissante (pour afficher les plus récents en premier)
            usort($releases, function ($a, $b) {
                return strtotime($b['published_at']) - strtotime($a['published_at']);
            });

            // Calcul de la pagination
            $total = count($releases);
            $totalPages = ceil($total / $perPage);
            $pagination[$type] = [
                'current' => $page,
                'total' => $totalPages
            ];
            $offset = ($page - 1) * $perPage;
            $releases = array_slice($releases, $offset, $perPage);

            // Ajout des infos complémentaires et likes
            foreach ($releases as &$release) {
                $release['version'] = isset($release['tag_name']) ? $release['tag_name'] : '';
                $release['published_at'] = isset($release['published_at']) ? $release['published_at'] : '';
                $downloadCount = 0;
                if (isset($release['assets']) && is_array($release['assets'])) {
                    foreach ($release['assets'] as $asset) {
                        $downloadCount += isset($asset['download_count']) ? (int) $asset['download_count'] : 0;
                    }
                }
                $release['download_count'] = $downloadCount;
                $releaseId = $release['id'];
                $release['likes'] = (int) $likesData[$type][$releaseId] ?? 0;
            }
            $data[$type] = $releases;
        }

        // Vérification des mises à jour installées
        $updateAlerts = $this->checkForUpdates($config);

        // Création de la réponse
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('marketplace', 'admin-marketplace');
        $tpl->set('reposData', $data);
        $tpl->set('searchQuery', $search);
        // Suppression de dateFrom et dateTo
        $tpl->set('pagination', $pagination);
        $tpl->set('updateAlerts', $updateAlerts);
        $tpl->set('token', $this->user->token);
        $response->addTemplate($tpl);
        return $response;
    }

    /**
     * Récupère les données depuis l'API GitHub avec mise en cache et authentification.
     *
     * @param string $url URL de l'API GitHub.
     * @param array $config Configuration du plugin.
     * @return array|null
     */
    protected function fetchGithubData($url, $config)
    {
        // Créer le répertoire de cache s'il n'existe pas
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
        // Nom du fichier cache basé sur l'URL
        $cacheFile = $this->cacheDir . md5($url) . '.json';
        $cacheDuration = isset($config['cacheDuration']) ? (int) $config['cacheDuration'] : 3600;

        // Si le cache existe et n'est pas expiré, l'utiliser
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheDuration) {
            $cachedData = file_get_contents($cacheFile);
            $data = json_decode($cachedData, true);
            if ($data !== null) {
                return $data;
            }
        }

        // Initialisation de cURL avec options sécurisées
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $headers = ['User-Agent: 299KoMarketplace'];
        // Ajout d'un token GitHub si défini pour l'authentification
        if (!empty($config['githubToken'])) {
            $headers[] = 'Authorization: token ' . $config['githubToken'];
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Exécution de la requête
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch) || $httpCode !== 200) {
            curl_close($ch);
            return null;
        }
        curl_close($ch);

        // Décodage du JSON et sauvegarde dans le cache
        $data = json_decode($response, true);
        if ($data !== null) {
            file_put_contents($cacheFile, $response);
        }
        return $data;
    }

    /**
     * Installation automatique d'un release.
     * Télécharge, extrait et installe le plugin ou thème dans le répertoire cible.
     *
     * @param string $type "plugins" ou "themes"
     * @param string $zipUrl URL de l'archive zip.
     * @return ApiResponse
     */
    public function installRelease($type, $zipUrl)
    {
        $response = new ApiResponse();
        if (!in_array($type, ['plugins', 'themes'])) {
            $response->status = ApiResponse::STATUS_BAD_REQUEST;
            $response->body = "Type invalide.";
            return $response;
        }

        // Télécharger le fichier zip
        $tempFile = tempnam(sys_get_temp_dir(), 'mk_') . '.zip';
        $fileData = file_get_contents($zipUrl);
        if ($fileData === false) {
            $response->status = ApiResponse::STATUS_BAD_REQUEST;
            $response->body = "Erreur lors du téléchargement.";
            return $response;
        }
        file_put_contents($tempFile, $fileData);

        // Extraction de l'archive dans un dossier temporaire
        $zip = new ZipArchive();
        if ($zip->open($tempFile) === TRUE) {
            $extractDir = sys_get_temp_dir() . '/mk_extract_' . uniqid();
            mkdir($extractDir, 0755, true);
            $zip->extractTo($extractDir);
            $zip->close();
        } else {
            unlink($tempFile);
            $response->status = ApiResponse::STATUS_BAD_REQUEST;
            $response->body = "Erreur lors de l'extraction de l'archive.";
            return $response;
        }
        unlink($tempFile);

        // Détermination du répertoire d'installation
        if ($type === 'plugins') {
            $installDir = PLUGINS;
        } else {
            $installDir = ROOT . 'theme/';
        }

        // Copier le contenu extrait dans le répertoire d'installation
        $files = scandir($extractDir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..')
                continue;
            $source = $extractDir . '/' . $file;
            $destination = $installDir . $file;
            $this->recurseCopy($source, $destination);
        }
        util::deleteDir($extractDir);

        $response->status = ApiResponse::STATUS_ACCEPTED;
        $response->body = "Installation réussie.";
        return $response;
    }

    /**
     * Copie récursive d'un dossier.
     *
     * @param string $src Source.
     * @param string $dst Destination.
     */
    protected function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);
        while (false !== ($file = readdir($dir))) {
            if (($file !== '.') && ($file !== '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Vérifie les mises à jour pour les plugins ou thèmes installés.
     * Compare la version locale (infos.json) avec la dernière release sur GitHub.
     *
     * @param array $config Configuration du plugin.
     * @return array Tableau d'alertes.
     */
    protected function checkForUpdates($config)
    {
        $alerts = [];
        $installedPluginsDir = PLUGINS;
        $plugins = glob($installedPluginsDir . '*', GLOB_ONLYDIR);
        foreach ($plugins as $pluginDir) {
            $infosFile = $pluginDir . '/param/infos.json';
            if (file_exists($infosFile)) {
                $infos = json_decode(file_get_contents($infosFile), true);
                if (!isset($infos['version']))
                    continue;
                $currentVersion = $infos['version'];
                // Construire l'URL de la dernière release sur GitHub pour ce plugin
                $repo = 'https://api.github.com/repos/moncompte/' . strtolower($infos['name']) . '/releases/latest';
                $latestRelease = $this->fetchGithubData($repo, $config);
                if ($latestRelease && isset($latestRelease['tag_name'])) {
                    $latestVersion = $latestRelease['tag_name'];
                    if (version_compare($latestVersion, $currentVersion, '>')) {
                        $alerts[] = [
                            'name' => $infos['name'],
                            'currentVersion' => $currentVersion,
                            'latestVersion' => $latestVersion,
                            'url' => $latestRelease['html_url']
                        ];
                    }
                }
            }
        }
        return $alerts;
    }

    /**
     * Traite la requête AJAX pour ajouter un like à une release.
     *
     * Expects JSON POST with keys: type et releaseId.
     *
     * @return ApiResponse
     */
    public function likeRelease()
    {
        $response = new ApiResponse();
        $data = json_decode(file_get_contents("php://input"), true);
        $type = isset($data['type']) ? $data['type'] : '';
        $releaseId = isset($data['releaseId']) ? $data['releaseId'] : '';

        if (!in_array($type, ['plugins', 'themes']) || empty($releaseId)) {
            $response->status = ApiResponse::STATUS_BAD_REQUEST;
            $response->body = "Paramètres invalides.";
            return $response;
        }

        $likesData = util::readJsonFile($this->likesFile);

        if (!isset($likesData[$type][$releaseId])) {
            $likesData[$type][$releaseId] = 0;
        }
        $likesData[$type][$releaseId]++;

        util::writeJsonFile($this->likesFile, $likesData);

        $response->status = ApiResponse::STATUS_ACCEPTED;
        $response->body = json_encode(["likes" => $likesData[$type][$releaseId]]);
        return $response;
    }

    /**
     * Liste les plugins et thèmes disponibles dans la marketplace.
     *
     * @return AdminResponse
     */
    public function officialZone()
    {
        // Chargement de la configuration du plugin
        $configFile = PLUGINS . 'marketplace/param/config.json';
        $config = json_decode(file_get_contents($configFile), true);

        // Récupération des paramètres de recherche et de pagination depuis GET
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 5;

        // Lecture du fichier de likes
        $likesData = util::readJsonFile($this->likesFile);

        /* ------------------- Traitement des Plugins ------------------- */

        $repoPlugins = $config['githubPluginsRepo'];
        $pluginsReleases = $this->fetchGithubData($repoPlugins, $config);
        if (!$pluginsReleases) {
            $pluginsReleases = [];
        }
        // Filtrage sur le terme de recherche si présent
        if ($search !== '') {
            $pluginsReleases = array_filter($pluginsReleases, function ($release) use ($search) {
                return (stripos($release['name'], $search) !== false) ||
                    (stripos($release['body'], $search) !== false);
            });
        }
        // Tri par date décroissante
        usort($pluginsReleases, function ($a, $b) {
            return strtotime($b['published_at']) - strtotime($a['published_at']);
        });
        // Pagination
        $totalPlugins = count($pluginsReleases);
        $totalPagesPlugins = ceil($totalPlugins / $perPage);
        $offsetPlugins = ($page - 1) * $perPage;
        $pluginsReleases = array_slice($pluginsReleases, $offsetPlugins, $perPage);
        // Ajout des infos complémentaires et des likes
        foreach ($pluginsReleases as &$release) {
            $release['version'] = isset($release['tag_name']) ? $release['tag_name'] : '';
            $release['published_at'] = isset($release['published_at']) ? $release['published_at'] : '';
            $downloadCount = 0;
            if (isset($release['assets']) && is_array($release['assets'])) {
                foreach ($release['assets'] as $asset) {
                    $downloadCount += isset($asset['download_count']) ? (int) $asset['download_count'] : 0;
                }
            }
            $release['download_count'] = $downloadCount;
            $releaseId = $release['id'];
            $release['likes'] = (int) $likesData['plugins'][$releaseId] ?? 0;
        }

        /* ------------------- Traitement des Thèmes ------------------- */

        $repoThemes = $config['githubThemesRepo'];
        $themesReleases = $this->fetchGithubData($repoThemes, $config);
        if (!$themesReleases) {
            $themesReleases = [];
        }
        // Filtrage sur le terme de recherche si présent
        if ($search !== '') {
            $themesReleases = array_filter($themesReleases, function ($release) use ($search) {
                return (stripos($release['name'], $search) !== false) ||
                    (stripos($release['body'], $search) !== false);
            });
        }
        // Tri par date décroissante
        usort($themesReleases, function ($a, $b) {
            return strtotime($b['published_at']) - strtotime($a['published_at']);
        });
        // Pagination
        $totalThemes = count($themesReleases);
        $totalPagesThemes = ceil($totalThemes / $perPage);
        $offsetThemes = ($page - 1) * $perPage;
        $themesReleases = array_slice($themesReleases, $offsetThemes, $perPage);
        // Ajout des infos complémentaires et des likes
        foreach ($themesReleases as &$release) {
            $release['version'] = isset($release['tag_name']) ? $release['tag_name'] : '';
            $release['published_at'] = isset($release['published_at']) ? $release['published_at'] : '';
            $downloadCount = 0;
            if (isset($release['assets']) && is_array($release['assets'])) {
                foreach ($release['assets'] as $asset) {
                    $downloadCount += isset($asset['download_count']) ? (int) $asset['download_count'] : 0;
                }
            }
            $release['download_count'] = $downloadCount;
            $releaseId = $release['id'];
            $release['likes'] = (int) $likesData['themes'][$releaseId] ?? 0;
        }

        /* ------------------- Création de la réponse officielle ------------------- */

        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('marketplace', 'admin-marketplace-official');
        $tpl->set('plugins', $pluginsReleases);
        $tpl->set('themes', $themesReleases);
        $tpl->set('searchQuery', $search);
        $tpl->set('paginationPlugins', [
            'current' => $page,
            'total' => $totalPagesPlugins
        ]);
        $tpl->set('paginationThemes', [
            'current' => $page,
            'total' => $totalPagesThemes
        ]);
        $tpl->set('token', $this->user->token);
        $response->addTemplate($tpl);

        return $response;
    }

    /**
     * Liste les plugins disponibles dans la marketplace.
     *
     * @return AdminResponse
     */
    public function listPlugins()
    {
        // Chargement de la configuration du plugin
        $configFile = PLUGINS . 'marketplace/param/config.json';
        $config = json_decode(file_get_contents($configFile), true);
        $repo = $config['githubPluginsRepo'];

        // Récupération des paramètres de recherche et de pagination depuis GET
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 5;

        // Lecture du fichier de likes
        $likesData = util::readJsonFile($this->likesFile);

        // Récupération des releases pour les plugins
        $releases = $this->fetchGithubData($repo, $config);
        if (!$releases) {
            $releases = [];
        }

        // Filtrage sur le terme de recherche si présent
        if ($search !== '') {
            $releases = array_filter($releases, function ($release) use ($search) {
                return (stripos($release['name'], $search) !== false) ||
                    (stripos($release['body'], $search) !== false);
            });
        }

        // Tri par date décroissante
        usort($releases, function ($a, $b) {
            return strtotime($b['published_at']) - strtotime($a['published_at']);
        });

        // Pagination
        $total = count($releases);
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $releases = array_slice($releases, $offset, $perPage);

        // Ajout des infos complémentaires et des likes
        foreach ($releases as &$release) {
            $release['version'] = isset($release['tag_name']) ? $release['tag_name'] : '';
            $release['published_at'] = isset($release['published_at']) ? $release['published_at'] : '';
            $downloadCount = 0;
            if (isset($release['assets']) && is_array($release['assets'])) {
                foreach ($release['assets'] as $asset) {
                    $downloadCount += isset($asset['download_count']) ? (int) $asset['download_count'] : 0;
                }
            }
            $release['download_count'] = $downloadCount;
            $releaseId = $release['id'];
            $release['likes'] = (int) $likesData['plugins'][$releaseId] ?? 0;
        }

        // Création et envoi de la réponse
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('marketplace', 'admin-marketplace-plugins');
        $tpl->set('releases', $releases);
        $tpl->set('searchQuery', $search);
        $tpl->set('pagination', [
            'current' => $page,
            'total' => $totalPages
        ]);
        $tpl->set('token', $this->user->token);
        $response->addTemplate($tpl);
        return $response;
    }

    /**
     * Liste les thèmes disponibles dans la marketplace.
     *
     * @return AdminResponse
     */
    public function listThemes()
    {
        // Chargement de la configuration du plugin
        $configFile = PLUGINS . 'marketplace/param/config.json';
        $config = json_decode(file_get_contents($configFile), true);
        $repo = $config['githubThemesRepo'];

        // Récupération des paramètres de recherche et de pagination depuis GET
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 5;

        // Lecture du fichier de likes
        $likesData = util::readJsonFile($this->likesFile);

        // Récupération des releases pour les thèmes
        $releases = $this->fetchGithubData($repo, $config);
        if (!$releases) {
            $releases = [];
        }

        // Filtrage sur le terme de recherche si présent
        if ($search !== '') {
            $releases = array_filter($releases, function ($release) use ($search) {
                return (stripos($release['name'], $search) !== false) ||
                    (stripos($release['body'], $search) !== false);
            });
        }

        // Tri par date décroissante
        usort($releases, function ($a, $b) {
            return strtotime($b['published_at']) - strtotime($a['published_at']);
        });

        // Pagination
        $total = count($releases);
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $releases = array_slice($releases, $offset, $perPage);

        // Ajout des infos complémentaires et des likes
        foreach ($releases as &$release) {
            $release['version'] = isset($release['tag_name']) ? $release['tag_name'] : '';
            $release['published_at'] = isset($release['published_at']) ? $release['published_at'] : '';
            $downloadCount = 0;
            if (isset($release['assets']) && is_array($release['assets'])) {
                foreach ($release['assets'] as $asset) {
                    $downloadCount += isset($asset['download_count']) ? (int) $asset['download_count'] : 0;
                }
            }
            $release['download_count'] = $downloadCount;
            $releaseId = $release['id'];
            $release['likes'] = (int) $likesData['themes'][$releaseId] ?? 0;
        }

        // Création et envoi de la réponse
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('marketplace', 'admin-marketplace-themes');
        $tpl->set('releases', $releases);
        $tpl->set('searchQuery', $search);
        $tpl->set('pagination', [
            'current' => $page,
            'total' => $totalPages
        ]);
        $tpl->set('token', $this->user->token);
        $response->addTemplate($tpl);
        return $response;
    }

}
