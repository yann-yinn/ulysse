<?php
/**
 * Define pages url and content.
 */

$routes = [];

$routes['homepage'] = [
    'path'     => '',
    'template' => 'layout.php',
    'content'  =>  template('homepage.php'),
];
$routes['contact'] = [
  'path'     => 'contact',
  'template' => 'layout.php',
  'content'  =>  template('contact.php')
];
$routes['hello'] = [
  'path'     => 'hello',
  'template' => 'layout.php',
  'content'  =>  "bonjour monde",
];

// how to merge setting from another file :
// $routes = merge_config_from_file($routes, 'myvendor/mymodule/config/routes.php');

return $routes;