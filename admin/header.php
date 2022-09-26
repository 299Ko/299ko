<?php
/**
 * @copyright (C) 2022, 299Ko, based on code (2010-2021) 99ko https://github.com/99kocms/
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Jonathan Coulet <j.coulet@gmail.com>
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * @author Frédéric Kaplon <frederic.kaplon@me.com>
 * @author Florent Fortat <florent.fortat@maxgun.fr>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');
?>
<!doctype html>
<html lang="fr">
    <head>
        <?php $core->callHook('adminHead'); ?>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>299ko - Administration</title>	
        <link rel="icon" href="data:image/gif;base64,R0lGODlhQABAALMAAENKWU5XaDlATTA1QFVfc0tUZTM5RDY8SUBHVVBZbFNcb0hQYT1DUUVNXVhidiwxOyH/C1hNUCBEYXRhWE1QPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS4zLWMwMTEgNjYuMTQ1NjYxLCAyMDEyLzAyLzA2LTE0OjU2OjI3ICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpDMDA4QzM0QkQ0MUMxMUU1OEVGMzhGN0Y5QzUyNThGRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpDMDA4QzM0Q0Q0MUMxMUU1OEVGMzhGN0Y5QzUyNThGRiI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkMwMDhDMzQ5RDQxQzExRTU4RUYzOEY3RjlDNTI1OEZGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkMwMDhDMzRBRDQxQzExRTU4RUYzOEY3RjlDNTI1OEZGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Af/+/fz7+vn49/b19PPy8fDv7u3s6+rp6Ofm5eTj4uHg397d3Nva2djX1tXU09LR0M/OzczLysnIx8bFxMPCwcC/vr28u7q5uLe2tbSzsrGwr66trKuqqainpqWko6KhoJ+enZybmpmYl5aVlJOSkZCPjo2Mi4qJiIeGhYSDgoGAf359fHt6eXh3dnV0c3JxcG9ubWxramloZ2ZlZGNiYWBfXl1cW1pZWFdWVVRTUlFQT05NTEtKSUhHRkVEQ0JBQD8+PTw7Ojk4NzY1NDMyMTAvLi0sKyopKCcmJSQjIiEgHx4dHBsaGRgXFhUUExIREA8ODQwLCgkIBwYFBAMCAQAAIfkEAAAAAAAsAAAAAEAAQAAABP/wyUmrvTjrzbv/YCiOZGmeaKqubOu+cCwLtCCfwuLsfIDcogZvyFPYgJxBgsjc/ZCaAjEBADQUxAMUAxgSGBUGobi1DMY7gtZyQDueZUmXB+byCp7aEYXdBTQDQxs5TD4mBkNwGAE8axdCTTtGJHM7HIw7exRKkUSKHzp+lzwAF1JDVFZ9jSKYDqUbrrAUlQ5fYW4KraQcQ7MSZzxqF208n6M7v8RDmg+1dReVeCCyG5CZFat/GYE8IacO0xjBPAYUiMaxrB8IQ+Z2grTxGq7NG+QO2xYCRPoSofmQvQpRq8CACgjc7GhQoZq6ZCE4DVkAQACAJUygSXCowZcIiZ34iBykwBHDAWYjBgBkUgBcAgslH6EkYWDBqnAC8Cl7AE7cBXwO3h2iMaFdOQtGdwi1UMvBi1U+J+DzR4HfEKopkjqwJ2eIQYQKHTBkMUAbN4w8KFpEO0SjBgOONKzkKpVtyB0jOQC4pUHrWEAribTk8dLDqQJL5RHOu6HmzQI53ezMQGQBg5EDENwcFsIAUQlaE2cwEDYk5xNQQRhwFTJB3BJa6WoQwJoIAQCMS5TtUeIAANYBcHOA22FuHDl8M/g9LuGw6K6LmT+ofBmYZi+vt5C+i126hNXcHbj2XrW2F+HkK/gGjj69+/fw48ufT7++/fv48+uXEQEAOw==">
        <?php show::linkTags(); ?>
        <link rel="stylesheet" href="styles.css" media="all">
        <?php show::scriptTags(); ?>
        <script type="text/javascript" src="scripts.js"></script>
        <?php $core->callHook('endAdminHead'); ?>	
    </head>
    <body>
        <div id="container">
            <div id="header">
                <div id="header_content">	
                    <ul>
                        <li><h1><a href="javascript:" id="open_nav"></a></h1></li>
                        <li><a target="_blank" href="../">Voir le site</a></li>
                        <li><a href="index.php?action=logout&token=<?php echo administrator::getToken(); ?>">Déconnexion</a></li>
                    </ul>
                </div>
            </div>
            <div id="alert-msg">
                <?php show::displayMsg(); ?>
            </div>
            <div id="body">
                <div id="content_mask">
                    <div id="content" class="<?php echo $runPlugin->getName(); ?>-admin">
                        <div id="sidebar">
                            <ul id="navigation">
                                <?php
                                foreach ($pluginsManager->getPlugins() as $k => $v)
                                    if ($v->getConfigVal('activate') && $v->getAdminFile() && $v->getIsDefaultAdminPlugin()) {
                                        ?>
                                        <li><a href="index.php?p=<?php echo $v->getName(); ?>"><?php echo $v->getInfoVal('name'); ?></a></li>
                                        <?php
                                    }
                                foreach ($pluginsManager->getPlugins() as $k => $v)
                                    if ($v->getConfigVal('activate') && $v->getAdminFile() && !$v->getIsDefaultAdminPlugin()) {
                                        ?>
                                        <li><a href="index.php?p=<?php echo $v->getName(); ?>"><?php echo $v->getInfoVal('name'); ?></a></li>
                                        <?php
                                    }
                                ?>
                                <li class="site"><a href="index.php?action=logout&token=<?php echo administrator::getToken(); ?>">Déconnexion</a></li>
                                <li class="site"><a target="_blank" href="../">Voir le site</a></li>
                            </ul>
                            <p class="just_using">
                                <a target="_blank" href="https://github.com/299ko/">Just using 299ko <?php echo VERSION; ?></a>
                            </p>
                        </div>
<?php if ($runPlugin->getParamTemplate()) { ?>
                            <a title="Paramètres" data-fancybox id="param_link" href="#" data-src="#param_panel"><i class="fa-solid fa-screwdriver-wrench"></i></a>
                            <div id="param_panel">
                                <div class="content">
                                    <h2>Paramètres</h2>
    <?php include($runPlugin->getParamTemplate()); ?>
                                </div>
                            </div>
                        <?php } ?>
<?php if ($runPlugin->getHelpTemplate()) { ?>
                            <div id="help_panel">
                                <div class="content">
                                    <h2>Aide</h2>
    <?php include($runPlugin->getHelpTemplate()); ?>
                                </div>
                            </div>
                            <a title="Aide" data-fancybox id="help_link" href="#" data-src="#help_panel"><i class="fa-solid fa-circle-question"></i></a>
                            <?php }
                            core::executeHookAction('adminToolsTemplates', [$runPlugin->getName()]);
                            ?>
                        <h2><?php echo $runPlugin->getInfoVal('name'); ?></h2>