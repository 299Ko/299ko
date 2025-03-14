<?php
/**
 * index.php
 *
 * Ce fichier génère un explorateur de fichiers et permet de créer un fichier Markdown
 * contenant l'arborescence du répertoire ainsi que le contenu des fichiers sélectionnés.
 *
 * Les modifications apportées visent à :
 * - Forcer l'encodage en UTF-8 pour éviter les problèmes d'affichage des caractères.
 * - Utiliser des caractères ASCII simples dans l'arborescence pour garantir un rendu lisible.
 */

// Forcer l'encodage UTF-8 en interne pour éviter les problèmes de caractères spéciaux.
// La fonction header permet de s'assurer que le navigateur interprète bien le contenu en UTF-8.
mb_internal_encoding("UTF-8");
header('Content-Type: text/html; charset=utf-8');

/**
 * Fonction qui scanne récursivement un répertoire pour récupérer
 * la liste de tous les fichiers contenus (y compris dans les sous‐répertoires),
 * en excluant les fichiers zip et db.
 *
 * @param string $dir Chemin du répertoire à scanner.
 * @param array  $result Référence vers le tableau qui contiendra les chemins des fichiers.
 * @return array La liste des fichiers trouvés.
 */
function scanDirectory($dir, &$result = []) {
    // Récupération du contenu du répertoire
    $files = scandir($dir);
    if ($files === false) {
        // En cas d'erreur, retourner le tableau actuel
        return $result;
    }
    foreach ($files as $value) {
        // Exclure les entrées spéciales et certains dossiers/fichiers indésirables
        if (in_array($value, ['.', '..', '.git', '.github', basename(__FILE__)])) {
            continue;
        }
        // Construction du chemin absolu
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if ($path === false) {
            continue;
        }
        // Si c'est un fichier, vérifier son extension avant de l'ajouter
        if (!is_dir($path)) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($ext === 'zip' || $ext === 'db') {
                continue;
            }
            $result[] = $path;
        } elseif (is_dir($path)) {
            // Pour un dossier, effectuer un scan récursif
            scanDirectory($path, $result);
            // Optionnel : ajouter aussi le dossier lui-même
            $result[] = $path;
        }
    }
    return $result;
}

/**
 * Fonction pour générer le contenu Markdown à partir de la liste des fichiers sélectionnés.
 * Le fichier Markdown généré contiendra d'abord la structure en schéma ASCII du répertoire,
 * puis le contenu de chaque fichier sélectionné.
 *
 * @param array  $selectedFiles Liste des chemins de fichiers sélectionnés.
 * @param string $rootDir Répertoire racine pour le calcul du chemin relatif.
 */
function generateMarkdownContent($selectedFiles, $rootDir) {
    // Génère l'arborescence ASCII du répertoire racine
    $asciiTree = generateAsciiTree($rootDir);
    $mdContent = "# Directory Structure\n\n" . $asciiTree . "\n\n# File Contents\n\n";

    // Parcourt chaque fichier sélectionné et ajoute son contenu dans le Markdown
    foreach ($selectedFiles as $file) {
        $filePath = realpath($file);
        if ($filePath !== false && file_exists($filePath) && is_file($filePath)) {
            // Obtention du chemin relatif pour un affichage plus clair
            $relativePath = str_replace($rootDir . DIRECTORY_SEPARATOR, '', $filePath);
            $mdContent .= "## " . $relativePath . "\n\n";
            $mdContent .= "```\n" . file_get_contents($filePath) . "\n```\n\n";
        }
    }

    // Enregistrement du contenu Markdown dans un fichier dans le répertoire racine
    file_put_contents($rootDir . DIRECTORY_SEPARATOR . 'directory_structure.md', $mdContent);
    echo "Markdown file generated successfully!";
    exit;
}

/**
 * Fonction pour générer une représentation ASCII en arborescence d'un répertoire.
 *
 * Pour garantir une compatibilité maximale et éviter les problèmes d'encodage,
 * nous utilisons ici des caractères ASCII simples :
 * - "`-- " pour le dernier élément
 * - "|-- " pour les autres éléments
 * - L'indentation est gérée avec des espaces et la barre verticale "|   " pour les niveaux intermédiaires.
 *
 * @param string $dir Répertoire à scanner.
 * @param string $prefix Préfixe pour la mise en forme de l'arborescence.
 * @return string L'arborescence sous forme de chaîne de caractères.
 */
