    <h1>Liste Complète des Plugins</h1>
    <!-- Lien de retour vers la page principale de la marketplace -->
    <a href="<?= ROUTER::getInstance()->generate('admin-marketplace'); ?>">Retour à la Marketplace</a>

    <?php if(isset($pluginsData) && is_array($pluginsData) && count($pluginsData) > 0): ?>
        <ul>
            <?php foreach($pluginsData as $release): ?>
                <li style="border-bottom:1px solid #ccc; padding:10px 0;">
                    <strong><?= htmlspecialchars($release['name']); ?></strong>
                    <p><?= htmlspecialchars($release['body']); ?></p>
                    <p>
                        <strong>Version :</strong> <?= htmlspecialchars($release['tag_name'] ?? ''); ?><br>
                        <strong>Publié le :</strong> <?= htmlspecialchars($release['published_at'] ?? ''); ?><br>
                        <strong>Téléchargements :</strong> <?= htmlspecialchars($release['download_count'] ?? '0'); ?><br>
                        <strong>Likes :</strong> <span id="like-<?= htmlspecialchars($release['id']); ?>"><?= htmlspecialchars($release['likes'] ?? '0'); ?></span>
                    </p>
                    <a href="<?= htmlspecialchars($release['zipball_url']); ?>" target="_blank">Télécharger</a>
                    <button onclick="installRelease('plugins', '<?= htmlspecialchars($release['zipball_url']); ?>', '<?= htmlspecialchars($release['folder'] ?? ''); ?>')">Installer</button>
                    <button onclick="likeRelease('plugins', '<?= htmlspecialchars($release['id']); ?>')">Like</button>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun plugin disponible pour le moment.</p>
    <?php endif; ?>

    <!-- Fonctions JavaScript réutilisées pour l'installation et le like -->
    <script>
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
                    alert("Installation réussie !");
                    setTimeout(() => { window.location.reload(); }, 1500);
                } else {
                    alert("L'installation a échoué.");
                }
            }
        }
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
                alert("Merci pour votre like !");
            } else {
                alert("Erreur lors de l'ajout du like.");
            }
        }
    </script>