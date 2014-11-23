<?php

$pages = [];

require "ulysse/content/controllers/contentController.php";
require_once 'ulysse/content/api/contentApi.php';

// list all site contents
$pages['ulysse.content.list'] = [
  'path' => 'admin/content',
  'theme' => 'admin',
  'layout' => 'page.php',
  'content' => function() {
      return contentListPage();
    }
];

// display an add / edit content form
$pages['ulysse.content.form'] = [
  'path' => 'admin/content/form',
  'theme' => 'admin',
  'layout' => 'page.php',
  'layout_variables' => [
    'head' => function() {
        //return '<script src="//cdn.ckeditor.com/4.4.5.1/standard/ckeditor.js"></script>';
      },
  ],
  'content' => function() {
      return contentFormPage();
    }
];

// save content to database
$pages['ulysse.content.form.save'] = [
  'path' => 'admin/content/form/save',
  'theme' => 'admin',
  'layout' => 'page.php',
  'content' => function () {
      return contentFormSavePage();
    }
];

return $pages;