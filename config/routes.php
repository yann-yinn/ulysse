<?php

$routes['homepage'] = [
    'path' => '',
    'return' => getStaticContent('homepage/homepage.html'),
];


// how to merge setting from another file :
$routes = merge_config_file($routes, 'okc/simulateur/config/routes.php');

return $routes;