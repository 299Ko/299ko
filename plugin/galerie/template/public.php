<?php
defined('ROOT') OR exit('No direct script access allowed');
include_once(THEMES . $core->getConfigVal('theme') . '/header.php');
?>
<!-- Intro -->
<?php echo $runPlugin->getConfigVal('introduction'); ?>

<!-- Categories -->
<?php if ($galerie->useCategories()) { ?>
    <ul class="categories">
        <?php if (count($galerie->listCategories(false)) > 0) { ?><li><a rel="category_all" href="javascript:">Afficher tout</a></li><?php } ?>
        <?php foreach ($galerie->listCategories(false) as $k => $v) { ?>
            <li><a rel="category_<?php echo util::strToUrl($v); ?>" href="javascript:"><?php echo $v; ?></a></li>
        <?php } ?>
    </ul>
<?php } ?>

<!-- Liste -->
<?php if (!$galerie->countItems()) { ?>
    <p>Aucun élément n'a été trouvé.</p>
<?php } else { ?>
    <ul id="list">
        <?php foreach ($galerie->getItems() as $k => $obj)
            if (!$obj->getHidden()) { ?>
        <li class="category_<?php echo util::strToUrl($obj->getCategory()); ?> category_all" style="background-image:url(<?php echo util::urlBuild(UPLOAD . 'galerie/' . $obj->getImg()); ?>);">
                    <a href="<?php echo util::urlBuild(UPLOAD . 'galerie/' . $obj->getImg()); ?>" data-fancybox="gallery" data-caption="<?php echo $obj->getTitle(); ?><br><?php echo $obj->getCategory(); ?><br><?php echo htmlentities($obj->getContent()); ?>">
            <?php if ($runPlugin->getConfigVal('showTitles')) { ?><span><?php echo $obj->getTitle(); ?></span><?php } ?>
                    </a>
                </li>
        <?php } ?>
    </ul>
<?php } ?>

<?php include_once(THEMES . $core->getConfigVal('theme') . '/footer.php') ?>