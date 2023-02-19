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

## Fonction d'installation

function configmanagerInstall() {
    
}

## Hooks

function ConfigManagerCheckInstallFile() {
    global $administrator;
    if (file_exists(ROOT . 'install.php') && $administrator->isLogged()) {
        show::msg("<p>Le fichier install.php est toujours présent. Pour plus de sécurité, il est conseillé de le supprimer.<br/>
                Si l'installation de 299ko s'est déroulée correctement, cliquez sur le bouton ci-dessous pour le supprimer.</p>
                <a role='button' class='alert' href='index.php?p=configmanager&action=del_install&token=" . administrator::getToken() . "'>Supprimer le fichier install</a>", 'warning');
    }
}

## Code relatif au plugin
