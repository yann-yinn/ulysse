<?php

$pages = [];

require "okc/content/controllers/contentController.php";
require_once 'okc/content/api/contentApi.php';

$pages['content_form'] = [
  'path' => 'admin/content/form',
  'template' => 'adminPage.php',
  'content' => function() {
      return contentFormPage();
    }
];

$pages['content_form_save'] = [
  'path' => 'admin/content/form/save',
  'template' => 'adminPage.php',
  'content' => function () {
      return contentFormSavePage();
    }
];

return $pages;