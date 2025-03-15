<h1>Marketplace</h1>

<!-- Conteneur de notifications -->
<div id="notification-container"></div>

<!-- Formulaire de recherche simplifié (sans filtres de dates) -->
<form class="filter-form" method="GET" action="<?= ROUTER::getInstance()->generate('admin-marketplace'); ?>">
    <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($searchQuery) ?>">
    <button type="submit">Filtrer</button>
</form>

<!-- Alertes de mises à jour -->
<?php if (!empty($updateAlerts)): ?>
    <div class="alert" style="background-color:#e7f3fe; border:1px solid #2196F3; padding:10px; margin-bottom:20px;">
        <h3>Mises à jour disponibles :</h3>
        <ul>
            <?php foreach ($updateAlerts as $alert): ?>
                <li>
                    <?= htmlspecialchars($alert['name']); ?> : Version installée <?= htmlspecialchars($alert['currentVersion']); ?>, nouvelle version <?= htmlspecialchars($alert['latestVersion']); ?>.
                    <a href="<?= htmlspecialchars($alert['url']); ?>" target="_blank">Voir sur GitHub</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Bloc de navigation avec 3 liens -->
<div class="marketplace-links">
    <ul>
        <li><a href="<?= ROUTER::getInstance()->generate('marketplace-plugins'); ?>">Liste des Plugins</a></li>
        <li><a href="<?= ROUTER::getInstance()->generate('marketplace-themes'); ?>">Liste des Thèmes</a></li>
        <li><a href="<?= ROUTER::getInstance()->generate('marketplace-official'); ?>">Zone Officielle</a></li>
    </ul>
</div>

<?php
// Récupération des top 5 éléments par likes pour chaque type
$topPlugins = [];
if (isset($reposData['plugins']) && is_array($reposData['plugins'])) {
    usort($reposData['plugins'], function($a, $b) {
        return $b['likes'] - $a['likes'];
    });
    $topPlugins = array_slice($reposData['plugins'], 0, 5);
}

$topThemes = [];
if (isset($reposData['themes']) && is_array($reposData['themes'])) {
    usort($reposData['themes'], function($a, $b) {
        return $b['likes'] - $a['likes'];
    });
    $topThemes = array_slice($reposData['themes'], 0, 5);
}
?>

<!-- Affichage des top 5 plugins et top 5 thèmes côte à côte -->
<div class="top-items" style="display:flex; justify-content:space-between;">
    <!-- Colonne Plugins -->
    <div class="top-plugins" style="width:48%;">
         <h2><?= isset($Lang['marketplace.plugins_title']) ? $Lang['marketplace.plugins_title'] : 'Plugins Disponibles'; ?></h2>
         <?php if (count($topPlugins) > 0): ?>
            <ul>
               <?php foreach($topPlugins as $release): ?>
                  <li style="border-bottom:1px solid #ccc; padding:10px 0;">
                     <strong><?= htmlspecialchars($release['name']); ?></strong>
                     <p><?= htmlspecialchars($release['body']); ?></p>
                     <p>
                        <strong>Version :</strong> <?= htmlspecialchars($release['version']); ?><br>
                        <strong>Likes :</strong> <span id="like-<?= htmlspecialchars($release['id']); ?>"><?= htmlspecialchars($release['likes']); ?></span>
                     </p>
                     <a href="<?= htmlspecialchars($release['zipball_url']); ?>" target="_blank"><?= isset($Lang['marketplace.download']) ? $Lang['marketplace.download'] : 'Télécharger'; ?></a>
                     <button onclick="installRelease('plugins', '<?= htmlspecialchars($release['zipball_url']); ?>', '<?= htmlspecialchars($release['folder'] ?? ''); ?>')">Installer</button>
                     <button onclick="likeRelease('plugins', '<?= htmlspecialchars($release['id']); ?>')">Like</button>
                  </li>
               <?php endforeach; ?>
            </ul>
            <?= renderPagination('plugins', $pagination['plugins'], $searchQuery) ?>
         <?php else: ?>
            <p><?= isset($Lang['marketplace.no_item']) ? $Lang['marketplace.no_item'] : 'Aucun plugin disponible pour le moment'; ?></p>
         <?php endif; ?>
    </div>

    <!-- Colonne Thèmes -->
    <div class="top-themes" style="width:48%;">
         <h2><?= isset($Lang['marketplace.themes_title']) ? $Lang['marketplace.themes_title'] : 'Thèmes Disponibles'; ?></h2>
         <?php if (count($topThemes) > 0): ?>
            <ul>
               <?php foreach($topThemes as $release): ?>
                  <li style="border-bottom:1px solid #ccc; padding:10px 0;">
                     <strong><?= htmlspecialchars($release['name']); ?></strong>
                     <p><?= htmlspecialchars($release['body']); ?></p>
                     <p>
                        <strong>Version :</strong> <?= htmlspecialchars($release['version']); ?><br>
                        <strong>Likes :</strong> <span id="like-<?= htmlspecialchars($release['id']); ?>"><?= htmlspecialchars($release['likes']); ?></span>
                     </p>
                     <a href="<?= htmlspecialchars($release['zipball_url']); ?>" target="_blank"><?= isset($Lang['marketplace.download']) ? $Lang['marketplace.download'] : 'Télécharger'; ?></a>
                     <button onclick="installRelease('themes', '<?= htmlspecialchars($release['zipball_url']); ?>', '<?= htmlspecialchars($release['folder'] ?? ''); ?>')">Installer</button>
                     <button onclick="likeRelease('themes', '<?= htmlspecialchars($release['id']); ?>')">Like</button>
                  </li>
               <?php endforeach; ?>
            </ul>
            <?= renderPagination('themes', $pagination['themes'], $searchQuery) ?>
         <?php else: ?>
            <p><?= isset($Lang['marketplace.no_item']) ? $Lang['marketplace.no_item'] : 'Aucun thème disponible pour le moment'; ?></p>
         <?php endif; ?>
    </div>
