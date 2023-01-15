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
define('VERSION', '1.0');
define('COMMON', ROOT . 'common/');
define('DATA', ROOT . 'data/');
define('UPLOAD', ROOT . 'data/upload/');
define('DATA_PLUGIN', ROOT . 'data/plugin/');
define('THEMES', ROOT . 'theme/');
define('PLUGINS', ROOT . 'plugin/');
define('ADMIN_PATH', ROOT . 'admin/');
define('FONTICON', 'assets/bootstrap-icons/bootstrap-icons.css');
define('FANCYCSS', "assets/fancyapps/fancybox.css");
define('COMMONJS', "assets/js/common.js");
define("FANCYJS", "assets/fancyapps/fancybox.umd.js");
if (file_exists(DATA . 'key.php'))
    include(DATA . 'key.php');