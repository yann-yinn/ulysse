<?php
/**
 * PHP Ulysse framework core files.
 */
if (!defined('ULYSSE_ROOT'))
{
  define('ULYSSE_ROOT', '../../../ulysse');
}

if (!defined('APPLICATION_ROOT'))
{
  define('APPLICATION_ROOT', '../..');
}

define('TEMPLATE_FORMATTERS_FILEPATH', 'templateFormatters.php');
define('ULYSSE_THEMES_DIRECTORY_PATH', ULYSSE_ROOT . '/themes');

// filepaths considering "siteDirectory/www/public/index.php file."
define('APPLICATION_THEMES_DIRECTORY_PATH', APPLICATION_ROOT . '/themes');
define('APPLICATION_CONFIG_DIRECTORY_PATH', APPLICATION_ROOT . '/config');

/**
 * Bootstrap the ulysse framework : listen http request and map it to
 * a php controller, looking at config/_routes.php file.
 *
 * @param array $contextVariables : array of values to define site context
 * Use this bootstrap in a script in "www" directory with following example code :
 * @code
 * require_once "../src/ulysse/framework/core.php";
 * startFramework();
 * @endocde
 */
function startFramework($contextVariables = [])
{

  _addPhpIncludePaths([
      ULYSSE_ROOT . '/src',
      ULYSSE_ROOT . '/vendors',
      APPLICATION_ROOT . '/src',
      APPLICATION_ROOT . '/vendors',
    ]);
  // register a PSR0 class to allow autoloading for vendors and custom code.
  registerPsr0ClassAutoloader();

  // for user connexion.
  session_start();

  // connect to database and register its connexion in the context, so
  // that we can access db connexion from anywhere in the code.
  // @see getDbConnexion();
  $contextVariables['db'] = connectToDatabase();

  // register context variables in the application context
  setContextVariable('time_start', microtime(TRUE));
  foreach ($contextVariables as $key => $contextVariable)
  {
    setContextVariable($key, $contextVariable);
  }

  // include template formatters file, for template() function.
  // @FIXME find a better place for formatters ? is this needed at all ?
  require TEMPLATE_FORMATTERS_FILEPATH;

  fireEvent('ulysse.framework.afterBootstrap');

  // executing controller and returning output to the browser
  echo renderRouteByPath(getCurrentPath());

  // display developper informations.
  if (getSetting('ulysse.framework.displayDevelopperToolbar') === TRUE) {
    require_once "ulysse/framework/developperToolbar.php";
  }

}

/**
 * connect to database with PDO
 * @return PDO connexion
 */
function connectToDatabase()
{
  $databaseDatas = getSetting("database");
  $db = _connectToDatabase($databaseDatas);
  return $db;
}

/**
 * Return base path, if framework is installed in a subfolder of the host
 *
 * @return string
 */
