<?php
/**
 * PHP Ulysse framework core files.
 */

// relative paths from main index.php file (yourapp/www/index.php)
// to find application directory path and ulysse directory path.
// Those constants are first defined in index.php file.
if (!defined('APPLICATION_ROOT')) {
  define('APPLICATION_ROOT', '..');
}
if (!defined('ULYSSE_ROOT')) {
  define('ULYSSE_ROOT', '../..');
}
define('APPLICATION_THEMES_DIRECTORY_PATH', APPLICATION_ROOT . '/themes');
define('APPLICATION_CONFIG_DIRECTORY_PATH', APPLICATION_ROOT . '/config');

/**
 * Run ulysse framework : map an url to a php controller.
 *
 * @param array $contextVariables : array of values to define site context
 * Use this bootstrap in a script in "www" directory with following example code :
 * @code
 * require_once "../src/ulysse/framework/core.php";
 * startFramework();
 * @endocde
 */
function startFramework($contextVariables = []) {

  addPhpIncludePaths([
      ULYSSE_ROOT . '/src',
      APPLICATION_ROOT . '/src',
      APPLICATION_ROOT . '/vendors',
    ]);

  // register a PSR0 class to allow autoloading for vendors and custom code.
  registerPsr0ClassAutoloader();

  fireEvent('ulysse.framework.beforeBootstrap');

  // register context variables in the application context
  setContextVariable('time_start', microtime(TRUE));
  foreach ($contextVariables as $key => $contextVariable) {
    setContextVariable($key, $contextVariable);
  }

  fireEvent('ulysse.framework.afterBootstrap');

  // executing our controller and return output to the browser
  echo renderRouteByPath(getCurrentPath());

  // display developper informations.
  if (getSetting('ulysse.framework.displayDevelopperToolbar') === TRUE) {
    require_once "ulysse/framework/developperToolbar.php";
  }

  exit;

}

/**
 * Return base path, if framework is installed in a subfolder of the host
 *
 * @return string
 */
function getBasePath() {
  $scriptNamePath = getServerScriptNamePath();
  $scriptName = getServerScriptName($scriptNamePath);
  return _getBasePath($scriptName, $scriptNamePath);
}

/**
 * Extract a path usable by the framework from an incoming http request.
 * Heart of the framework routing system.
 *
 * @return string :
 * For "http://localhost/ulysse/www/public/index.php/hello/world" returns "hello/world"
 * For "http://ulysse.local/index.php/hello/world" returns "hello/world"
 * For "http://ulysse.local/index.php/hello/world?test=value" returns "hello/world".
 *
 * This path is then fetched in $config['routes'] array. If a matching route is found,
 * route controller will be executed.
 */
function getCurrentPath() {

  static $path = null;

  if ($path) {
    return $path;
  }

  // "http://localhost/ulysse/www/public/index.php/admin/content/form" > "/ulysse/www/public/index.php"
  $scriptNamePath = getServerScriptNamePath();

  // "/ulysse/www/public/index.php" >  "index.php"
  $scriptName = getServerScriptName($scriptNamePath);

  // "/ulysse/www/public/index.php" > "/ulysse/www/public/"
  $basePath = _getBasePath($scriptName, $scriptNamePath);

  //  "http://localhost/ulysse/www/public/index.php/admin/content/form" > "/ulysse/www/public/index.php/admin/content/form"
  $serverRequestUri = getServerRequestUri();

  // "/ulysse/www/public/index.php/admin/content/form" > "index.php/admin/content/form"
  $serverRequestUriWihoutBasePath = _removeBasePathFromServerRequestUri($serverRequestUri, $basePath);

  // "index.php/admin/content/form" >  "/admin/content/form"
  $path = _removeScriptNameFromPath($serverRequestUriWihoutBasePath, $scriptName);

  // "/admin/content/form" > "admin/content/form"
  $path = _removeTrailingSlashFromPath($path);

  return $path;
}

/**
 * Get framework script entry point, usually "index.php"
 * @return string
 */
function getServerEntryPoint() {
  return getServerScriptName(getServerScriptNamePath());
}

/**
 * Get all site Logs
 * @return array : all site logs
 */
function getAllLogs() {
  return $GLOBALS['_LOGS'];
}

