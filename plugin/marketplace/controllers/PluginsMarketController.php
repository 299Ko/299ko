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
 * PluginsMarketController.php
 *
 * FR: Ce contrôleur gère la page d'administration des plugins.
 *     Il met à jour le cache des plugins depuis GitHub et permet l'installation/mise à jour.
 *
 * EN: This controller handles the admin page for plugins.
 *     It updates the plugins cache from GitHub and allows installation/updating.
 */

defined('ROOT') or exit('Access denied!');

class PluginsMarketController extends AdminController
{
    // Dossier de cache et fichier JSON pour les plugins
    // Cache directory and JSON cache file for plugins
    protected $cacheDir = DATA_PLUGIN . 'marketplace' . DS . 'cache' . DS;
    protected $pluginsCacheFile = DATA_PLUGIN . 'marketplace' . DS . 'cache' . DS . 'plugins.json';

    /**
     * index
     *
     * FR: Affiche la liste complète des plugins avec leur état (installé, mise à jour nécessaire).
     * EN: Displays the full list of plugins along with their status (installed, update needed).
     *
     * @return AdminResponse La réponse à afficher.
     */
    public function index()
    {
        // Met à jour le cache des plugins depuis GitHub
        $this->updatePluginsJson();

        // Charge les données du cache et les transforme en tableau
        $data = util::readJsonFile($this->pluginsCacheFile);
        if (!is_array($data)) {
            die("Erreur lors de la lecture du fichier plugins.json.");
        }
        $plugins = $data['plugins'] ?? [];

        // Pour chaque plugin, vérifie s'il est installé et s'il nécessite une mise à jour
        foreach ($plugins as &$plugin) {
            $pluginDir = PLUGINS . $plugin['directory'] . DS;
            $plugin['is_installed'] = is_dir($pluginDir);
            if ($plugin['is_installed']) {
                // Lecture du SHA local stocké dans commit.sha dans le dossier du plugin
                $commitFile = $pluginDir . 'commit.sha';
                $localSHA = file_exists($commitFile) ? trim(file_get_contents($commitFile)) : '';
                $plugin['local_sha'] = $localSHA;
                // Comparaison entre le SHA local et le SHA distant
                $plugin['update_needed'] = ($localSHA !== $plugin['CommitGithubSHA']);
            } else {
                $plugin['update_needed'] = false;
            }
        }
        unset($plugin);

        // Prépare la réponse en utilisant la vue admin-marketplace-plugins.tpl
        $response = new AdminResponse();
        $tpl = $response->createPluginTemplate('marketplace', 'admin-marketplace-plugins');
        $tpl->set('router', ROUTER::getInstance());
        $tpl->set('pluginsList', $plugins);
        $response->addTemplate($tpl);
        return $response;
    }

    /**
     * installRelease
     *
     * FR: Télécharge et installe (ou met à jour) un plugin.
     * EN: Downloads and installs (or updates) a plugin.
     */
    public function installRelease()
    {
        $type = isset($_GET['type']) ? $_GET['type'] : 'plugin';
        if (!isset($_GET['folder'])) {
            die("Aucun dossier spécifié.");
        }
        $folder = $_GET['folder'];
        $pluginName = basename($folder);

        $config = $this->runPlugin->getConfig();
        if (!$config) {
            die("Erreur lors de la lecture du fichier config.json.");
        }
        if (!isset($config['repos'][$type])) {
            die("Type inconnu.");
        }
        $repo = $config['repos'][$type];

        $tempDir = sys_get_temp_dir() . '/' . uniqid('gh_');
        if (!mkdir($tempDir, 0777, true)) {
            die("Erreur lors de la création du dossier temporaire.");
        }

        if (!$this->downloadFolderFromGitHub($repo, $folder, $tempDir, $config['github_token'])) {
            die("Erreur lors du téléchargement des fichiers.");
        }

        $zipFile = __DIR__ . '/' . $pluginName . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            die("Impossible de créer le fichier zip.");
        }
        $this->addFolderToZip($tempDir, $zip);
        $zip->close();

