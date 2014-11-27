<?php

$listeners['ulysse.framework.afterBootstrap'][] = [
  'title' => 'Ulysse content include files',
  'callable' => function() {
      // include files needed by our content module.
      include_once "ulysse/content/controllers/contentController.php";
      include_once 'ulysse/content/api/contentApi.php';
    }
];
$listeners['ulysse.framework.javascripts'][] = [
  'callable' => function() {
      return '<script src="//cdn.ckeditor.com/4.4.5.1/standard/ckeditor.js"></script>';
    }
];