/**
 * Get current language used on the site by the visitor
 * @return string : langcode (fr, en etc...)
 */
function getCurrentLanguage() {
  $currentLanguage = getSetting('language_default');
  if (isset($_REQUEST['language'])) {
    $requestedLanguage = (string)sanitizeValue($_REQUEST['language']);
    $definedLanguages = getSetting('languages');
    foreach ($definedLanguages as $id => $datas) {
      if ($definedLanguages[$id]['query'] == $requestedLanguage) {
        $currentLanguage = $requestedLanguage;
      }
    }
  }
  return $currentLanguage;
}

/**
 * Return value of a site setting
 * @see config/_settings.php file.
 * @param string $key = settings identifier
 * @return mixed
 */
function getSetting($key) {
  $settings = getConfig('settings');
  if (isset($settings[$key])) {
    return $settings[$key];
  }
}

/**
 * Fire a html Dom Event
 * @param string $event_id
 * @return string
 */
function fireDomEvent($event_id) {
  $returns = fireEvent($event_id);
  return implode("\r\n", $returns);
}

/**
 * @param string $event_id : event id
 * @return array all returns by all executed listeners
 */
function fireEvent($event_id) {
  $listeners = getConfig('listeners');
  $returns = [];
  if (isset($listeners[$event_id])) {
    foreach($listeners[$event_id] as $listener_id => $listener) {
      $return = executeListener($listener);
      $returns[] = $return;
      writeLog(['detail' => "Executing '$listener_id' listener for event '$event_id' : listener returned : " . var_export($return, TRUE)]);
    }
    return $returns;
  }
  else {
    writeLog(['detail' => 'No listeners found for ' . $event_id . ' event']);
    return FALSE;
  }
}

/**
 * @param array $listener with a "callable" key which is a closure.
 * @return mixed
 */
function executeListener($listener) {
  return $listener['callable']();
}

/**
 * Return full context for the current framework response to the http request.
 * @return array
 */
function getContext() {
  return $GLOBALS['_CONTEXT'];
}

/**
 * Get a context Variable by its key
 * @param string $key
 * @return mixed
 */
function getContextVariable($key) {
  return $GLOBALS['_CONTEXT'][$key];
}

/**
 * @param string $type : 'routes', 'listeners' etc ...
 * @return mixed
 */
function getConfig($type = null) {
  static $config = [];
  if (!$config) {
    include APPLICATION_CONFIG_DIRECTORY_PATH . '/config.php';
    // special local config file.
    if (is_readable(APPLICATION_CONFIG_DIRECTORY_PATH  . '/config.local.php')) {
      include APPLICATION_CONFIG_DIRECTORY_PATH  . '/config.local.php';
    }
  }
  if ($type && isset($config[$type])) {
    return $config[$type];
  }
  return $config;
}

/**
 * Get a translation for a specific string_id
 * @param $string_id
 * @param string $language
 * @return string : localized string
 */
function t($string_id, $language = NULL) {
  return getTranslation($string_id, $language = NULL);
}

/**
 * Get a translation for a specific string_id
 * @param $string_id
 * @param string $language
 * @return string : localized string
 */
function getTranslation($string_id, $language = NULL) {
  $translations = getConfig('translations');
  if (!$language) $language = getCurrentLanguage();
  return $translations[$string_id][$language];
}

/**
 * Build an url from a routeId, suitable for an href html attribute.
 *
 * @param string $routeId : routeId. e.g: "helloWorld"
 * @param string $queryString
 * @return string
 * e.g : "/ulysse/example.app/www/default/index.php/hello-world"
 */
function buildUrl($routeId, $queryString = '') {
  $routes = getConfig('routes');
  // get the route definition by its key identifier.
  $route = $routes[$routeId];
  return buildUrlFromPath($route['path'], $queryString);
}

/**
 * Build a full url from an ulysse path.
 * @param string $path : e.g "hello/world"
 * @param string $queryString : e.g "value=4&test=true&redirection=contact"
 * @return string : full relative url suitable to build an href html attribute.
 * e.g : "/ulysse/example.app/www/default/index.php/hello-world"
 */
