<?php
defined('ROOT') OR exit('No direct script access allowed');
include_once(ROOT . 'admin/header.php');

if ($mode == 'list') { ?>

    <ul class="tabs_style">
        <li><a class="button" href="index.php?p=page&amp;action=edit">Ajouter une page</a></li>
        <li><a class="button" href="index.php?p=page&amp;action=edit&parent=1">Ajouter un item parent</a></li>
        <li><a class="button" href="index.php?p=page&amp;action=edit&link=1">Ajouter un lien externe</a></li>
    </ul>
    <?php if ($lost != '') {
         var_dump($lost); ?>
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
            <?php foreach ($page->getItems() as $k => $pageItem)
                if ((int) $pageItem->getParent() == 0 && ($pageItem->targetIs() != 'plugin' || ($pageItem->targetIs() == 'plugin' && $pluginsManager->isActivePlugin($pageItem->getTarget())))) {
                    ?>
                    <tr>
                        <td><?php echo $pageItem->getName(); ?></td>
                        <td><?php if ($pageItem->targetIs() != 'parent') { ?><input readonly="readonly" type="text" value="<?php echo $page->makeUrl($pageItem); ?>" /><?php } ?></td>
                        <td>
                            <a class="up" href="index.php?p=page&action=up&id=<?php echo $pageItem->getId(); ?>&token=<?php echo administrator::getToken(); ?>"><img src="<?php echo PLUGINS; ?>page/template/up.png" alt="icon" /></a>&nbsp;
                            <a class="down" href="index.php?p=page&action=down&id=<?php echo $pageItem->getId(); ?>&token=<?php echo administrator::getToken(); ?>"><img src="<?php echo PLUGINS; ?>page/template/down.png" alt="icon" /></a>
                        </td>
                        <td>
                            <a class="button" href="index.php?p=page&amp;action=edit&amp;id=<?php echo $pageItem->getId(); ?>">Modifier</a> 
                               <?php if (!$pageItem->getIsHomepage() && $pageItem->targetIs() != 'plugin') { ?><a class="button alert" href="index.php?p=page&amp;action=del&amp;id=<?php echo $pageItem->getId() . '&amp;token=' . administrator::getToken(); ?>" onclick = "if (!confirm('Supprimer cet élément ?'))
                                        return false;">Supprimer</a><?php } ?>	
                        </td>
                    </tr>
                    <?php foreach ($page->getItems() as $k => $pageItemChild)
                        if ($pageItemChild->getParent() == $pageItem->getId() && ($pageItemChild->targetIs() != 'plugin' || ($pageItemChild->targetIs() == 'plugin' && $pluginsManager->isActivePlugin($pageItemChild->getTarget())))) {
                            ?>
                            <tr>
                                <td>▸ <?php echo $pageItemChild->getName(); ?></td>
                                <td><input readonly="readonly" type="text" value="<?php echo $page->makeUrl($pageItemChild); ?>" /></td>
                                <td>
                                    <a class="up" href="index.php?p=page&action=up&id=<?php echo $pageItemChild->getId(); ?>&token=<?php echo administrator::getToken(); ?>"><img src="<?php echo PLUGINS; ?>page/template/up.png" alt="icon" /></a>&nbsp;
                                    <a class="down" href="index.php?p=page&action=down&id=<?php echo $pageItemChild->getId(); ?>&token=<?php echo administrator::getToken(); ?>"><img src="<?php echo PLUGINS; ?>page/template/down.png" alt="icon" /></a>
                                </td>
                                <td>
                                    <a class="button" href="index.php?p=page&amp;action=edit&amp;id=<?php echo $pageItemChild->getId(); ?>">Modifier</a> 
                    <?php if (!$pageItemChild->getIsHomepage() && $pageItemChild->targetIs() != 'plugin') { ?><a class="button alert" href="index.php?p=page&amp;action=del&amp;id=<?php echo $pageItemChild->getId() . '&amp;token=' . administrator::getToken(); ?>" onclick = "if (!confirm('Supprimer cet élément ?'))
                                                                return false;">Supprimer</a><?php } ?>	
                                </td>
                            </tr>
                <?php }
        }
    ?>
        </tbody>
    </table>
    <?php } ?>

    <?php if ($mode == 'edit' && !$isLink && !$isParent && $pageItem->targetIs() != 'plugin') { ?>
    <form method="post" action="index.php?p=page&amp;action=save" enctype="multipart/form-data">
        <?php show::adminTokenField(); ?>
        <input type="hidden" name="id" value="<?php echo $pageItem->getId(); ?>" />
    <?php if ($pluginsManager->isActivePlugin('galerie')) { ?>
            <input type="hidden" name="imgId" value="<?php echo $pageItem->getImg(); ?>" />
    <?php } ?>
        <h3>Paramètres</h3>
        <p>
            <input <?php if ($pageItem->getIsHomepage()) { ?>checked<?php } ?> type="checkbox" name="isHomepage" /> Page d'accueil
        </p>
        <p>
            <input <?php if ($pageItem->getIsHidden()) { ?>checked<?php } ?> type="checkbox" name="isHidden" /> Ne pas afficher dans le menu
        </p>
        <p>
            <label>Item parent</label><br>
            <select name="parent">
                <option value="">Aucun</option>
                <?php foreach ($page->getItems() as $k => $v)
                    if ($v->targetIs() == 'parent') {
                        ?>
                        <option <?php if ($v->getId() == $pageItem->getParent()) { ?>selected<?php } ?> value="<?php echo $v->getId(); ?>"><?php echo $v->getName(); ?></option>
        <?php } ?>
            </select>
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
            <label>Restreindre l'accès avec un mot de passe</label>
            <input type="password" name="_password" value="" />
        </p>
        <?php if ($pageItem->getPassword() != '') { ?>
            <p>
                <input type="checkbox" name="resetPassword" /> Retirer la restriction par mot de passe  
            </p>
    <?php } ?>
        <h3>SEO</h3>
        <p>
            <input <?php if ($pageItem->getNoIndex()) { ?>checked<?php } ?> type="checkbox" name="noIndex" /> Interdire l'indexation
        </p>
        <p>
            <label>Meta title</label>
            <input type="text" name="metaTitleTag" value="<?php echo $pageItem->getMetaTitleTag(); ?>" />
        </p>
        <p>
            <label>Meta description</label>
            <input type="text" name="metaDescriptionTag" value="<?php echo $pageItem->getMetaDescriptionTag(); ?>" />
        </p>
        <h3>Contenu</h3>
        <p>
            <label>Nom</label><br>
            <input type="text" name="name" value="<?php echo $pageItem->getName(); ?>" required="required" />
        </p>
        <p>
            <label>Titre de page</label><br>
            <input type="text" name="mainTitle" value="<?php echo $pageItem->getMainTitle(); ?>" />
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
            <textarea name="content" class="editor"><?php echo $core->callHook('beforeEditEditor', $pageItem->getContent()); ?></textarea><br>
        <?php filemanagerDisplayManagerButton(); ?>
        </p>
            <?php if ($pluginsManager->isActivePlugin('galerie')) { ?>
            <h3>Image à la une</h3>
            <p>
            <?php if (galerie::searchByfileName($pageItem->getImg())) { ?><input type="checkbox" name="delImg" /> Supprimer l'image à la une
            <?php } else { ?><label>Fichier (png, jpg, jpeg, gif)</label><br><input type="file" name="file" accept="image/*" /><?php } ?>
                <br><br>
        <?php if (galerie::searchByfileName($pageItem->getImg())) { ?><img src="<?php echo UPLOAD; ?>galerie/<?php echo $pageItem->getImg(); ?>" alt="<?php echo $pageItem->getImg(); ?>" /><?php } ?>
            </p>
    <?php } ?>
        <p>
            <button type="submit" class="button success radius">Enregistrer</button>
        </p>
    </form>
    <?php } ?>

<?php if ($mode == 'edit' && ($isLink || $pageItem->targetIs() == 'plugin')) { ?>
    <form method="post" action="index.php?p=page&amp;action=save">
    <?php show::adminTokenField(); ?>
        <input type="hidden" name="id" value="<?php echo $pageItem->getId(); ?>" />
        <!--<input type="hidden" name="position" value="<?php echo $pageItem->getPosition(); ?>" />-->
        <p>
            <input <?php if ($pageItem->getIsHidden()) { ?>checked<?php } ?> type="checkbox" name="isHidden" /> <label for="isHidden">Ne pas afficher dans le menu</label>
        </p>
        <p>
            <label>Item parent</label><br>
            <select name="parent">
                <option value="">Aucun</option>
    <?php foreach ($page->getItems() as $k => $v)
        if ($v->targetIs() == 'parent') {
            ?>
                        <option <?php if ($v->getId() == $pageItem->getParent()) { ?>selected<?php } ?> value="<?php echo $v->getId(); ?>"><?php echo $v->getName(); ?></option>
        <?php } ?>
            </select>
        </p>
        <p>
            <label>Nom</label><br>
            <input type="text" name="name" value="<?php echo $pageItem->getName(); ?>" required="required" />
        </p>
        <?php if ($pageItem->targetIs() == 'plugin') { ?>
            <p>
                <label>Cible : <?php echo $pageItem->getTarget(); ?></label>
                <input style="display:none;" type="text" name="target" value="<?php echo $pageItem->getTarget(); ?>" />
            </p>
        <?php } else { ?>
            <p>
                <label>Cible</label><br>
                <input placeholder="Example : http://www.google.com" <?php if ($pageItem->targetIs() == 'plugin') { ?>readonly<?php } ?> type="url" name="target" value="<?php echo $pageItem->getTarget(); ?>" required="required" />
            </p>
    <?php } ?>
        <p>
            <label>Ouverture</label><br>
            <select name="targetAttr">
                <option value="_self" <?php if ($pageItem->getTargetAttr() == '_self') { ?>selected<?php } ?>>Même fenêtre</option>
                <option value="_blank" <?php if ($pageItem->getTargetAttr() == '_blank') { ?>selected<?php } ?>>Nouvelle fenêtre</option>
            </select>
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

<?php if ($mode == 'edit' && $isParent) { ?>
    <form method="post" action="index.php?p=page&amp;action=save">
    <?php show::adminTokenField(); ?>
        <input type="hidden" name="id" value="<?php echo $pageItem->getId(); ?>" />
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