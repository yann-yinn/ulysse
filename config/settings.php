<?php

$settings = [];

$settings['display_developper_toolbar'] = FALSE;

$settings['language_default'] = 'en';

$settings['languages'] = [
  'fr' => [
    'query' => 'fr',
  ],
  'en' => [
    'query' => 'en',
  ],
];


$settings['theme_path'] = 'themes/example';

// how to merge setting from another file :
// $settings = merge_config_file($settings, 'myvendor/mymodule/config/settings.php');

return $settings;