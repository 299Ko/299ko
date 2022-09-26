<?php

/**
 * @copyright (C) 2022, 299Ko
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3
 * @author Maxence Cauderlier <mx.koder@gmail.com>
 * 
 * @package 299Ko https://github.com/299Ko/299ko
 */
defined('ROOT') OR exit('No direct script access allowed');

core::registerHookAction("adminToolsTemplates", "categoriesAdminToolsTemplates");
core::registerHookAction("adminEditingAnItem", "categoriesAdminEditingAnItem");
core::registerHookAction("adminOnSaveItem", "categoriesAdminOnSaveItem");