<?php

$settings = [
  'display_developper_toolbar' => FALSE,
  'theme_path' => 'themes/example',
  'language_default' => 'en',
  'languages' =>   [
    'fr' => [
      'query' => 'fr',
    ],
    'en' => [
      'query' => 'en',
    ],
  ]
];

// example merge setting from another file :
// $settings = merge_config_from_file($settings, 'myvendor/mymodule/config/settings.php');

return $settings;