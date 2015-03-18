<?php
/**
 * PHP Ulysse framework core files.
 */

// relative paths from main index.php file (yourapp/www/index.php)
// to find application directory path and ulysse directory path.

// Those constants are defined in index.php file, put some
// defaults if this is not the case. (user may rewrite index.php if he wants to)
if (!defined('APPLICATION_ROOT')) {
  define('APPLICATION_ROOT', '..');
}

if (!defined('ULYSSE_ROOT')) {
  define('ULYSSE_ROOT', '../..');
}

define('APPLICATION_CONFIG_DIRECTORY_PATH', APPLICATION_ROOT . '/config');

/**
 * Run ulysse framework : map an url to a php controller.
 *
 * @code
 * require_once "../src/ulysse/framework/ulysse.php";
 * startFramework();
 * @endocde
 */
function startFramework() {

  // php include paths and PSR0 autoloading :
  addPhpIncludePaths([
      ULYSSE_ROOT . '/modules',
      ULYSSE_ROOT . '/vendors',
      APPLICATION_ROOT . '/modules',
      APPLICATION_ROOT . '/vendors',
    ]);
  registerPsr0ClassAutoloader();

  fireEvent('ulysse.start');

  setContextVariable('time_start', microtime(TRUE));

  // executing our controller and return output to the browser
  // for the currentl path
  echo renderRouteByPath(getCurrentPath(), getServerHttpRequestMethod());

  fireEvent('ulysse.end');

  // display developper informations below the html page.
  if (getSetting('ulysse.framework.displayDevelopperToolbar') === TRUE) {
    require_once "ulysse/framework/developperToolbar.php";
  }
  exit;
}

/**
 * Render a route to json, html etc... parsing route definition
 *
 * Heart function of the router.
 *
 * @see $config['routes']
 * @param array $route : page array declaration as returned by getPageByPath() or getPageByKey()
 * @return bool
 */
function renderRoute(array $route) {

  $routeFormatters = getConfig('routesFormatters');

  if (empty($route['format'])) {
    return $route['path'] . " route has no defined output format";
  }

  if ($route['format'] == 'html' && empty($route['template'])) {
    return '"' . $route['path'] . '" route has not defined a template';
  }

  if (empty($routeFormatters[$route['format']])) {
    return $route['path'] . " has not defined a known route formatter.";
  }

  // call route formatter for this route, which will execute
  // and wrap controller in its own way.
  $output = $routeFormatters[$route['format']]($route);

  // we should have a string at this point.
  return $output;
}



/**
 * Extract a path usable by the framework from an incoming http request,
 * and from apache server informations about this http request.
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

  // "http://localhost/ulysse/www/index.php/admin/content/form" > "/ulysse/www/index.php"
  $scriptNamePath = getServerScriptNamePath();

  // "/ulysse/www/index.php" >  "index.php"
  $scriptName = getServerScriptName($scriptNamePath);

  // "/ulysse/www/index.php" > "/ulysse/www/"
  $basePath = _getBasePath($scriptName, $scriptNamePath);

  //  "http://localhost/ulysse/www/index.php/admin/content/form" > "/ulysse/www/index.php/admin/content/form"
  $serverRequestUri = getServerRequestUri();

  // "/ulysse/www/index.php/admin/content/form" > "index.php/admin/content/form"
  $serverRequestUriWihoutBasePath = removeBasePathFromServerRequestUri($serverRequestUri, $basePath);

  // "index.php/admin/content/form" >  "/admin/content/form"
  $path = removeScriptNameFromPath($serverRequestUriWihoutBasePath, $scriptName);

  // "/admin/content/form" > "admin/content/form"
  $path = removeTrailingSlashFromPath($path);

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
  return getContextVariable('logs');
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
  if (isset($GLOBALS['ULYSSE'])) {
    return $GLOBALS['ULYSSE'];
  }
}

/**
 * Get a context Variable by its key
 * @param string $key
 * @return mixed
 */
