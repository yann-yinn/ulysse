<?php

$config = [];

include "settings.php";
include "translations.php";
include "listeners.php";
include "pages.php";

// include your module configuration here
include('ulysse/content/config/config.php');

if (is_readable(getConfigDirectoryPath() . '/config.local.php'))
{
  include getConfigDirectoryPath() . '/config.local.php';
}







