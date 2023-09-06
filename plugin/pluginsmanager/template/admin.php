<?php
defined('ROOT') OR exit('No direct script access allowed');
include_once(ROOT . 'admin/header.php');
?>
<section>
    <header>Liste des plugins</header>
    <form method="post" action=".?p=pluginsmanager&action=save" id="pluginsmanagerForm">
        <?php show::adminTokenField(); ?> 
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Priorité</th>
                    <th>Activer</th>
                </tr>
            </thead>
            <tbody>			  	
                <?php foreach ($pluginsManager->getPlugins() as $plugin) { ?>
                    <tr>
                        <td>
                            <?php echo $plugin->getInfoVal('name'); ?>
                            <?php if ($plugin->getInfoVal('version') != 'none') { ?> (version <?php echo $plugin->getInfoVal('version'); ?>)<?php } ?> : <?php echo $plugin->getInfoVal('description'); ?>
                            <?php if ($plugin->getConfigVal('activate') && !$plugin->isInstalled()) { ?><p><a class="button" href=".?p=pluginsmanager&action=maintenance&plugin=<?php echo $plugin->getName(); ?>&token=<?php echo administrator::getToken(); ?>">Maintenance requise</a><?php } ?></p>
                        </td>
                        <td>
                            <select name="priority[<?php echo $plugin->getName(); ?>]" onchange="document.getElementById('pluginsmanagerForm').submit();">
                                <?php foreach ($priority as $k => $v) { ?>
                                    <option <?php if ($plugin->getconfigVal('priority') == $v) { ?>selected<?php } ?> value="<?php echo $v; ?>"><?php echo $v; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td>
                            <?php if (!$plugin->isRequired()) { ?>
                                <input onchange="document.getElementById('pluginsmanagerForm').submit();" id="activate[<?php echo $plugin->getName(); ?>]" type="checkbox" name="activate[<?php echo $plugin->getName(); ?>]" <?php if ($plugin->getConfigVal('activate')) { ?>checked<?php } ?> />
                            <?php } else { ?>
                                <input style="display:none;" id="activate[<?php echo $plugin->getName(); ?>]" type="checkbox" name="activate[<?php echo $plugin->getName(); ?>]" checked />
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>					
        </table>
    </form>
</section>
<?php include_once(ROOT . 'admin/footer.php'); ?>