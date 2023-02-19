<?php
defined('ROOT') OR exit('No direct script access allowed');
include_once(ROOT . 'admin/header.php');
?>

<?php if ($mode == 'list') { ?>
    <a role="button" class="button" href="index.php?p=galerie&action=edit">Ajouter</a>
    <a role="button" class="button showall" href="javascript:">Afficher / masquer les éléments invisibles</a>
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Titre</th>
                <th>Adresse</th>
                <th>Actions</th>
            </tr>
        </thead>
        <?php foreach ($galerie->getItems() as $k => $v) { ?>
            <tr class="<?php if ($v->getHidden()) { ?>hidden<?php } else { ?>visible<?php } ?>">
                <td><img width="128" src="<?php echo UPLOAD . 'galerie/' . $v->getImg(); ?>" alt="<?php echo $v->getImg(); ?>" /></td>
                <td><?php echo $v->getTitle(); ?><br><?php
                    if ($v->getCategory() != '') {
                        echo '<i>' . $v->getCategory() . '</i>';
                    }
                    ?></td>
                <td><input class="small" readonly="readonly" type="text" value="<?php echo $core->getConfigVal('siteUrl') . str_replace('..', '', UPLOAD) . 'galerie/' . $v->getImg(); ?>" /></td>
                <td>
                    <div role="group">
                        <a role="button" class="small" href="index.php?p=galerie&action=edit&id=<?php echo $v->getId(); ?>">Modifier</a>
                        <a role="button" class="small alert" href="index.php?p=galerie&action=del&id=<?php echo $v->getId(); ?>&token=<?php echo administrator::getToken(); ?>" onclick = "if (!confirm('Supprimer cet élément ?'))
                                            return false;">Supprimer</a>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>

<?php if ($mode == 'edit') { ?>
    <form method="post" action="index.php?p=galerie&action=save" enctype="multipart/form-data">
        <?php show::adminTokenField(); ?>
        <input type="hidden" name="id" value="<?php echo $item->getId(); ?>" />
        <h3>Paramètres</h3>
        <p>
            <input <?php if ($item->getHidden()) { ?>checked<?php } ?> type="checkbox" name="hidden" /> Rendre invisible
        </p>

        <p>
            <label>
                Catégorie
                <?php foreach ($galerie->listCategories() as $k => $v) { ?>
                    &nbsp;&nbsp;&#8594; <a class="category" href="javascript:"><?php echo $v; ?></a>
                <?php } ?>
            </label><br>
            <input type="text" name="category" id="category" value="<?php echo $item->getCategory(); ?>" />
        </p>
        <h3>Contenu</h3>
        <p>
            <label>Titre</label><br>
            <input type="text" name="title" value="<?php echo $item->getTitle(); ?>" required="required" />
        </p>
        <p>
            <label>Date</label><br>
            <input type="date" name="date" value="<?php echo $item->getDate(); ?>" /> 
        </p>

        <p>
            <label>Contenu</label><br>
            <textarea name="content" class="editor"><?php echo core::executeHookFilter('beforeEditEditor', $item->getContent()); ?></textarea>
        </p>
        <h3>Image</h3>
        <p>
            <label>Fichier (jpg)</label><br>
            <input type="file" name="file" accept="image/*" <?php if ($item->getImg() == '') { ?>required="required"<?php } ?> />
            <br>
            <?php if ($item->getImg() != '') { ?><img src="<?php echo UPLOAD; ?>galerie/<?php echo $item->getImg(); ?>" alt="<?php echo $item->getImg(); ?>" /><?php } ?>
        </p>

        <p><button type="submit" class="button">Enregistrer</button></p>
    </form>
<?php } ?>

<?php include_once(ROOT . 'admin/footer.php'); ?>