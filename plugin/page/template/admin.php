<?php
defined('ROOT') OR exit('No direct script access allowed');
include_once(ROOT . 'admin/header.php');
echo $pageItem->name;
if ($mode == 'list') {
    
        echo $pageManager->output();
        ?>
    <ul class = "tabs_style">
        <li><a class = "button" href = "index.php?p=page&amp;action=edit">Ajouter une page</a></li>
        <li><a class = "button" href = "index.php?p=page&amp;action=edit&parent=1">Ajouter un item parent</a></li>
        <li><a class = "button" href = "index.php?p=page&amp;action=edit&link=1">Ajouter un lien externe</a></li>
    </ul>
    <?php if ($lost != '') {
        ?>
        <p>Des pages "fantômes" pouvant engendrer des dysfonctionnements ont été trouvées. <a href="index.php?p=page&amp;action=maintenance&id=<?php echo $lost; ?>&token=<?php echo administrator::getToken(); ?>">Cliquez ici</a> pour exécuter le script de maintenance.</p>
    <?php } ?>
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Adresse</th>
                <th>Position</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($page->getItems() as $k => $pageItem)
                if ($pageItem->getParent() == 0 && ($pageItem->targetIs() != 'plugin' || ($pageItem->targetIs() == 'plugin' && $pluginsManager->isActivePlugin($pageItem->getTarget())))) {
                    ?>
                    <tr>
                        <td><?php echo $pageItem->getName(); ?></td>
                        <td><?php if ($pageItem->targetIs() != 'parent') { ?><input readonly="readonly" type="text" value="<?php echo $page->makeUrl($pageItem); ?>" /><?php } ?></td>
                        <td>
                            <a class="up" href="index.php?p=page&action=up&id=<?php echo $pageItem->getId(); ?>&token=<?php echo administrator::getToken(); ?>"><i class="fa-solid fa-circle-up"></i></a>
                            <a class="down" href="index.php?p=page&action=down&id=<?php echo $pageItem->getId(); ?>&token=<?php echo administrator::getToken(); ?>"><i class="fa-solid fa-circle-down"></i></a>
                        </td>
                        <td>
                            <a class="button" href="index.php?p=page&amp;action=edit&amp;id=<?php echo $pageItem->getId(); ?>">Modifier</a> 
                            <?php if (!$pageItem->getIsHomepage() && $pageItem->targetIs() != 'plugin') { ?><a class="button alert" href="index.php?p=page&amp;action=del&amp;id=<?php echo $pageItem->getId() . '&amp;token=' . administrator::getToken(); ?>" onclick = "if (!confirm('Supprimer cet élément ?'))
                                                           return false;">Supprimer</a><?php } ?>	
                        </td>
                    </tr>
                    <?php
                    foreach ($page->getItems() as $k => $pageItemChild)
                        if ($pageItemChild->getParent() == $pageItem->getId() && ($pageItemChild->targetIs() != 'plugin' || ($pageItemChild->targetIs() == 'plugin' && $pluginsManager->isActivePlugin($pageItemChild->getTarget())))) {
                            ?>
                            <tr>
                                <td>▸ <?php echo $pageItemChild->getName(); ?></td>
                                <td><input readonly="readonly" type="text" value="<?php echo $page->makeUrl($pageItemChild); ?>" /></td>
                                <td>
                                    <a class="up" href="index.php?p=page&action=up&id=<?php echo $pageItemChild->getId(); ?>&token=<?php echo administrator::getToken(); ?>"><i class="fa-solid fa-circle-up"></i></a>
                                    <a class="down" href="index.php?p=page&action=down&id=<?php echo $pageItemChild->getId(); ?>&token=<?php echo administrator::getToken(); ?>"><i class="fa-solid fa-circle-down"></i></a>
                                </td>
                                <td>
                                    <a class="button" href="index.php?p=page&amp;action=edit&amp;id=<?php echo $pageItemChild->getId(); ?>">Modifier</a> 
                                    <?php if (!$pageItemChild->getIsHomepage() && $pageItemChild->targetIs() != 'plugin') { ?><a class="button alert" href="index.php?p=page&amp;action=del&amp;id=<?php echo $pageItemChild->getId() . '&amp;token=' . administrator::getToken(); ?>" onclick = "if (!confirm('Supprimer cet élément ?'))
                                                                           return false;">Supprimer</a><?php } ?>	
                                </td>
                            </tr>
                            <?php
                        }
                }
            ?>
        </tbody>
    </table>
<?php } ?>

<?php if ($mode == 'edit' && !$isLink && $pageItem->type === PageItem::PAGE) { ?>
    <form method="post" action="index.php?p=page&amp;action=save" enctype="multipart/form-data">
        <?php show::adminTokenField(); ?>
        <input type="hidden" name="id" value="<?php echo $pageItem->id; ?>" />
        <?php if ($pluginsManager->isActivePlugin('galerie')) { ?>
            <input type="hidden" name="imgId" value="<?php echo $pageItem->img; ?>" />
        <?php } ?>
        <h3>Paramètres</h3>
        <p>
            <input <?php if ($pageItem->isHomepage) { ?>checked<?php } ?> type="checkbox" name="isHomepage" /> Page d'accueil
        </p>
        <p>
            <input <?php if ($pageItem->isHidden) { ?>checked<?php } ?> type="checkbox" name="isHidden" /> Ne pas afficher dans le menu
        </p>
        <p>
            <label>Item parent</label><br>
            <select name="parent">
                <option value="">Aucun</option>
                <?php
                foreach ($pageManager->getItems() as $k => $v)
                    if ($v->type === PageItem::CATEGORIE) {
                        ?>
                        <option <?php if ($v->id === $pageItem->parent) { ?>selected<?php } ?> value="<?php echo $v->id; ?>"><?php echo $v->name; ?></option>
                    <?php } ?>
            </select>
        </p>
        <p>
            <label>Classe CSS</label>
            <input type="text" name="cssClass" value="<?php echo $pageItem->cssClass; ?>" />
        </p>
        <p>
            <label>Position</label>
            <input type="number" name="position" value="<?php echo $pageItem->position; ?>" />
        </p>
        <p>
            <label>Restreindre l'accès avec un mot de passe</label>
            <input type="password" name="_password" value="" />
        </p>
        <?php if ($pageItem->password != '') { ?>
            <p>
                <input type="checkbox" name="resetPassword" /> Retirer la restriction par mot de passe  
            </p>
        <?php } ?>
        <h3>SEO</h3>
        <p>
            <input <?php if ($pageItem->noIndex) { ?>checked<?php } ?> type="checkbox" name="noIndex" /> Interdire l'indexation
        </p>
        <p>
            <label>Meta title</label>
            <input type="text" name="metaTitleTag" value="<?php echo $pageItem->metaTitleTag; ?>" />
        </p>
        <p>
            <label>Meta description</label>
            <input type="text" name="metaDescriptionTag" value="<?php echo $pageItem->metaDescriptionTag; ?>" />
        </p>
        <h3>Contenu</h3>
        <p>
            <label>Nom</label><br>
            <input type="text" name="name" value="<?php echo $pageItem->name; ?>" required="required" />
        </p>
        <p>
            <label>Titre de page</label><br>
            <input type="text" name="mainTitle" value="<?php echo $pageItem->mainTitle; ?>" />
        </p>
        <p>
            <label>Inclure un fichier .php au lieu du contenu
                <select name="file" class="large-3 columns">
                    <option value="">--</option>
                    <?php foreach ($page->listTemplates() as $file) { ?>
                        <option <?php if ($file == $pageItem->getFile()) { ?>selected<?php } ?> value="<?php echo $file; ?>"><?php echo $file; ?></option>
                    <?php } ?>
                </select>
        </p>
        <p>
            <label>Contenu</label>
            <textarea name="content" class="editor"><?php echo $core->callHook('beforeEditEditor', $pageItem->getContent()); ?></textarea>
        </p>
        <?php
        if ($pluginsManager->isActivePlugin('galerie')) {
            galerieDisplaySidebarModule($pageItem->getImg());
        }
        core::executeHookAction('adminEditingAnItem', [$runPlugin->getName(), $pageItem->getId()]);
        show::displayAdminSidebar();
        ?>
        <p>
            <button type="submit" class="button success radius">Enregistrer</button>
        </p>
    </form>
<?php } ?>

<?php if ($mode == 'edit' && ($pageItem->type === PageItem::URL || $pageItem->type === PageItem::PLUGIN)) { ?>
    <form method="post" action="index.php?p=page&amp;action=save">
        <?php show::adminTokenField(); ?>
        <input type="hidden" name="id" value="<?php echo $pageItem->id; ?>" />
        <!--<input type="hidden" name="position" value="<?php echo $pageItem->position; ?>" />-->
        <p>
            <input <?php if ($pageItem->isHidden) { ?>checked<?php } ?> type="checkbox" name="isHidden" /> <label for="isHidden">Ne pas afficher dans le menu</label>
        </p>
        <p>
            <label>Item parent</label><br>
            <select name="parent">
                <option value="">Aucun</option>
                <?php
                foreach ($pageManager->getItems() as $k => $v)
                    if ($v->type === PageItem::CATEGORIE) {
                        ?>
                        <option <?php if ($v->id == $pageItem->parent) { ?>selected<?php } ?> value="<?php echo $v->id; ?>"><?php echo $v->name; ?></option>
                    <?php } ?>
            </select>
        </p>
        <p>
            <label>Nom</label><br>
            <input type="text" name="name" value="<?php echo $pageItem->name; ?>" required="required" />
        </p>
        <?php if ($pageItem->type === PageItem::PLUGIN) { ?>
            <p>
                <label>Cible : <?php echo $pageItem->target; ?></label>
                <input style="display:none;" type="text" name="target" value="<?php echo $pageItem->target; ?>" />
            </p>
        <?php } else { ?>
            <p>
                <label>Cible</label><br>
                <input placeholder="Example : http://www.google.com" <?php if ($pageItem->type === PageItem::PLUGIN) { ?>readonly<?php } ?> type="url" name="target" value="<?php echo $pageItem->target; ?>" required="required" />
            </p>
        <?php } ?>
        <p>
            <label>Ouverture</label><br>
            <select name="targetAttr">
                <option value="_self" <?php if ($pageItem->targetAttr == '_self') { ?>selected<?php } ?>>Même fenêtre</option>
                <option value="_blank" <?php if ($pageItem->targetAttr == '_blank') { ?>selected<?php } ?>>Nouvelle fenêtre</option>
            </select>
        </p>
        <p>
            <label>Classe CSS</label>
            <input type="text" name="cssClass" value="<?php echo $pageItem->cssClass; ?>" />
        </p>
        <p>
            <label>Position</label>
            <input type="number" name="position" value="<?php echo $pageItem->position; ?>" />
        </p>
        <p>
            <button type="submit" class="button success radius">Enregistrer</button>
        </p>
    </form>
<?php } ?>

<?php if ($mode == 'edit' && $isParent) { ?>
    <form method="post" action="index.php?p=page&amp;action=save">
        <?php show::adminTokenField(); ?>
        <input type="hidden" name="id" value="<?php echo $pageItem->id; ?>" />
        <!--<input type="hidden" name="position" value="<?php echo $pageItem->getPosition(); ?>" />-->
        <input type="hidden" name="target" value="javascript:" />
        <p>
            <input <?php if ($pageItem->getIsHidden()) { ?>checked<?php } ?> type="checkbox" name="isHidden" /> <label for="isHidden">Ne pas afficher dans le menu</label>
        </p>
        <p>
            <label>Nom</label><br>
            <input type="text" name="name" value="<?php echo $pageItem->getName(); ?>" required="required" />
        </p>
        <p>
            <label>Classe CSS</label>
            <input type="text" name="cssClass" value="<?php echo $pageItem->getCssClass(); ?>" />
        </p>
        <p>
            <label>Position</label>
            <input type="number" name="position" value="<?php echo $pageItem->getPosition(); ?>" />
        </p>
        <p>
            <button type="submit" class="button success radius">Enregistrer</button>
        </p>
    </form>
<?php } ?>

<?php include_once(ROOT . 'admin/footer.php'); ?>