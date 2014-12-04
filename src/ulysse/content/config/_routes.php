<?php

// list all site contents
$config['routes']['ulysse.content.list'] = [
  'path' => 'admin',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function() {
      return contentListController();
    }
];

// display an add / edit content form
$config['routes']['ulysse.content.create'] = [
  'path' => 'admin/content/create',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function() {
      return contentCreateController();
    }
];

// display an add / edit content form
$config['routes']['ulysse.content.update'] = [
  'path' => 'admin/content/edit',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function() {
      return contentUpdateController();
    }
];

// save content to database
$config['routes']['ulysse.content.save'] = [
  'path' => 'admin/content/save',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function () {
      return contentFormSaveController();
    }
];

// confirm deletion
$config['routes']['ulysse.content.confirmDeletion'] = [
  'path' => 'admin/content/delete/confirm',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function () {
      return contentDeleteConfirmController();
    }
];

// confirm deletion
$config['routes']['ulysse.content.delete'] = [
  'path' => 'admin/content/delete',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function () {
      return contentDeleteController();
    }
];


