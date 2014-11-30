<?php
/**
 * This config files contains ALL configuration of your application :
 * $conf['pages'] : pages of your site
 * $conf['settings'] : settings
 * $conf['listeners'] : Events listeners
 * $conf['translations'] : String translations
 *
 * You may easily include all configuration of a custom module
 * including here only its config file. This way, commenting
 * this include is equivalent to "disabling" your module
 * has its pages, listeners and such won't be used anymore by the framework.
 */

$config = [];

// enable framework. You should _really_ not remove this.
include 'ulysse/framework/config/config.php';

// enable content module
include 'ulysse/content/config/config.php';

// import custom module configuration here :
// include 'myname/mymodule/config/config.php';






