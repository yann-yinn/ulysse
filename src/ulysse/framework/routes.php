<?php

function getCurrentRouteId() {
  $routes = getConfig('routes');
  $path = getCurrentPath();
  foreach ($routes as $id => $route) {
    if (isset($route['path']) && $route['path'] == $path) {
      return $id;
    }
  }
}

function isCurrentRoute($routeId) {
  return $routeId == getCurrentRouteId();
}

function currentRouteIsParentOf($parentRouteId) {
  return routeIsParentOf($parentRouteId, getCurrentRouteId());
}

function getRouteIdFromPath($path) {
  $routes = getConfig('routes');
  foreach ($routes as $id => $route) {
    if (isset($route['path']) && $route['path'] == $path) {
      return $id;
    }
  }
}

function routeIsParentOf($supposedParentRouteId, $routeId) {
  $route = getRouteById($routeId);
  if (isset($route['parent']))
  {
    if ($route['parent'] == $supposedParentRouteId)
    {
      return TRUE;
    }
    else
    {
      return routeIsParentOf($supposedParentRouteId, $route['parent']);
    }
  }
  return FALSE;
}

/**
 * Return a page by its key
 * @see config/_routes.php file.
 * @param string $routeId
 * @return array : the page definition as an array
 */
function getRouteById($routeId)
{
  $routes = getConfig('routes');
  $route = $routes[$routeId];
  $route['id'] = $routeId;
  return $route;
}

/**
 * Render a page using its path.
 * @see $config['routes']
 * @param string $path
 * @return string (html, json, xml or whatever the controller return to us.)
 */
function renderRouteByPath($path)
{
  $routes = getConfig('routes');
  $route = getRouteDeclarationByPath($path, $routes);
  if (!$route)
  {
    $route = getRouteById('__HTTP_404__', $routes);
  }
  $output = renderRoute($route);
  return $output;
}

/**
 * If there is several routes, last found route will be used.
 * @see config/_routes.php
 * @param $path
 * @param $routes
 * @return array : page declaration
 */
function getRouteDeclarationByPath($path, $routes)
{
  $route = [];
  foreach ($routes as $id => $datas)
  {
    if (isset($routes[$id]['path']) && $routes[$id]['path'] == $path)
    {
      $route = $routes[$id];
      $route['id'] = $id;
    }
  }
  return $route;
}

/**
 * Build an url suitable for href, but using a pageId to retrieve
 * the requested path. This way, you may change paths without breaking html links.
 * @param $routeId
 * @param string $queryString
 * @return string
 */
function href($routeId, $queryString = '') {
  $routes = getConfig('routes');
  $route = $routes[$routeId];
  return url($route['path'], $queryString);
}

/**
 * Render a page, parsing a page definition
 * @see $config['routes']
 * @param array $route : page array declaration as returned by getPageByPath() or getPageByKey()
 * @return bool
 */
function renderRoute(array $route)
{

  $output = getRoutePropertyValue($route['callable']);

  if (!empty($route['layout'])) {
    $layoutVariables = [];

    if (!empty($route['layout_variables'])) {
      $layoutVariables = $route['layout_variables'];
    }
    $layoutVariables['content'] = $output;
    if (!empty($route['theme'])) {
      $themePath = getThemePath($route['theme']);
    }
    else {
      $themePath = getThemePath(getSetting('theme'));
    }
    $output = template($route['layout'], $layoutVariables, $themePath);
  }
  return $output;
}

/**
 * Page properties might be strings or closure.
 * @param $property
 * @return string
 */
function getRoutePropertyValue($property) {
  return is_string($property) ? $property : $property();
}

function buildAutoRedirectionQueryString() {
  return 'redirection=' . getRouteIdFromPath(getCurrentPath());
}