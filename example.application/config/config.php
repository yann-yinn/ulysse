<?php
/**
 * This config files contains ALL configuration of your application :
 * $conf['routes'] : pages of your site
 * $conf['settings'] : settings
 * $conf['listeners'] : Events listeners
 * $conf['translations'] : String translations
 *
 * You may easily include all configuration of a custom module
 * including here only its config file. This way, commenting
 * this include is equivalent to "disabling" your module
 * has its pages, listeners and such won't be used anymore by the framework.
 */

// enable framework module.
include 'ulysse/framework/config/config.php';
// enable content module.
include 'ulysse/content/config/config.php';

// include global config files for the site.
include '_settings.php';
include '_translations.php';
include '_listeners.php';
include '_routes.php';


// import custom module configuration here :
// include 'myname/mymodule/config/config.php';