function buildUrlFromPath($path, $queryString = '') {
  $queryArray = [];
  if ($queryString) parse_str($queryString, $queryArray);
  $queryString = http_build_query($queryArray);
  if (getSetting('cleanUrls') == FALSE) {
    $url = sanitizeValue(getBasePath() . getServerEntryPoint() . '/' . $path);
  }
  else {
    $url = sanitizeValue(getBasePath() . $path);
  }
  if ($queryString) $url .= '?' . sanitizeValue($queryString);
  return $url;
}

/**
 * Return TRUE if $path is the current http requested path, FALSE otherwise.
 * Usefull to set "active" classes in html, for example for menus.
 * @param string $path
 * @return bool
 */
function pathIsActive($path) {
  return $path ==  getCurrentPath() ? TRUE : FALSE;
}

/**
 * Return true if the route is the currently active route.
 *
 * @param $routeId
 * @return bool
 */
function routeIsActive($routeId) {
  $routes = getConfig('routes');
  $route = $routes[$routeId];
  return pathIsActive($route['path']);
}

/**
 * Return full relative path to a theme inside themes directory.
 * @param string $theme
 * @return bool|string
 */
function getThemePath($theme) {
  $themePath = FALSE;
  if(file_exists(APPLICATION_THEMES_DIRECTORY_PATH . DIRECTORY_SEPARATOR . $theme)) {
    $themePath = APPLICATION_THEMES_DIRECTORY_PATH . DIRECTORY_SEPARATOR . $theme;
  }
  return $themePath;
}

/**
 * Write a log
 *
 * @param array $log
 * associative array containing the following keys
 * - level : notice, warning, error
 * - detail : detail of the log
 */
function writeLog($log) {
  $GLOBALS['_LOGS'][] = $log;
}

/**
 * Add an http response code header, using http or https for the sheme
 * @param int $code : http response code 200, 400 etc...
 * @param $message : message associated to the http response code
 * @param $protocol (
 */
function setHttpResponseCode($code, $message = null, $protocol = null) {
  // most common response code and their associated messages.
  $codesMessages =
    [
      200 => 'OK',
      201 => 'Created',
      301 => 'Moved Permanently',
      302 => 'Moved Temporarily',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      418 => 'I’m a teapot',
      500 => 'Internal Server Error',
      503 => 'Service Unavailable',
    ];
  $protocol = $protocol ? $protocol : getServerProtocol();
  $message  = $message ? $message : $codesMessages[$code];
  header(sprintf("%s %s %s", $protocol, sanitizeValue($code), sanitizeValue($message)));
}

/**
 * Set or add a value to the context
 * @param string $key
 * @param mixed $value
 */
function setContextVariable($key, $value) {
  $GLOBALS['_CONTEXT'][$key] = $value;
}

/**
 * Register a basic psr0 class autoloader.
 */
function registerPsr0ClassAutoloader() {
  spl_autoload_register(function($class){require_once str_replace('\\','/', $class).'.php';});
}

/**
 * Sanitize a variable, to display it, encoding html.
 * @param string $value
 * @return string html encoded value
 */
function sanitizeValue($value) {
  return _sanitizeValue($value);
}

/**
 * Render a specific template file
 *
 * First look for a file inside the currently active theme.
 *
 * @param string $templatePath : file path. e.g : ock/content/template/mytemplate.php
 * @param array $variables
 * @param string $themePath : search first template file in this directory.
 * may be defined. A theme is a collection of template.
 * @return string
 */
function template($templatePath, $variables = [], $themePath = null) {
  // content.php
  $output = FALSE;
  $searchPaths = [];
  if ($themePath)
  {
    $searchPaths[] = $themePath . DIRECTORY_SEPARATOR . $templatePath;
  }
  // @FIXME template should be fetched from currently active theme.
  // we should not search first in active theme adn then in admin theme
  $searchPaths[] = getThemePath(getSetting('theme')) . DIRECTORY_SEPARATOR . $templatePath;
  $searchPaths[] = getThemePath(getSetting('theme_admin')) . DIRECTORY_SEPARATOR . $templatePath;
  $searchPaths[] = $templatePath;
  foreach ($searchPaths as $path)
  {
    $output = @_template($path, $variables);
    if ($output) break;
  }
  if (!$output)
  {
    writeLog(['level' => 'warning', 'detail' => sprintf("%s template is not readable or does not exist", sanitizeValue($path))]);
  }
  else
  {
    writeLog(['level' => 'notification', 'detail' => sprintf('Template "%s" rendered. ', sanitizeValue($path))]);
  }
  return $output;
}

