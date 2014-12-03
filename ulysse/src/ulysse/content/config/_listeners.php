<?php

$config['listeners']['ulysse.framework.afterBootstrap']['ulysse.content.includePhpFiles'] = [
  'title' => 'Ulysse content include files',
  'callable' => function() {
      include_once "ulysse/content/controllers/contentController.php";
      include_once 'ulysse/content/api/contentApi.php';
    }
];
$config['listeners']['ulysse.framework.javascripts']['ulysse.content.addCkeditorJs'] = [
  'callable' => function() {
      if (getCurrentPath() == 'admin/content/form') {
        return '<script src="//cdn.ckeditor.com/4.4.5.1/standard/ckeditor.js"></script>';
      }
    }
];