<?php

$listeners['ulysse.framework.afterBootstrap']['ulysse.content.afterBootsrap'] = [
  'callable' => function() {
      // include files needed by our content module.
      include_once "ulysse/content/controllers/contentController.php";
      include_once 'ulysse/content/api/contentApi.php';
    }
];