/**
 * @param string $path : path to the template file
 * @param array $variables
 * @return string : template parsed with variables, ready to be printed
 */
function _template($path, $variables = []) {
  if ($variables) extract($variables);
  ob_start();
  include $path;
  return ob_get_clean();
}

/**
 * Echo a value in a secured /escaped way.
 * Do not use "print" or "echo" in template when possible, as
 * this function take care of encoding malicious entities.
 *
 * @param string $value : a single string value to print.
 * @param array | string  $formatters : array of function names to format the value.
 * Special formatter "raw" may be used to disabled default escaping of the value.
 * @return string
 */
function e($value, $formatters = []) {
  // sanitize value string by default unless "raw" special formatter name is requested
  $output = ($formatters != "raw" || !in_array('raw', $formatters)) ? $value : sanitizeValue($value);

  if ($formatters) {
    // if formatters is a string, apply it directly and echo string :
    if (is_string($formatters)) {
      $output = $formatters($output);
    }
    // if formatters is an array, apply each formatter to the string :
    else {
      foreach ($formatters as $function) {
        if ($function != "raw") $output = $function($output);
      }
    }
  }
  echo $output;
}

function getFullDomainName() {
  return _getUrlScheme() . '://' . getServerName();
}

function setHttpRedirection($routeId) {
  $url = sanitizeValue(buildUrl($routeId));
  _setHttpRedirection(getFullDomainName() . $url);
}

/**
 * Http Redirection to specified path.
 * If path is not specified, function will look
 * into the url for a GET "redirection" query param, and will use
 * it as the redirection path.
 * @param string $routeId : pageId identifier
 *
 */
function redirection($routeId = NULL) {
  if (is_null($routeId)) {
    $routeId = getRedirectionFromUrl();
  }
  setHttpRedirection($routeId);
  exit;
}

/* ====================
   HELPERS
   ==================== */

/**
 * Core atoms
 * @param array $attributes
 * @return string
 */
function setHtmlAttributes(array $attributes = array()) {
  foreach ($attributes as $attribute => &$data) {
    $data = implode(' ', (array) $data);
    $data = $attribute . '="' . htmlspecialchars($data, ENT_QUOTES, 'utf-8') . '"';
  }
  return $attributes ? ' ' . implode(' ', $attributes) : '';
}

/**
 * get path to build links, href, src etc .... in template.
 * @param string $serverScriptName
 * @param string $serverScriptNamePath
 * @return string
 *   If entry point is an "index.php" file :
 *   For "localhost/ulysse/www/public/index.php" it will returns "/ulysse/www/public/"
 *   For "mysite.local" it will returns "/"
 */
function _getBasePath($serverScriptName, $serverScriptNamePath) {
  return str_replace($serverScriptName, '', $serverScriptNamePath);
}

/**
 * Return framework entry point.
 * @return string
 *   If entry point is an "index.php" file :
 *   For "localhost/ulysse/www/public/index.php" it will returns "/ulysse/www/public/index.php"
 *   For "mysite.local" it will returns "/index.php"
 */
function getServerScriptNamePath() {
  return $_SERVER['SCRIPT_NAME'];
}

/**
 * @param string $serverScriptName
 *   server script name as return by $_SERVER['script_name'] or _getServerScriptNamePath().
 * @return string
 *   For "/ulysse/www/public/index.php" it will returns "index.php"
 *   For "mysite.local/index.php" it will returns "index.php"
 */
function getServerScriptName($serverScriptName) {
  return basename($serverScriptName);
}

/**
 * @param string $serverRequestUriWithoutBasePath
 *   @see _getServerRequestUriWithoutBasePath()
 * @param string $scriptName
 *   @see getServerScriptName()
 * @return string :
 *   For "http://localhost/ulysse/www/public/index.php/azertyuiop789456123"
 *   it will return "/index.php/azertyuiop789456123"
 *   Idem for "http://ulysse.local/index.php/azertyuiop789456123"
 */