function getContextVariable($key) {
  if (isset($GLOBALS['ULYSSE'][$key])) {
    return $GLOBALS['ULYSSE'][$key];
  }
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
function getTranslation($string_id, $language = NULL) {
  $translations = getConfig('translations');
  if (!$language) $language = getCurrentLanguage();
  return $translations[$string_id][$language];
}

/**
 * Build a full url from an ulysse path.
 * @param string $path : e.g "hello/world"
 * @param string $queryString : e.g "value=4&test=true&redirection=contact"
 * @return string : full relative url suitable to build an href html attribute.
 * e.g : "/ulysse/example.app/www/default/index.php/hello-world"
 */
function getRouteUrl($path, $queryString = '') {
  $queryArray = [];
  if ($queryString) parse_str($queryString, $queryArray);
  $queryString = http_build_query($queryArray);
  if (getSetting('cleanUrls') == FALSE) {
    $url = sanitizeValue(getInstallationPath() . getServerEntryPoint() . '/' . $path);
  }
  else {
    $url = sanitizeValue(getInstallationPath() . $path);
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
 * Write a log
 *
 * @param array $log
 * associative array containing the following keys
 * - level : notice, warning, error
 * - detail : detail of the log
 */
function writeLog($log) {
  $logs = getContextVariable('logs');
  $logs[] = $log;
  setContextVariable('logs', $logs);
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
      304 => 'Not modified',
      401 => 'Not authorized',
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
 * Set or add a value to the context.
 * That's in fact just a wrapper around globals.
 * @param string $key
 * @param mixed $value
 */
function setContextVariable($key, $value) {
  $GLOBALS['ULYSSE'][$key] = $value;
}

/**
 * Register a basic psr0 class autoloader.
 */
function registerPsr0ClassAutoloader() {
  spl_autoload_register(function($class){require_once str_replace('\\','/', $class).'.php';});
}

/**
 * @param string $path : path to the template file
 * @param array $variables
 * @return string : template parsed with variables, ready to be printed
 */
function template($path, $variables = []) {
  if ($variables) extract($variables);
  ob_start();
  include $path;
  return ob_get_clean();
}

/**
 * echo with xss protection.
 * @param $value
 */
function e($value) {
  echo sanitizeValue($value);
}

/**
 * @param string $fullUrl
 */
function setHttpRedirection($fullUrl) {
  header("Location: $fullUrl");
}

/**
 * @param array $include_paths
 *   a list of php paths that will be added to php include path variable.
 */
function addPhpIncludePaths($include_paths) {
  set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $include_paths));
}

/**
 * Render a page using its path.
 * @see $config['routes']
 * @param string $path
 * @param string $httpMethod : http request method (GET, POST, PUT etc ...)
 * @return string (html, json, xml or whatever the controller return to us.)
 */
function renderRouteByPath($path, $httpMethod = 'GET') {
  $routes = getConfig('routes');
  $route = getRouteByPath($path, $routes, $httpMethod);
  // route not found, render a 404
  if (!$route) {
    $route = getRouteByPath('__HTTP_404__', $routes);
  }

  $output = renderRoute($route);
  return $output;
}

/**
 * If there is several routes declared with the same path,
 * last found route will be used.
 * @see config/_routes.php
 * @param $pathFromUrl
 * @param $routes
 * @param string $httpMethod : http request method (GET, PUT, POST)
 * @return array : page declaration, null if no route is found.
 */
function getRouteByPath($pathFromUrl, $routes, $httpMethod = 'GET') {

  $matchingRoute = null;

  // try to find a matching static route.
  if (!empty($routes[$pathFromUrl][$httpMethod])) {
    $matchingRoute = $routes[$pathFromUrl][$httpMethod];
    $matchingRoute['path'] = $pathFromUrl;
    $matchingRoute['controller arguments'] = [];
  }

  // no luck with static routes, try to find a corresponding dynamic route.
  $pathFromUrlParts = array_filter(explode('/', $pathFromUrl));
  foreach ($routes as $routePath => $routeDatas) {

    // do not treat static routes. Dynamics routes contain a "arguments" key.
    if (!isset($routeDatas[$httpMethod]['arguments'])) {
      continue;
    }

    // if url parts count does not match this route, skip this iteration.
    $routePathParts = array_filter(explode('/', $routePath));

    $searchedRoutePathParts = [];
    foreach ($routePathParts as $index => $value) {
      if(in_array($value, $routeDatas[$httpMethod]['arguments'])) {
        if (isset($pathFromUrlParts[$index])) {
          $searchedRoutePathParts[] = $pathFromUrlParts[$index];
        }
      }
      else {
        $searchedRoutePathParts[] = $value;
      }
    }
    $searchedRoutePath = implode('/', $searchedRoutePathParts);

    if ($searchedRoutePath == $pathFromUrl) {
      $controllerArgs = array_diff($pathFromUrlParts, $routePathParts);
      $matchingRoute = $routeDatas[$httpMethod] + array(
          'controller arguments' => $controllerArgs,
          'path' => $searchedRoutePath
        );
      break;
    }

  }

  return $matchingRoute;

}

/**
 * Execute Template formatter
 * @param int $formatterId
 * @return string
 */
function formatAs($formatterId) {
  $args = func_get_args();
  if ($args) unset($args[0]);
  $templateFormatters = getConfig('templateFormatters');
  return call_user_func_array($templateFormatters[$formatterId], $args);
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

/**
 * Disable malicous code.
 * @param $value
 * @return string
 */
function sanitizeValue($value) {
  return htmlspecialchars($value, ENT_QUOTES, 'utf-8');
}

/* ==========================================
   Helpers to extract path from http request
   ========================================= */

/**
 * Return base path, if framework is installed in a subfolder of the host
 *
 * @return string
 */
function getInstallationPath() {
  $scriptNamePath = getServerScriptNamePath();
  $scriptName = getServerScriptName($scriptNamePath);
  $test =  _getBasePath($scriptName, $scriptNamePath);
  return $test;
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
 *   it will return "/index.php/azertyuiop789456123".
 * For "http://eurl.local/ulysse/www/index.php/azertyuiop789456123"
 *   it will return "/ulysse/www/index.php/azertyuiop789456123".
 */
function getServerRequestUri() {
  return $_SERVER['REQUEST_URI'];
}

function getServerName() {
  return $_SERVER['SERVER_NAME'];
}

function getServerHttpRequestMethod() {
  return $_SERVER['REQUEST_METHOD'];
}

/**
 * Return le scheme d'une url (http ou https)
 * @return string
 */
function getUrlScheme() {
  return $_SERVER["HTTPS"] == "on" ? 'https' : 'http';
}

/**
 * Get full domain name, with http or https according to
 * the currently used protocol.
 * @return string
 */
function getFullDomainName() {
  return getUrlScheme() . '://' . getServerName();
}

/**
 * get base path to build relative links.
 * @param string $serverScriptName
 * @param string $serverScriptNamePath
 * @return string
 *   If entry point is an "index.php" file :
 *   For "localhost/ulysse/www/index.php" it will returns "/ulysse/www/"
 *   For "mysite.local" it will returns "/"
 */
function _getBasePath($serverScriptName, $serverScriptNamePath) {
  return str_replace($serverScriptName, '', $serverScriptNamePath);
}

/**
 * Return server script name path.
 * @return string
 *   If entry point is an "index.php" file :
 *   For "localhost/ulysse/www/index.php" it will returns "/ulysse/www/index.php"
 *   For "mysite.local" it will returns "/index.php"
 */
function getServerScriptNamePath() {
  return $_SERVER['SCRIPT_NAME'];
}

/**
 * @param string $serverScriptName
 *   server script name as return by $_SERVER['script_name'] or _getServerScriptNamePath().
 * @return string
 *   For "/ulysse/www/index.php" it will returns "index.php"
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
 *   For "http://localhost/ulysse/www/index.php/azertyuiop789456123"
 *   it will return "/index.php/azertyuiop789456123"
 *   Idem for "http://ulysse.local/index.php/azertyuiop789456123"
 */
function removeScriptNameFromPath($serverRequestUriWithoutBasePath, $scriptName) {
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
 *   it returns "index.php/azertyuiop789456123".
 * For "localhost/ulysse/www/index.php/azertyuiop789456123"
 *   it return also "index.php/azertyuiop789456123".
 */
function removeBasePathFromServerRequestUri($serverRequestUri, $basePath) {
  return substr_replace($serverRequestUri, '', 0, strlen($basePath));
}

/**
 * @param string $path : @see _removeScriptNameFromPath()
 * @return string :
 * for "/hello", returns "hello".
 * for "/hello/", returns "hello".
 */
function removeTrailingSlashFromPath($path) {
  return trim(parse_url($path, PHP_URL_PATH), '/');
}
