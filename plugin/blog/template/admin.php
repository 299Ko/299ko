<?php
defined('ROOT') OR exit('No direct script access allowed');
include_once(ROOT . 'admin/header.php');
?>

<?php if ($mode == 'list') { ?>
    <ul class="tabs_style">
        <li><a class="button" href="index.php?p=blog&action=edit">Ajouter</a></li>
        <li><a target="_blank" class="button" href="<?php echo $runPlugin->getPublicUrl(); ?>rss.html">Flux RSS</a></li>
    </ul>
    <table>
        <tr>
            <th>Titre</th>
            <th>Date</th>
            <th></th>
        </tr>
        <?php foreach ($newsManager->getItems() as $k => $v) { ?>
            <tr>
                <td><?php echo $v->getName(); ?></td>
                <td><?php echo util::formatDate($v->getDate(), 'en', 'fr'); ?></td>
                <td>
                    <a href="index.php?p=blog&action=edit&id=<?php echo $v->getId(); ?>" class="button">Modifier</a>
                    <?php if ($newsManager->countComments($v->getId()) > 0) { ?><a href="index.php?p=blog&action=listcomments&id=<?php echo $v->getId(); ?>" class="button">Commentaires (<?php echo $newsManager->countComments($v->getId()); ?>)</a><?php } ?>
                    <a href="index.php?p=blog&action=del&id=<?php echo $v->getId(); ?>&token=<?php echo administrator::getToken(); ?>" onclick = "if (!confirm('Supprimer cet élément ?'))
                                return false;" class="button alert">Supprimer</a>
                </td>
            </tr>
    <?php } ?>
    </table>
<?php } ?>

    <?php if ($mode == 'edit') { ?>
    <form method="post" action="index.php?p=blog&action=save" enctype="multipart/form-data">
        <?php show::adminTokenField(); ?>
        <input type="hidden" name="id" value="<?php echo $news->getId(); ?>" />
        <?php if ($pluginsManager->isActivePlugin('galerie')) { ?>
            <input type="hidden" name="imgId" value="<?php echo $news->getImg(); ?>" />
    <?php } ?>
        <h3>Paramètres</h3>
        <p>
            <input <?php if ($news->getdraft()) { ?>checked<?php } ?> type="checkbox" name="draft" /> Ne pas publier (brouillon)
        </p>
    <?php if ($runPlugin->getConfigVal('comments')) { ?>
            <p>
                <input <?php if ($news->getCommentsOff()) { ?>checked<?php } ?> type="checkbox" name="commentsOff" /> Désactiver les commentaires pour cet article
            </p>
    <?php } ?>
        <h3>Contenu</h3>
        <p>
            <label>Titre</label><br>
            <input type="text" name="name" value="<?php echo $news->getName(); ?>" required="required" />
        </p>
    <?php if ($showDate) { ?>
            <p>
                <label>Date</label><br>
                <input placeholder="Exemple : 2017-07-06 12:28:51" type="date" name="date" value="<?php echo $news->getDate(); ?>" required="required" />
            </p>
    <?php } ?>

        <p>
            <label>Contenu</label><br>
            <textarea name="content" class="editor"><?php echo $core->callHook('beforeEditEditor', $news->getContent()); ?></textarea>
        </p>
        
        <?php
        core::executeHookAction('adminEditingAnItem', [$runPlugin->getName(), $news->getId()]);

     if ($pluginsManager->isActivePlugin('galerie')) { ?>
            <h3>Image à la une</h3>
            <p>
                <?php if (galerie::searchByfileName($news->getImg())) { ?><input type="checkbox" name="delImg" /> Supprimer l'image à la une
                <?php } else { ?><label>Fichier (png, jpg, jpeg, gif)</label><br><input type="file" name="file" accept="image/*" /><?php } ?>
                <br><br>
            <?php if (galerie::searchByfileName($news->getImg())) { ?><img src="<?php echo UPLOAD; ?>galerie/<?php echo $news->getImg(); ?>" alt="<?php echo $news->getImg(); ?>" /><?php } ?>
            </p>
    <?php } ?>

        <p><button type="submit" class="button">Enregistrer</button></p>
    </form>
<?php } ?>

<?php if ($mode == 'listcomments') { ?>
    <ul class="tabs_style">
        <li><a class="button" href="index.php?p=blog">Retour</a></li>
    </ul>
    <table>
        <tr>
            <th>Commentaire</th>
            <th></th>
        </tr>
    <?php foreach ($newsManager->getComments() as $k => $v) { ?>
            <tr>
                <td>
        <?php echo $v->getAuthor(); ?> <i><?php echo $v->getAuthorEmail(); ?></i> - <?php echo util::formatDate($v->getDate(), 'en', 'fr'); ?></b> :<br><br>
                    <form id="comment<?php echo $v->getId(); ?>" method="post" action="index.php?p=blog&action=updatecomment&id=<?php echo $_GET['id']; ?>&idcomment=<?php echo $v->getId(); ?>&token=<?php echo administrator::getToken(); ?>"><textarea name="content<?php echo $v->getId(); ?>"><?php echo $v->getContent(); ?></textarea></form>
                </td>
                <td>
                    <a onclick="updateComment(<?php echo $v->getId(); ?>);" href="javascript:" class="button">Enregistrer</a>
                    <a href="index.php?p=blog&action=delcomment&id=<?php echo $_GET['id']; ?>&idcomment=<?php echo $v->getId(); ?>&token=<?php echo administrator::getToken(); ?>" onclick = "if (!confirm('Supprimer cet élément ?'))
                                return false;" class="button alert">Supprimer</a>
                </td>
            </tr>
    <?php } ?>
    </table>
    <script>
        function updateComment(id) {
            document.getElementById('comment' + id).submit();
        }
    </script>
<?php } ?>

<?php include_once(ROOT . 'admin/footer.php'); ?>