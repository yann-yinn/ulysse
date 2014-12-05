<?php

$config['listeners']['ulysse.framework.afterBootstrap']['ulysse.user.includePhpFiles'] = [
  'title' => 'Ulysse content include files',
  'callable' => function() {
      include_once "ulysse/user/controller/userController.php";
      include_once 'ulysse/user/api/userApi.php';
      include_once 'ulysse/user/dataValidator/userDataValidator.php';
    }
];
