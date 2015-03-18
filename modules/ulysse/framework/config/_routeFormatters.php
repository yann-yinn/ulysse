<?php

$config['routesFormatters']['html'] = function($route) {
  return template(
    $route['template'],
    call_user_func_array($route['controller'], $route['controller arguments'])
  );
};

$config['routesFormatters']['json'] = function($route) {
  header('Content-Type: application/json');
  return json_encode(call_user_func_array($route['controller'], $route['controller arguments']));
};