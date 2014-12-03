<?php

// list all site contents
$config['pages']['ulysse.content.list'] = [
  'path' => 'admin',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function() {
      return contentListPage();
    }
];

// display an add / edit content form
$config['pages']['ulysse.content.create'] = [
  'path' => 'admin/content/create',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function() {
      return contentFormPage();
    }
];

// display an add / edit content form
$config['pages']['ulysse.content.edit'] = [
  'path' => 'admin/content/edit',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function() {
      return contentFormPage();
    }
];

// save content to database
$config['pages']['ulysse.content.save'] = [
  'path' => 'admin/content/save',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function () {
      return contentFormSavePage();
    }
];

// confirm deletion
$config['pages']['ulysse.content.confirmDeletion'] = [
  'path' => 'admin/content/delete/confirm',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function () {
      return contentDeleteConfirmPage();
    }
];

// confirm deletion
$config['pages']['ulysse.content.delete'] = [
  'path' => 'admin/content/delete',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function () {
      return contentDeletePage();
    }
];


