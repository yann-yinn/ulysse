<?php

$settings = [];

$settings['language_default'] = 'en';

$settings['languages'] = [
  'fr' => [
    'query' => 'fr',
  ],
  'en' => [
    'query' => 'en',
  ],
];

$settings['display_developper_toolbar'] = FALSE;

$settings['theme_path'] = 'themes/default';

// how to merge setting from another file :
$settings = merge_config_file($settings, 'okc/simulateur/config/settings.php');

return $settings;