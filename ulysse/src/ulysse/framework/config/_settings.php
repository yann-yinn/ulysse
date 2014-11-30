<?php

$config['settings']['database'] = [
  'default' => [
    'driver' => 'sqlite',
    'sqlite_file' => APPLICATION_ROOT . '/writable/database.sqlite',
    // for mysql :
    #'host' => '127.0.0.1',
    #'name' => 'framework',
    #'user' => 'root',
    #'password' => '',
  ]
];

// theme to use to fetch requested templates.
$config['settings']['theme_path'] = FRAMEWORK_ROOT . '/themes/ulysse';
$config['settings']['admin_theme_path'] = FRAMEWORK_ROOT . '/themes/admin';

// language used by default by the framework if no language are specified
// in the http request.
$config['settings']['language_default'] = 'fr';

// enabled languages on the site
$config['settings']['languages'] = [
  'fr' => [
    // using ?language='fr' in
    'query' => 'fr',
  ],
  'en' => [
    'query' => 'en',
  ],
];