function _removeScriptNameFromPath($serverRequestUriWithoutBasePath, $scriptName) {
  if (strpos($serverRequestUriWithoutBasePath, $scriptName) === 0) {
    return str_replace($scriptName, '', $serverRequestUriWithoutBasePath);
  }
  return $serverRequestUriWithoutBasePath;
}

/**
 * @param string $serverRequestUri
 * @param string $basePath
 * @return string
 * For "http://ulysse.local/index.php/azertyuiop789456123"
 * it returns "index.php/azertyuiop789456123".
 * For "localhost/ulysse/www/public/index.php/azertyuiop789456123"
 * it return also "index.php/azertyuiop789456123".
 */
function _removeBasePathFromServerRequestUri($serverRequestUri, $basePath) {
  return substr_replace($serverRequestUri, '', 0, strlen($basePath));
}

/**
 * @param string $path : @see _removeScriptNameFromPath()
 * @return string :
 * for "/hello", returns "hello".
 * for "/hello/", returns "hello".
 */
function _removeTrailingSlashFromPath($path) {
  return trim(parse_url($path, PHP_URL_PATH), '/');
}

/**
 * @param string $fullUrl
 */
function _setHttpRedirection($fullUrl) {
  header("Location: $fullUrl");
}

/**
 * Désactiver du code malicieux
 * @param $value
 * @return string
 */
function _sanitizeValue($value) {
  return htmlspecialchars($value, ENT_QUOTES, 'utf-8');
}

/**
 * Return le scheme d'une url (http ou https)
 * @return string
 */
function _getUrlScheme() {
  return $_SERVER["HTTPS"] == "on" ? 'https' : 'http';
}

/**
 * FIXME : https & http detection here
 * @return string
 */
function getServerProtocol() {
  return $_SERVER["SERVER_PROTOCOL"];
}

/**
 * Return requested uri.
 * @return string
 * For "http://ulysse.local/index.php/azertyuiop789456123"
 * it will return "/index.php/azertyuiop789456123".
 * For "http://eurl.local/ulysse/www/public/index.php/azertyuiop789456123"
 * it will return "/ulysse/www/public/index.php/azertyuiop789456123".
 */
function getServerRequestUri() {
  return $_SERVER['REQUEST_URI'];
}

function getServerName() {
  return $_SERVER['SERVER_NAME'];
}

/**
 * @param array $include_paths
 *   a list of php paths that will be added to php include path variable.
 */
function addPhpIncludePaths($include_paths) {
  set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $include_paths));
}

function getRedirectionFromUrl() {
  $path = null;
  if (isset($_GET['redirection'])) {
    $path = urldecode($_GET['redirection']);
  }
  return $path;
}

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
  if (isset($route['parent'])) {
    if ($route['parent'] == $supposedParentRouteId) {
      return TRUE;
    }
    else {
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
function getRouteById($routeId) {
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
function renderRouteByPath($path) {
  $routes = getConfig('routes');
  $route = getRouteDeclarationByPath($path, $routes);
  if (!$route) {
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
function getRouteDeclarationByPath($path, $routes) {
  $route = [];
  foreach ($routes as $id => $datas) {
    if (isset($routes[$id]['path']) && $routes[$id]['path'] == $path) {
      $route = $routes[$id];
      $route['id'] = $id;
    }
  }
  return $route;
}

/**
 * Render a route, parsing a route definition
 *
 * @see $config['routes']
 * @param array $route : page array declaration as returned by getPageByPath() or getPageByKey()
 * @return bool
 */
function renderRoute(array $route) {

  $output = getRoutePropertyValue($route['controller']);

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
 * Route properties might be strings or closures, this
 * function returns a value whatever the property is.
 *
 * @param $property
 * @return string
 */
function getRoutePropertyValue($property) {
  return is_string($property) ? $property : $property();
}

function buildAutoRedirectionQueryString() {
  return 'redirection=' . getRouteIdFromPath(getCurrentPath());
}

function vd($value) {
  echo '<pre>';
  var_dump($value);
  echo '</pre>';
}

function vde($value) {
  var_dump($value);exit;
}

function pr($array) {
  echo '<pre>';
  print_r($array);
  echo '</pre>';
}

function pre($array) {
  echo '<pre>';
  print_r($array);
  echo '</pre>';
  exit;
}
