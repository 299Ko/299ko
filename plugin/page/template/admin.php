<?php
defined('ROOT') OR exit('No direct script access allowed');
include_once(ROOT . 'admin/header.php');
if ($mode == 'list') {
    ?>
    <a role="button" href= "index.php?p=page&amp;action=edit"><i class="bi bi-file-earmark-plus"></i> Ajouter une page</a>
    <a role="button" href= "index.php?p=categories&plugin=page"><i class="bi bi-folder-plus"></i> Ajouter une catégorie</a>
    <a role="button" href= "index.php?p=page&amp;action=edit&link=1"><i class="bi bi-link-45deg"></i> Ajouter un lien externe</a>
    <?php
    echo $pageManager->output();
} 
if ($mode == 'edit' && $pageItem->type === PageItem::PAGE) {
    ?>
    <form method="post" action="index.php?p=page&amp;action=save" enctype="multipart/form-data">
    <?php show::adminTokenField(); ?>
        <input type="hidden" name="id" value="<?php echo $pageItem->id; ?>" />
        <input type="hidden" name="type" value="<?php echo $pageItem->type; ?>" />
        <?php if ($pluginsManager->isActivePlugin('galerie')) { ?>
            <input type="hidden" name="imgId" value="<?php echo $pageItem->img; ?>" />
    <?php } ?>
        <article>
            <header>
                <h3>Paramètres</h3>
            </header>
            <p>
                <label for="isHomepage">
                    <input <?php if ($pageItem->isHomepage) { ?>checked<?php } ?> type="checkbox" name="isHomepage" id="isHomepage" role="switch"/> Page d'accueil
                </label>
            </p>
            <p>
                <label for="isHidden">
                    <input <?php if ($pageItem->isHidden) { ?>checked<?php } ?> type="checkbox" name="isHidden" id="isHidden" role="switch"/> Ne pas afficher dans le menu
                </label>
            </p>
            <p>
                <label for="parent">Catégorie parente</label>
                <select name="parent" id="parent">
                    <option value="">Aucune</option>
                    <?php
                    foreach ($pageManager->getItems() as $k => $v)
                        if ($v->type === PageItem::CATEGORIE) {
                            ?>
                            <option <?php if ($v->id === $pageItem->parent) { ?>selected<?php } ?> value="<?php echo $v->id; ?>"><?php echo $v->name; ?></option>
        <?php } ?>
                </select>
            </p>
            <p>
                <label for="cssClass">Classe CSS</label>
                <input type="text" name="cssClass" id="cssClass" value="<?php echo $pageItem->cssClass; ?>" />
            </p>
            <p>
                <label for="position">Position</label>
                <input type="number" name="position" id="position" value="<?php echo $pageItem->position; ?>" />
            </p>
            <p>
                <label for="_password">Restreindre l'accès avec un mot de passe</label>
                <input type="password" name="_password" id="_password" value="" />
            </p>
    <?php if ($pageItem->password != '') { ?>
                <p>
                    <label for="resetPassword">
                        <input type="checkbox" name="resetPassword" id="resetPassword" role="switch"/> Retirer la restriction par mot de passe  
                    </label>
                </p>
    <?php } ?>
        </article>
        <article>
            <header>
                <h3>SEO</h3>
            </header>
            <p>
                <label for="noIndex">
                    <input <?php if ($pageItem->noIndex) { ?>checked<?php } ?> type="checkbox" name="noIndex" id="noIndex" role="switch"/> Interdire l'indexation
                </label>
            </p>
            <p>
                <label for="metaTitleTag">Meta title</label>
                <input type="text" name="metaTitleTag" id="metaTitleTag" value="<?php echo $pageItem->metaTitleTag; ?>" />
            </p>
            <p>
                <label for="metaDescriptionTag">Meta description</label>
                <input type="text" name="metaDescriptionTag" id="metaDescriptionTag" value="<?php echo $pageItem->metaDescriptionTag; ?>" />
            </p>
        </article>
        <article>
            <header>
                <h3>Contenu</h3>
            </header>
            <p>
                <label for="name">Nom</label>
                <input type="text" name="name" id="name" value="<?php echo $pageItem->name; ?>" required="required" />
            </p>
            <p>
                <label for="mainTitle">Titre de page</label>
                <input type="text" name="mainTitle" id="mainTitle" value="<?php echo $pageItem->mainTitle; ?>" />
            </p>
            <p>
                <label for="file">Inclure un fichier .php au lieu du contenu
                    <select name="file" id="file" class="large-3 columns">
                        <option value="">--</option>
                        <?php foreach ($pageManager->listTemplates() as $file) { ?>
                            <option <?php if ($file == $pageItem->file) { ?>selected<?php } ?> value="<?php echo $file; ?>"><?php echo $file; ?></option>
    <?php } ?>
                    </select>
            </p>
            <p>
                <label for="content">Contenu</label>
                <textarea name="content" id="content" class="editor"><?php echo core::executeHookFilter('beforeEditEditor', $pageItem->content); ?></textarea>
            </p>
        </article>
        <?php
        if ($pluginsManager->isActivePlugin('galerie')) {
            galerieDisplaySidebarModule($pageItem->img);
        }
        core::executeHookAction('adminEditingAnItem', [$runPlugin->getName(), $pageItem->id]);
        show::displayAdminSidebar();
        ?>
        <p>
            <button type="submit">Enregistrer</button>
        </p>
    </form>
<?php } ?>