</div>

<!-- Zone Officielle -->
<section class="official-zone" style="margin-top:30px;">
    <h2>Zone Officielle</h2>
    <div style="display:flex; justify-content:space-between;">
        <!-- Plugins Officiels -->
        <div class="official-plugins" style="width:48%;">
             <h3>Plugins Officiels</h3>
             <!-- Intégrer ici l'affichage des plugins officiels -->
             <p>Aucun plugin officiel disponible pour le moment.</p>
        </div>
        <!-- Thèmes Officiels -->
        <div class="official-themes" style="width:48%;">
             <h3>Thèmes Officiels</h3>
             <!-- Intégrer ici l'affichage des thèmes officiels -->
             <p>Aucun thème officiel disponible pour le moment.</p>
        </div>
    </div>
</section>

<!-- Scripts -->
<script>
    // Fonction de notification simple
    function showNotification(message, type = 'info') {
        const container = document.getElementById('notification-container');
        const notif = document.createElement('div');
        notif.className = 'notification';
        if (type === 'success') {
            notif.style.backgroundColor = '#4CAF50';
        } else if (type === 'error') {
            notif.style.backgroundColor = '#f44336';
        }
        notif.textContent = message;
        container.appendChild(notif);
        setTimeout(() => {
            notif.style.opacity = 0;
            setTimeout(() => {
                container.removeChild(notif);
            }, 500);
        }, 3000);
    }

    // Installation automatique
    async function installRelease(type, zipUrl, pluginFolder = null) {
        if (confirm("Voulez-vous installer cet élément ?")) {
            let url = '<?= ROUTER::getInstance()->generate("admin-marketplace"); ?>?action=install';
            let data = {
                type: type,
                zipUrl: zipUrl,
                token: '<?= htmlspecialchars($token) ?>'
            };
            if (pluginFolder !== null) {
                data.pluginFolder = pluginFolder;
            }
            let response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            if (response.status === 202) {
                showNotification("Installation réussie !", "success");
                setTimeout(() => { window.location.reload(); }, 1500);
            } else {
                showNotification("L'installation a échoué.", "error");
            }
        }
    }

    // Requête de like
    async function likeRelease(type, releaseId) {
        let url = '<?= ROUTER::getInstance()->generate("marketplace-like"); ?>';
        let data = { type: type, releaseId: releaseId };
        let response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        if (response.status === 202) {
            let result = await response.json();
            document.getElementById('like-' + releaseId).textContent = result.likes;
            showNotification("Merci pour votre like !", "success");
        } else {
            showNotification("Erreur lors de l'ajout du like.", "error");
        }
    }
</script>
