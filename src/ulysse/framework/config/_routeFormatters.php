<?php

$config['routesFormatters']['html'] = function($route) {
  return template($route['template'], $route['datas']());
};


$config['routesFormatters']['json'] = function($route) {
  return json_encode($route['datas']());
};