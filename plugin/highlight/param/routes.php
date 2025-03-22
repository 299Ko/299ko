<?php

/**
 * @copyright (C) 2024, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

$router = router::getInstance();

$router->map('GET', '/admin/highlight[/?]', 'HighlightAdminController#home', 'highlight-admin');
$router->map('POST', '/admin/highlight/saveconf[/?]', 'HighlightAdminController#save', 'highlight-saveconf');