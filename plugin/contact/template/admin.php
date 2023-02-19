<?php
defined('ROOT') OR exit('No direct script access allowed');
include_once(ROOT . 'admin/header.php');
?>

<form method="post" action="index.php?p=contact&action=save">
    <?php show::adminTokenField(); ?>
    <h3>Contenu</h3>
    <p>
        <label>Avant le formulaire</label><br>
        <textarea class="editor" name="content1"><?php echo core::executeHookFilter('beforeEditEditor', $runPlugin->getConfigVal('content1')); ?></textarea>
    </p>
    <p>
        <label>Après le formulaire</label><br>
        <textarea class="editor" name="content2"><?php echo core::executeHookFilter('beforeEditEditor', $runPlugin->getConfigVal('content2')); ?></textarea>
    </p>
    <p><button type="submit" class="button">Enregistrer</button></p>
    <h3>Adresses email récoltées</h3>
    <p>
        <textarea readonly="readonly"><?php echo $emails; ?></textarea>
    </p>

    <a href="index.php?p=contact&action=emptymails&token=<?php echo administrator::getToken(); ?>" role="button" class="alert"
       onclick="return(confirm('Êtes-vous sûr de vouloir vider la base des adresses mail collectées ?'));">Supprimer la base</a> 
</form>

<?php include_once(ROOT . 'admin/footer.php'); ?>