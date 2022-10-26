<?php
defined('ROOT') OR exit('No direct script access allowed');
include_once(THEMES . $core->getConfigVal('theme') . '/header.php');
if ($pagesManager->isUnlocked($pageItem)) {
    if ($pageItem->file)
        include_once(THEMES . $core->getConfigVal('theme') . '/' . $pageItem->file);
    else {
        if ($pluginsManager->isActivePlugin('galerie') && galerie::searchByfileName($pageItem->img))
            echo '<img class="featured" src="' . UPLOAD . 'galerie/' . $pageItem->img . '" alt="' . $pageItem->name . '" />';
        echo $pageItem->content;
    }
} else {
    ?>
    <form method="post" action="">
        <input type="hidden" name="unlock" value="<?php echo $pageItem->id; ?>" />
        <p>
            <label>Mot de passe</label><br>
            <input style="display:none;" type="text" name="_password" value="" />
            <input required="required" type="password" name="password" value="" />
        </p>
        <p>
            <input type="submit" value="Envoyer" />
        </p>
    </form>
<?php
}
include_once(THEMES . $core->getConfigVal('theme') . '/footer.php');