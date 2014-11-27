<?php

// list all site contents
$pages['ulysse.content.list'] = [
  'path' => 'admin/content',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function() {
      return contentListPage();
    }
];

// display an add / edit content form
$pages['ulysse.content.form'] = [
  'path' => 'admin/content/form',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function() {
      return contentFormPage();
    }
];

// save content to database
$pages['ulysse.content.form.save'] = [
  'path' => 'admin/content/form/save',
  'theme' => 'admin',
  'layout' => 'page.php',
  'callable' => function () {
      return contentFormSavePage();
    }
];

