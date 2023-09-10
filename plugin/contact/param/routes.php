<?php

/**
 * @copyright (C) 2023, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

$router = router::getInstance();

$router->map('GET', '/contact[/?]', 'ContactController#home', 'contact-home');
$router->map('POST', '/contact/send.html', 'ContactController#send', 'contact-send');