<?php

$translations = [
  'okc.framework.welcome.homepage' => [
    'fr' => 'Bienvenue sur votre premiere page.  Editer le fichier ' . PAGES_FILEPATH . ' pour commencer.',
    'en' => 'Welcome to your first framework page. Edit ' . PAGES_FILEPATH . ' file to customize.',
  ],
];

$translations = mergeConfigFromFile($translations, 'okc/content/config/translations.php');

return $translations;