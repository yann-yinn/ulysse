<?php



// list all site contents
$config['routes']['ulysse.user.create'] = [
  'path' => 'admin/user/create',
  'theme' => getSetting('theme_admin'),
  'layout' => 'page.php',
  'callable' => function() {
      return userCreateController();
    }
];

// list all site contents
$config['routes']['ulysse.user.save'] = [
  'path' => 'admin/user/save',
  'theme' => getSetting('theme_admin'),
  'layout' => 'page.php',
  'callable' => function() {
      return userFormSaveController();
    }
];