        $extractDir = sys_get_temp_dir() . '/mk_extract_' . uniqid();
        if (!mkdir($extractDir, 0755, true)) {
            die("Erreur lors de la création du dossier d'extraction.");
        }
        $zip = new ZipArchive();
        if ($zip->open($zipFile) === true) {
            $zip->extractTo($extractDir);
            $zip->close();
        } else {
            unlink($zipFile);
            die("Erreur lors de l'extraction de l'archive.");
        }
        unlink($zipFile);

        if ($type === 'plugin') {
            $installDir = PLUGINS;
        } else {
            $installDir = ROOT . 'theme' . DS;
        }

        $sourcePluginDir = $extractDir . '/' . $pluginName;
        $destinationPluginDir = $installDir . $pluginName;
        if (!is_dir($sourcePluginDir)) {
            $sourcePluginDir = $extractDir;
            $destinationPluginDir = $installDir . $pluginName;
        }
        $this->recurseCopy($sourcePluginDir, $destinationPluginDir);

        $commitSHA = isset($_GET['commit']) ? $_GET['commit'] : '';
        if (!empty($commitSHA)) {
            file_put_contents($destinationPluginDir . '/commit.sha', $commitSHA);
        }

        $this->deleteDirectory($extractDir);
        $this->deleteDirectory($tempDir);

