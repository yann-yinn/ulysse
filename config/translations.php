<?php

$translations = [
  'ulysse.framework.welcome' => [
    'fr' => 'Bienvenue sur votre premiere page.  Editer le fichier "config/pages.php" pour commencer.',
    'en' => 'Welcome to your first framework page. Edit "config/pages.php" file to customize.',
  ],
  'ulysse.framework.installationTitle' => [
    'fr' => 'Bienvenue sur Ulysse',
    'en' => 'Welcome on Ulysse',
  ],
  'ulysse.framework.installationText' => [
    'fr' => 'Please rename "example.config" directory to "config" to start using framework',
    'en' => 'Renommer le fichier example.config en example pour commencer à utiliser le framework.',
  ],
];

$translations = mergeConfigFromFile($translations, 'ulysse/content/config/translations.php');

return $translations;