function getBasePath()
{
  $scriptNamePath = _getServerScriptNamePath();
  $scriptName = _getServerScriptName($scriptNamePath);
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
function getCurrentPath()
{
  static $path = null;
  if ($path) return $path;

  // "/ulysse/www/public/index.php" < "http://localhost/ulysse/www/public/index.php/admin/content/form"
  $scriptNamePath = _getServerScriptNamePath();

  // "index.php" <  "/ulysse/www/public/index.php"
  $scriptName = _getServerScriptName($scriptNamePath);

  // "/ulysse/www/public/" < "/ulysse/www/public/index.php"
  $basePath = _getBasePath($scriptName, $scriptNamePath);

  // "/ulysse/www/public/index.php/admin/content/form" < "http://localhost/ulysse/www/public/index.php/admin/content/form"
  $serverRequestUri = _getServerRequestUri();

  // "index.php/admin/content/form" < "/ulysse/www/public/index.php/admin/content/form"
  $serverRequestUriWihoutBasePath = _removeBasePathFromServerRequestUri($serverRequestUri, $basePath);

  // "/admin/content/form" < "index.php/admin/content/form"
  $path = _removeScriptNameFromPath($serverRequestUriWihoutBasePath, $scriptName);

  // "admin/content/form" < "/admin/content/form"
  $path = _removeTrailingSlashFromPath($path);

  return $path;
}

function getCurrentRouteId() {
  $routes = getConfig('routes');
  $path = getCurrentPath();
  foreach ($routes as $id => $route) {
    if ($route['path'] == $path) {
      return $id;
    }
  }
}

/**
 * Get framework script entry point, usually "index.php"
 * @return string
 */
function getServerScriptName()
{
  return _getServerScriptName(_getServerScriptNamePath());
}

/**
 * Get all site Logs
 * @return array : all site logs
 */
function getAllLogs()
{
  return $GLOBALS['_LOGS'];
}

/**
 * Get database connexion to perform queries.
 * @return PDO connexion object
 */
function getDbConnexion()
{
  return getContextVariable('db');
}

/**
 * Get current language used on the site by the visitor
 * @return string : langcode (fr, en etc...)
 */
function getCurrentLanguage()
{
  $currentLanguage = getSetting('language_default');
  if (isset($_REQUEST['language']))
  {
    $requestedLanguage = (string)sanitizeValue($_REQUEST['language']);
    $definedLanguages = getSetting('languages');
    foreach ($definedLanguages as $id => $datas)
    {
      if ($definedLanguages[$id]['query'] == $requestedLanguage)
      {
        $currentLanguage = $requestedLanguage;
      }
    }
  }
  return $currentLanguage;
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
 * Return a page by its key
 * @see config/_routes.php file.
 * @param string $key
 * @return array : the page definition as an array
 */
function getRouteDeclarationByKey($key)
{
  $routes = getConfig('routes');
  return $routes[$key];
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
    $route = getRouteDeclarationByKey('__HTTP_404__', $routes);
  }
  $output = renderRoute($route);
  return $output;
}

/**
 * Return value of a site setting
 * @see config/_settings.php file.
 * @param string $key = settings identifier
 * @return mixed
 */
function getSetting($key)
{
  $settings = getConfig('settings');
  if (isset($settings[$key]))
  {
    return $settings[$key];
  }
}

/**
 * Fire a Dom Event
 * @param string $event_id
 * @return string
 */
function fireDomEvent($event_id)
{
  $returns = fireEvent($event_id);
  return implode("\r\n", $returns);
}

/**
 * @param string $event_id : event id
 * @return array all returns by all executed listeners
 */
function fireEvent($event_id)
{
  $listeners = getConfig('listeners');
  $returns = [];
  if (isset($listeners[$event_id]))
  {
    foreach($listeners[$event_id] as $listener_id => $listener)
    {
      $return = executeListener($listener);
      $returns[] = $return;
      writeLog(['detail' => "Executing '$listener_id' listener for event '$event_id' : listener returned : " . var_export($return, TRUE)]);
    }
    return $returns;
  }
  else
  {
    writeLog(['detail' => 'No listeners found for ' . $event_id . ' event']);
    return FALSE;
  }
}

/**
 * @param array $listener with a "callable" key which is a closure.
 * @return mixed
 */
function executeListener($listener)
{
  return $listener['callable']();
}

/**
 * Return full context for the current framework response to the http request.
 * @return array
 */
function getContext()
{
  return $GLOBALS['_CONTEXT'];
}

/**
 * Get a context Variable by its key
 * @param string $key
 * @return mixed
 */
function getContextVariable($key)
{
  return $GLOBALS['_CONTEXT'][$key];
}

function getConfig($type = null)
{
  static $config = [];
  if (!$config)
  {
    include APPLICATION_CONFIG_DIRECTORY_PATH . '/config.php';
    // special local config file.
    if (is_readable(APPLICATION_CONFIG_DIRECTORY_PATH  . '/config.local.php'))
    {
      include APPLICATION_CONFIG_DIRECTORY_PATH  . '/config.local.php';
    }

  }
  if ($type && isset($config[$type]))
  {
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
function getTranslation($string_id, $language = NULL)
{
  $translations = getConfig('translations');
  if (!$language) $language = getCurrentLanguage();
  return $translations[$string_id][$language];
}

/**
 * Build a full url from a framework path.
 * @param string $path : e.g "hello/world"
 * @param string $queryString : e.g "value=4&test=true&form_redirection=contact"
 * @return string : full url suitable to build an html link.
 */
function url($path, $queryString = '')
{
  $queryArray = [];
  if ($queryString) parse_str($queryString, $queryArray);
  $queryString = http_build_query($queryArray);
  if (getSetting('cleanUrls') == FALSE)
  {
    $url = sanitizeValue(getBasePath() . getServerScriptName() . '/' . $path);
  }
  else
  {
    $url = sanitizeValue(getBasePath() . $path);
  }
  if ($queryString) $url .= '?' . sanitizeValue($queryString);
  return $url;
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
 * Return TRUE if $path is the current http requested path, FALSE otherwise.
 * Usefull to set "active" classes in html, for example for menus.
 * @param string $path
 * @return bool
 */
function isCurrentPath($path)
{
  $urlPath = getCurrentPath();
  return $path == $urlPath ? TRUE : FALSE;
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
 * Return full relative path to a theme inside themes directory.
 * @param string $theme
 * @return bool|string
 */
function getThemePath($theme)
{
  $themePath = FALSE;
  if(file_exists(APPLICATION_THEMES_DIRECTORY_PATH . DIRECTORY_SEPARATOR . $theme))
  {
    $themePath = APPLICATION_THEMES_DIRECTORY_PATH . DIRECTORY_SEPARATOR . $theme;
  }
  elseif(file_exists(ULYSSE_THEMES_DIRECTORY_PATH . DIRECTORY_SEPARATOR . $theme))
  {
    $themePath = ULYSSE_THEMES_DIRECTORY_PATH . DIRECTORY_SEPARATOR . $theme;
  }
  return $themePath;
}

/**
 * Page properties might be strings or closure.
 * @param $property
 * @return string
 */
function getRoutePropertyValue($property) {
  return is_string($property) ? $property : $property();
}

/**
 * Write a log
 *
 * @param array $log
 * associative array containing the following keys
 * - level : notice, warning, error
 * - detail : detail of the log
 */
function writeLog($log)
{
  $GLOBALS['_LOGS'][] = $log;
}

/**
 * Add an http response code header, using http or https for the sheme
 * @param int $code : http response code 200, 400 etc...
 * @param $message : message associated to the http response code
 * @param $protocol (
 */
function setHttpResponseCode($code, $message = null, $protocol = null)
{
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
  $protocol = $protocol ? $protocol : _getServerProtocol();
  $message  = $message ? $message : $codesMessages[$code];
  header(sprintf("%s %s %s", $protocol, sanitizeValue($code), sanitizeValue($message)));
}

/**
 * Set or add a value to the context
 * @param string $key
 * @param mixed $value
 */
function setContextVariable($key, $value)
{
  $GLOBALS['_CONTEXT'][$key] = $value;
}

/**
 * Register a basic psr0 class autoloader.
 */
function registerPsr0ClassAutoloader()
{
  spl_autoload_register(function($class){require_once str_replace('\\','/', $class).'.php';});
}

/**
 * Sanitize a variable, to display it, encoding html.
 * @param string $value
 * @return string html encoded value
 */
function sanitizeValue($value)
{
  return _sanitizeValue($value);
}

/**
 * Render a specific template file
 *
 * First look for a file inside the currently active theme.
 *
 * @param string $templatePath : file path. e.g : ock/content/templates/mytemplate.php
 * @param array $variables
 * @param string $inDirectory : search first template file in this directory.
 * may be defined. A theme is a collection of templates.
 * @return string
 */
function template($templatePath, $variables = [], $inDirectory = null)
{
  // content.php
  $output = FALSE;
  $searchPaths =
  [
    $inDirectory ? $inDirectory . DIRECTORY_SEPARATOR . $templatePath : getSetting('theme') . DIRECTORY_SEPARATOR . $templatePath,
    $templatePath,
  ];
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
 * Do not use "print" or "echo" in templates when possible, as
 * this function take care of encoding malicious entities.
 *
 * @param string $value : a single string value to print.
 * @param array | string  $formatters : array of function names to format the value.
 * Special formatter "raw" may be used to disabled default escaping of the value.
 * @return string
 */
function e($value, $formatters = [])
{
  // sanitize value string by default unless "raw" special formatter name is requested
  $output = ($formatters != "raw" || !in_array('raw', $formatters)) ? $value : sanitizeValue($value);

  if ($formatters)
  {
    // if formatters is a string, apply it directly and echo string :
    if (is_string($formatters))
    {
      $output = $formatters($output);
    }
    // if formatters is an array, apply each formatter to the string :
    else
    {
      foreach ($formatters as $function)
      {
        if ($function != "raw") $output = $function($output);
      }
    }
  }
  echo $output;
}

function getFullDomainName()
{
  return _getUrlScheme() . '://' . _getServerName();
}

function setHttpRedirection($routeId)
{
  $url = sanitizeValue(href($routeId));
  _setHttpRedirection(getFullDomainName() . $url);
}

function getFormRedirectionFromUrl()
{
  return _getFormRedirectionFromUrl();
}

/**
 * Redirect to specified path.
 * If path is not specified, function will look
 * into the url for a GET "redirection" query param, and will use
 * it as the redirection path.
 * @param string $routeId : pageId identifier
 *
 */
function redirection($routeId = NULL) {
  if (is_null($routeId))
  {
    $routeId = _getFormRedirectionFromUrl();
  }
  setHttpRedirection($routeId);
  exit;
}

/**
 * One day, this function will check for user permissions. Maybe.
 * @return bool
 */
function userHasPermission()
{
  return TRUE;
}

/* ====================
   HELPERS
   ==================== */

/**
 * Core atoms
 * @param array $attributes
 * @return string
 */

function _setHtmlAttributes(array $attributes = array())
{
  foreach ($attributes as $attribute => &$data)
  {
    $data = implode(' ', (array) $data);
    $data = $attribute . '="' . htmlspecialchars($data, ENT_QUOTES, 'utf-8') . '"';
  }
  return $attributes ? ' ' . implode(' ', $attributes) : '';
}

/**
 * get path to build links, href, src etc .... in templates.
 * @param string $serverScriptName
 * @param string $serverScriptNamePath
 * @return string
 *   If entry point is an "index.php" file :
 *   For "localhost/ulysse/www/public/index.php" it will returns "/ulysse/www/public/"
 *   For "mysite.local" it will returns "/"
 */
function _getBasePath($serverScriptName, $serverScriptNamePath)
{
  return str_replace($serverScriptName, '', $serverScriptNamePath);
}

/**
 * Return framework entry point.
 * @return string
 *   If entry point is an "index.php" file :
 *   For "localhost/ulysse/www/public/index.php" it will returns "/ulysse/www/public/index.php"
 *   For "mysite.local" it will returns "/index.php"
 */
function _getServerScriptNamePath()
{
  return $_SERVER['SCRIPT_NAME'];
}

/**
 * @param string $serverScriptName
 *   server script name as return by $_SERVER['script_name'] or _getServerScriptNamePath().
 * @return string
 *   For "/ulysse/www/public/index.php" it will returns "index.php"
 *   For "mysite.local/index.php" it will returns "index.php"
 */
function _getServerScriptName($serverScriptName)
{
  return basename($serverScriptName);
}

/**
 * @param string $serverRequestUriWithoutBasePath
 *   @see _getServerRequestUriWithoutBasePath()
 * @param string $scriptName
 *   @see _getServerScriptName()
 * @return string :
 *   For "http://localhost/ulysse/www/public/index.php/azertyuiop789456123"
 *   it will return "/index.php/azertyuiop789456123"
 *   Idem for "http://ulysse.local/index.php/azertyuiop789456123"
 */
function _removeScriptNameFromPath($serverRequestUriWithoutBasePath, $scriptName)
{
  if (strpos($serverRequestUriWithoutBasePath, $scriptName) === 0)
  {
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
function _removeBasePathFromServerRequestUri($serverRequestUri, $basePath)
{
  return substr_replace($serverRequestUri, '', 0, strlen($basePath));
}

/**
 * @param string $path : @see _removeScriptNameFromPath()
 * @return string :
 * for "/hello", returns "hello".
 * for "/hello/", returns "hello".
 */
function _removeTrailingSlashFromPath($path)
{
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
function _sanitizeValue($value)
{
  return htmlspecialchars($value, ENT_QUOTES, 'utf-8');
}

/**
 * Return le scheme d'une url (http ou https)
 * @return string
 */
function _getUrlScheme()
{
  return $_SERVER["HTTPS"] == "on" ? 'https' : 'http';
}

/**
 * FIXME : https & http detection here
 * @return string
 */
function _getServerProtocol()
{
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
function _getServerRequestUri()
{
  return $_SERVER['REQUEST_URI'];
}

function _getServerName()
{
  return $_SERVER['SERVER_NAME'];
}

/**
 * @TODO : databases whould be passed without the id for
 * this atomic function.
 * Connect to a database with PDO
 * @param array $databaseDatas = [
 *   'default' => [
 *     'driver' => 'sqlite',
 *     'sqlite_file' => 'writable/database.sqlite',
 *     // for mysql :
 *     //'host' => '127.0.0.1',
 *     //'name' => 'framework',
 *     //'user' => 'root',
 *     //'password' => '',
 * ]
 * @param string $id
 * @return bool|PDO
 */
function _connectToDatabase($databaseDatas, $id = 'default')
{
  // Connect to database specified in database settings if any, using PDO
  $db = FALSE;
  if (!empty($databaseDatas[$id]))
  {
    try
    {
      if ($databaseDatas[$id]['driver'] == 'mysql')
      {
        $db = new PDO("{$databaseDatas[$id]['driver']}:host={$databaseDatas[$id]['host']};dbname={$databaseDatas[$id]['name']}", $databaseDatas[$id]['user'], $databaseDatas[$id]['password']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      if ($databaseDatas[$id]['driver'] == 'sqlite')
      {
        $sqliteFile = $databaseDatas[$id]['sqlite_file'];
        $db = new PDO("{$databaseDatas[$id]['driver']}:$sqliteFile");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
    }
    catch (PDOException $e)
    {
      echo $e->getMessage();
      die();
    }
  }
  return $db;
}

/**
 * @param array $include_paths
 *   a list of php paths that will be added to php include path variable.
 */
function _addPhpIncludePaths($include_paths)
{
  set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $include_paths));
}

function _getFormRedirectionFromUrl() {
  $path = null;
  if (isset($_GET['form_redirection']))
  {
    $path = urldecode($_GET['form_redirection']);
  }
  return $path;
}

/**
 * @param string $machine_name : a string containing only alphanumeric and underscore characters
 * @return boolean : TRUE if machine_name is valid, FALSE otherwise
 */
function _validateMachineName($machine_name)
{
  return (bool)preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $machine_name);
}

function vd($value)
{
  echo '<pre>';
  var_dump($value);
  echo '</pre>';
}

function vde($value)
{
  var_dump($value);exit;
}

function pre($array)
{
  echo '<pre>';
  print_r($array);
  echo '</pre>';
  exit;
}