function generateAsciiTree($dir, $prefix = '') {
    $result = '';
    $files = scandir($dir);
    if ($files === false) {
        return $result;
    }
    // Filtrage des entrées indésirables et exclusion des fichiers zip et db
    $files = array_filter($files, function($value) use ($dir) {
        if (in_array($value, ['.', '..', '.git', '.github', basename(__FILE__)])) {
            return false;
        }
        $fullPath = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if ($fullPath !== false && !is_dir($fullPath)) {
            $ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));
            if (in_array($ext, ['zip', 'db'])) {
                return false;
            }
        }
        return true;
    });
    $files = array_values($files);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if ($path === false) {
            continue;
        }
        // Détermination du connecteur à utiliser en fonction de la position dans le répertoire
        $connector = ($key == count($files) - 1) ? '`-- ' : '|-- ';
        // Construction de la ligne de l'arborescence
        $result .= $prefix . $connector . $value . "\n";
        if (is_dir($path)) {
            // Gestion de l'indentation pour le sous-dossier :
            // Si c'est le dernier élément, on ajoute 4 espaces, sinon on ajoute "|   "
            $newPrefix = ($key == count($files) - 1) ? $prefix . '    ' : $prefix . '|   ';
            $result .= generateAsciiTree($path, $newPrefix);
        }
    }
    return $result;
}

/**
 * Fonction qui génère l'arborescence HTML pour l'explorateur de fichiers.
 * Permet de sélectionner des fichiers et des dossiers.
 *
 * @param string $dir Répertoire à parcourir.
 * @param string $baseDir Répertoire racine pour le calcul du chemin relatif.
 * @return string Le code HTML généré.
 */
function renderFileExplorer($dir, $baseDir) {
    $html = "<ul>";
    $files = scandir($dir);
    if ($files === false) {
        return '';
    }
    foreach ($files as $file) {
        // Exclure les entrées non désirées
        if (in_array($file, ['.', '..', '.git', '.github', basename(__FILE__)])) {
            continue;
        }
        $path = realpath($dir . DIRECTORY_SEPARATOR . $file);
        if ($path === false) {
            continue;
        }
        // Pour les fichiers, exclure ceux avec extension zip ou db
        if (!is_dir($path)) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($ext === 'zip' || $ext === 'db') {
                continue;
            }
        }
        // Calcul du chemin relatif pour un affichage simplifié
        $relativePath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $path);
        // Si c'est un dossier, afficher une case à cocher spécifique et son contenu récursif
        if (is_dir($path)) {
            $html .= "<li>";
            // Checkbox pour le dossier (nommée 'folders[]')
            $html .= "<input type='checkbox' class='folder-checkbox' name='folders[]' value='" . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . "'> ";
            $html .= "<strong>" . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . "</strong>";
            $html .= renderFileExplorer($path, $baseDir);
            $html .= "</li>";
        } else {
            // Pour un fichier, afficher une checkbox simple
            $html .= "<li>";
            $html .= "<input type='checkbox' class='file-checkbox' name='files[]' value='" . htmlspecialchars($path, ENT_QUOTES, 'UTF-8') . "'> ";
            $html .= htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
            $html .= "</li>";
        }
    }
    $html .= "</ul>";
    return $html;
}

// Définition du répertoire de base à utiliser pour le scan : le dossier parent ("../")
// Utilisation de realpath pour obtenir le chemin absolu et éviter les problèmes de chemins relatifs
$baseDir = realpath(__DIR__ . '/../');

// Traitement du formulaire lors de la soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des fichiers et dossiers sélectionnés
    $selectedFiles = $_POST['files'] ?? [];
    $selectedFolders = $_POST['folders'] ?? [];

    // --- MODIFICATION ICI ---
    // On ne scanne PLUS les dossiers sélectionnés, on les ajoute directement
    foreach ($selectedFolders as $folder) {
        $realFolder = realpath($folder);
        if ($realFolder && strpos($realFolder, $baseDir) === 0) {
            $selectedFiles[] = $realFolder; // Ajoute le dossier lui-même
        }
    }

    // Suppression des doublons éventuels
    $selectedFiles = array_unique($selectedFiles);

    // Génération du Markdown en utilisant le répertoire de base
    generateMarkdownContent($selectedFiles, $baseDir);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorateur de fichiers PHP - Sélection avancée</title>
    <style>
        /* Styles basiques pour l'arborescence */
        ul { list-style-type: none; padding-left: 20px; }
        li { margin: 5px 0; }
        /* Différencier les cases à cocher pour dossiers et fichiers */
        .folder-checkbox { accent-color: blue; }
        .file-checkbox { accent-color: green; }
        /* Style général du formulaire */
        form { font-family: Arial, sans-serif; margin: 20px; }
        button { margin-top: 10px; padding: 8px 16px; font-size: 1em; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Explorateur de fichiers PHP</h1>
    <!-- Formulaire pour la sélection des fichiers et dossiers -->
    <form method="POST">
        <?php
        // Affichage de l'arborescence complète à partir du répertoire de base (../)
        echo renderFileExplorer($baseDir, $baseDir);
        ?>
        <button type="submit">Générer Markdown</button>
    </form>

    <script>
        // Script pour gérer la sélection en cascade des dossiers
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.folder-checkbox').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    let li = checkbox.closest('li');
                    if (li) {
                        li.querySelectorAll('input[type="checkbox"]').forEach(function(childCheckbox) {
                            childCheckbox.checked = checkbox.checked;
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
