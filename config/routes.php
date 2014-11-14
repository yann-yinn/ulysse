<?php

$routes['homepage'] = [
    'path'   => '',
    'template' => 'layout.php',
    'return' =>  template('homepage.php')
];
$routes['contact'] = [
  'path'   => 'contact',
  'template' => 'layout.php',
  'return' =>  template('contact.php')
];


// how to merge setting from another file :
// $routes = merge_config_file($routes, 'myvendor/mymodule/config/routes.php');

return $routes;