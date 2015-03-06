<?php

// Default enabled theme. Templates will be fetched in this directory.
$config['settings']['theme'] = 'example';
$config['settings']['theme_admin'] = 'admin';

// Language used by default by the framework
// if no language are specified in the http request.
$config['settings']['language_default'] = 'fr';

// Enabled languages on the site
$config['settings']['languages'] = [
  'fr' => [
    // using ?language='fr' in
    'query' => 'fr',
  ],
  'en' => [
    'query' => 'en',
  ],
];

