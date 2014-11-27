<?php

$config = [];

include "settings.php";

if (is_readable(getConfigDirectoryPath() . '/settings.local.php'))
{
  include getConfigDirectoryPath() . '/settings.local.php';
}

include "translations.php";
include "listeners.php";
include "pages.php";

include('ulysse/content/config/config.php');



// register config variables into $config variable.
$config['settings'] = $settings;
$config['translations'] = $translations;
$config['pages'] = $pages;
$config['listeners'] = $listeners;




