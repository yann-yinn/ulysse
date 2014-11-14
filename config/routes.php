<?php

$routes['homepage'] = [
    'path' => '',
    'return' => getTranslation('okc.framework.welcome.homepage'),
];


// how to merge setting from another file :
// $routes = merge_config_file($routes, 'myvendor/mymodule/config/routes.php');

return $routes;