        header("Location: " . $this->router->generate('marketplace-plugins'));
        exit;
    }

    /**
     * updatePluginsJson
     *
     * FR: Met à jour le cache des plugins en récupérant pour chaque plugin
     *     les informations (infos.json) et le SHA du dernier commit (CommitGithubSHA) depuis GitHub.
     * EN: Updates the plugins cache by fetching for each plugin the info (infos.json)
     *     and the SHA of the last commit that modified the folder (CommitGithubSHA) from GitHub.
     */
    public function updatePluginsJson()
    {
        $config = $this->runPlugin->getConfig();
        if (!$config) {
            die("Erreur lors de la lecture du fichier config.json.");
        }
        $cacheDuration = isset($config['cacheDuration']) ? (int)$config['cacheDuration'] : 3600;
        if (file_exists($this->pluginsCacheFile)) {
            $data = json_decode(file_get_contents($this->pluginsCacheFile), true);
            if (isset($data['last_updated']) && (strtotime($data['last_updated']) > time() - $cacheDuration)) {
                return;
            }
        }
        $repo = $config['repos']['plugin'];
        $baseUrl = "https://api.github.com/repos/{$repo}/contents";

        list($response, $httpCode) = $this->callGitHubRequest($baseUrl, $config);
        if ($httpCode != 200) {
            die("Erreur API GitHub: HTTP $httpCode");
        }
        $rootContents = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Erreur de décodage JSON: " . json_last_error_msg());
        }

        $plugins = [];
        foreach ($rootContents as $item) {
            if ($item['type'] === 'dir') {
                $infosUrl = "{$baseUrl}/{$item['name']}/param/infos.json?ref=main";
                list($infosResponse, $infosHttpCode) = $this->callGitHubRequest($infosUrl, $config);
                if ($infosHttpCode == 200) {
                    $infosData = json_decode($infosResponse, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($infosData['content'])) {
                        $decodedContent = base64_decode($infosData['content']);
                        $infos = json_decode($decodedContent, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $infos['directory'] = $item['name'];
                            $infos['CommitGithubSHA'] = $this->getLastCommitSHA($item['name'], $config, $repo);
                            $infos['type'] = $infos['type'] ?? 'plugin';
                            $plugins[] = $infos;
                        }
                    }
                }
            }
        }

        $data = [
            'last_updated' => date('c'),
            'plugins' => $plugins
        ];

        file_put_contents($this->pluginsCacheFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    // Les méthodes communes callGitHubRequest, computePluginFolderHash, computeLocalPluginFolderHash,
    // recurseCopy, downloadFolderFromGitHub, addFolderToZip, deleteDirectory, getLastCommitSHA
    // restent inchangées et sont incluses ci-dessous avec leurs commentaires bilingues.

    private function callGitHubRequest($url, $config)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: token " . $config['github_token'],
            "Accept: application/vnd.github.v3+json",
            "User-Agent: PHP-List-System"
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            die("Erreur cURL: " . curl_error($ch));
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$response, $httpCode];
    }

    private function computePluginFolderHash($pluginName, $config, $repo)
    {
        $treeUrl = "https://api.github.com/repos/{$repo}/git/trees/main?recursive=1";
        list($treeResponse, $treeHttpCode) = $this->callGitHubRequest($treeUrl, $config);
        if ($treeHttpCode != 200) {
            die("Erreur API GitHub (arbre) : HTTP $treeHttpCode");
        }
        $treeData = json_decode($treeResponse, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Erreur de décodage JSON (arbre) : " . json_last_error_msg());
        }
        if (!isset($treeData['tree']) || !is_array($treeData['tree'])) {
            die("Structure inattendue de l'arbre GitHub.");
        }
        $hashElements = [];
        foreach ($treeData['tree'] as $element) {
            if ($element['type'] === 'blob' && strpos($element['path'], $pluginName . '/') === 0) {
                $hashElements[] = $element['path'] . ':' . $element['sha'];
            }
        }
        sort($hashElements);
        $combined = implode('', $hashElements);
        return hash('sha256', $combined);
    }

    private function computeLocalPluginFolderHash($pluginPath)
    {
        if (!is_dir($pluginPath)) {
            return '';
        }
        $pluginName = basename($pluginPath);
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($pluginPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        $hashes = [];
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = $pluginName . '/' . $iterator->getSubPathName();
                $hashes[] = $relativePath . ':' . hash_file('sha256', $file->getPathname());
            }
        }
        sort($hashes);
        $combined = implode('', $hashes);
        return hash('sha256', $combined);
    }

    protected function recurseCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);
        while (false !== ($file = readdir($dir))) {
            if ($file !== '.' && $file !== '..') {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    private function downloadFolderFromGitHub($repo, $path, $localDir, $token)
    {
        $apiUrl = "https://api.github.com/repos/{$repo}/contents/{$path}?ref=main";
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: token {$token}",
            "Accept: application/vnd.github.v3+json",
            "User-Agent: PHP-Download-System"
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        $items = json_decode($response, true);
        if (!is_array($items)) {
            return false;
        }
        foreach ($items as $item) {
            $localPath = $localDir . '/' . $item['name'];
            if ($item['type'] === 'dir') {
                if (!mkdir($localPath, 0777, true)) {
                    return false;
                }
                if (!$this->downloadFolderFromGitHub($repo, $item['path'], $localPath, $token)) {
                    return false;
                }
            } elseif ($item['type'] === 'file') {
                $fileContent = file_get_contents($item['download_url']);
                if ($fileContent === false) {
                    return false;
                }
                file_put_contents($localPath, $fileContent);
            }
        }
        return true;
    }

    private function addFolderToZip($folder, $zip, $parentFolder = '')
    {
        $handle = opendir($folder);
        while (($file = readdir($handle)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $filePath = $folder . '/' . $file;
            $localPath = $parentFolder . $file;
            if (is_dir($filePath)) {
                $zip->addEmptyDir($localPath);
                $this->addFolderToZip($filePath, $zip, $localPath . '/');
            } else {
                $zip->addFile($filePath, $localPath);
            }
        }
        closedir($handle);
    }

    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $filePath = $dir . '/' . $file;
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }
        rmdir($dir);
    }

    private function getLastCommitSHA($pluginName, $config, $repo)
    {
        $commitsUrl = "https://api.github.com/repos/{$repo}/commits?path={$pluginName}&per_page=1";
        list($commitsResponse, $httpCode) = $this->callGitHubRequest($commitsUrl, $config);
        if ($httpCode != 200) {
            die("Erreur API GitHub (commits pour $pluginName) : HTTP $httpCode");
        }
        $commitsData = json_decode($commitsResponse, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($commitsData)) {
            return '';
        }
        return $commitsData[0]['sha'] ?? '';
    }
}
?>