<?php if ($mode == 'edit' && ($pageItem->type === PageItem::URL || $pageItem->type === PageItem::PLUGIN)) { ?>
    <article>
        <header>
            <h3>Paramètres</h3>
        </header>
        <form method="post" action="index.php?p=page&amp;action=save">
    <?php show::adminTokenField(); ?>
            <input type="hidden" name="id" value="<?php echo $pageItem->id; ?>" />
            <input type="hidden" name="type" value="<?php echo $pageItem->type; ?>" />
            <p>
                <label for="isHidden">
                    <input <?php if ($pageItem->isHidden) { ?>checked<?php } ?> type="checkbox" name="isHidden" id="isHidden" role="switch"/> Ne pas afficher dans le menu
                </label>
            </p>
            <p>
                <label for="parent">Catégorie parente</label>
                <select name="parent" id="parent">
                    <option value="">Aucune</option>
                    <?php
                    foreach ($pageManager->getItems() as $k => $v)
                        if ($v->type === PageItem::CATEGORIE) {
                            ?>
                            <option <?php if ($v->id === $pageItem->parent) { ?>selected<?php } ?> value="<?php echo $v->id; ?>"><?php echo $v->name; ?></option>
        <?php } ?>
                </select>
            </p>
            <p>
                <label for="name">Nom</label>
                <input type="text" name="name" id="name" value="<?php echo $pageItem->name; ?>" required="required" />
            </p>
    <?php if ($pageItem->type === PageItem::PLUGIN) { ?>
                <p>
                    <label>Cible : <?php echo $pageItem->target; ?></label>
                    <input style="display:none;" type="text" name="target" value="<?php echo $pageItem->target; ?>" />
                </p>
    <?php } else { ?>
                <p>
                    <label for="target">Cible</label>
                    <input placeholder="Example : http://www.google.com" <?php if ($pageItem->type === PageItem::PLUGIN) { ?>readonly<?php } ?> type="url" name="target" id="target" value="<?php echo $pageItem->target; ?>" required="required" />
                </p>
    <?php } ?>
            <p>
                <label for="targetAttr">Ouverture</label>
                <select name="targetAttr" id="targetAttr">
                    <option value="_self" <?php if ($pageItem->targetAttr == '_self') { ?>selected<?php } ?>>Même fenêtre</option>
                    <option value="_blank" <?php if ($pageItem->targetAttr == '_blank') { ?>selected<?php } ?>>Nouvelle fenêtre</option>
                </select>
            </p>
            <p>
                <label for="cssClass">Classe CSS</label>
                <input type="text" name="cssClass" id="cssClass" value="<?php echo $pageItem->cssClass; ?>" />
            </p>
            <p>
                <label for="position">Position</label>
                <input type="number" name="position" id="position" value="<?php echo $pageItem->position; ?>" />
            </p>
            <p>
                <button type="submit">Enregistrer</button>
            </p>
        </form>
    </article>
<?php } ?>

<?php
include_once(ROOT . 'admin/footer.php');
