<?php

$config['listeners']['ulysse.framework.afterBootstrap']['ulysse.content.includePhpFiles'] = [
  'title' => 'Ulysse content include files',
  'callable' => function() {
      include_once "ulysse/content/controller/contentController.php";
      include_once 'ulysse/content/api/contentApi.php';
    }
];
$config['listeners']['template.addingJavascripts']['ulysse.content.addCkeditorJs'] = [
  'callable' => function() {
      if (getCurrentRouteId() == 'ulysse.content.create' || getCurrentRouteId() == 'ulysse.content.update') {
        return '<script src="//cdn.ckeditor.com/4.4.5.1/standard/ckeditor.js"></script>';
      }
    }
];