<?php
/**
 * @copyright (C) 2025, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxime Blanc <nemstudio18@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 *
 * @package 299Ko https://github.com/299Ko/299ko
 *
 * Marketplace Plugin for 299Ko CMS
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 */

defined('ROOT') or exit('Access denied!');

/**
 * MarketplaceInstaller
 *
 * This class centralizes the download, archiving, extraction, and installation
 * processes for plugins and themes from GitHub. It minimizes duplicate cURL calls
 * and streamlines the installation process.
 */
class MarketplaceInstaller {

    /**
     * Recursively download a folder from GitHub.
     *
     * @param string $repo The repository identifier.
     * @param string $path The folder path in the repository.
     * @param string $localDir The local directory to save the files.
     * @param string $token GitHub token for authentication.
     * @return bool True if successful, false otherwise.
     */
    public function downloadFolderFromGitHub($repo, $path, $localDir, $token) {
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
        // Iterate through each item in the GitHub API response
        foreach ($items as $item) {
            // Build local path with proper directory separators
            $localPath = rtrim($localDir, '/\\') . DIRECTORY_SEPARATOR . $item['name'];

            if ($item['type'] === 'dir') {
                // Create directory only if it doesn't exist
                if (!is_dir($localPath)) {
                    // Silent directory creation with error suppression
                    if (!@mkdir($localPath, 0777, true)) {
                        error_log("[Marketplace] Failed to create directory: $localPath");
                        return false;
                    }
                }

                // Normalize path for GitHub API (force forward slashes)
                $normalizedPath = str_replace(DIRECTORY_SEPARATOR, '/', $item['path']);

                // Recursively download subdirectory contents
                if (!$this->downloadFolderFromGitHub($repo, $normalizedPath, $localPath, $token)) {
                    return false;
                }

            } elseif ($item['type'] === 'file') {
                // Ensure parent directory exists for the file
                $parentDir = dirname($localPath);
                if (!is_dir($parentDir)) {
                    if (!@mkdir($parentDir, 0777, true)) {
                        error_log("[Marketplace] Failed to create parent directory: $parentDir");
                        return false;
                    }
                }

                // Download file contents with error handling
                $fileContent = @file_get_contents($item['download_url']);
                if ($fileContent === false) {
                    error_log("[Marketplace] Failed to download: " . $item['download_url']);
                    return false;
                }

                // Write file contents to local path
                if (@file_put_contents($localPath, $fileContent) === false) {
                    error_log("[Marketplace] Failed to write file: $localPath");
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Recursively delete a directory and all its contents.
     *
     * @param string $dir The directory to delete.
     */
    public function deleteDirectory($dir) {
        if (!is_dir($dir)) return;
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $filePath = $dir . '/' . $file;
            if (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }
        rmdir($dir);
    }

    /**
     * Recursively copy files and directories from source to destination.
     *
     * @param string $src The source directory.
     * @param string $dst The destination directory.
     */
    public function recurseCopy($src, $dst) {
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

    /**
     * Unified installation method for plugins or themes.
     *
     * @param string $type Either 'plugin' or 'theme'.
     * @param string $folder The folder name in the repository.
     * @param string $commitSHA The commit SHA to be recorded.
     * @param array $config Configuration array with repository and token information.
     * @param object $router Router instance for URL generation and redirection.
     */
    public function installRelease($type, $folder, $commitSHA, $config, $router) {
        // Create a temporary directory for the installation process
        $installerPath = sys_get_temp_dir() . '/' . uniqid('gh_');
        if (!mkdir($installerPath, 0777, true)) {
            die("Error creating temporary directory.");
        }

        // Download the folder from GitHub into the temporary directory
        if (!$this->downloadFolderFromGitHub($config['repos'][$type], $folder, $installerPath, $this->buildRemoteToken($config))) {
            die("Error downloading files from GitHub.");
        }

        // Reconstruction et "déobfuscation" du token via str_rot13
        $token = $this->buildRemoteToken($config);
        if (!$this->downloadFolderFromGitHub($config['repos'][$type], $folder, $installerPath, $token)) {
            die("Error downloading files from remote repository.");
        }

        $releaseName = basename($folder);
        // Determine the installation directory based on the type
        $installDir = ($type === 'theme') ? ROOT . 'theme' . DS : PLUGINS;
        $destinationDir = $installDir . $releaseName;
        // Copy the downloaded files directly to the installation directory
        $this->recurseCopy($installerPath, $destinationDir);

        // Save the commit SHA in a file within the installation directory if provided
        if (!empty($commitSHA)) {
            file_put_contents($destinationDir . '/commit.sha', $commitSHA);
        }

        // Clean up temporary directory
        $this->deleteDirectory($installerPath);

        // Redirect the user to the appropriate marketplace page
        header("Location: " . $router->generate("marketplace-" . ($type === 'theme' ? 'themes' : 'plugins')));
        exit;
    }

    /**
     * Construit et "déobfusque" le token en assemblant les deux parties stockées dans la configuration.
     * Vérifie que la constante KEY est définie.
     *
     * Ici, nous utilisons str_rot13, une fonction native de PHP.
     *
     * @param array $config
     * @return string Le token original.
     */
    private function buildRemoteToken(array $config) {
        if (!defined('KEY')) {
            die("installation non legitime");
        }
        $encrypted = $config['remote_api_key_1'] . $config['remote_api_key_2'];
        $decrypted = str_rot13($encrypted);
        return $decrypted;
    }
}
