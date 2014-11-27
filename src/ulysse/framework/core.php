<?php
/**
 * PHP Ulysse framework core file.
 */

// filepath considering www/public/index.php file.
define('CONFIG_DIRECTORY_PATH', '../../config');
define('FRAMEWORK_ROOT', '../..');
define('CONFIG_EXAMPLE_DIRECTORY_PATH', '../../example.config');
define('TEMPLATE_FORMATTERS_FILEPATH', 'templateFormatters.php');
define('THEMES_DIRECTORY', 'themes');

/**
 * Return TRUE if framework has already been setup, FALSE otherwise
 * @return bool
 */
function frameworkIsInstalled()
{
  return file_exists(CONFIG_DIRECTORY_PATH);
}

/**
 * Bootstrap the ulysse framework : listen http request and map it to
 * a php controller, looking at config/pages.php file.
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

  require "core.atoms.php";
  // if framework is not yet installed, display information about installation.
  if (!frameworkIsInstalled())
  {
    echo frameworkInstallationPage($contextVariables);
    exit;
  }

  // add some php usefull include paths.
  _addPhpIncludePaths(['../..', '../../src', '../../vendors']);
  registerPsr0ClassAutoloader();

  session_start();

  // connect to database and register it in the context.
  $contextVariables['db'] = connectToDatabase();

  // register context variables in the site context
  setContextVariable('time_start', microtime(TRUE));
  foreach ($contextVariables as $key => $contextVariable)
  {
    setContextVariable($key, $contextVariable);
  }

  // include template formatters file, for template() function.
  require TEMPLATE_FORMATTERS_FILEPATH;

  fireEvent('ulysse.framework.afterBootstrap');

  // executing controller and returning output to the browser
  echo renderPageByPath(getCurrentPath());

  // display developper informations.
  if (getSetting('display_developper_toolbar') === TRUE) {
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
 * Extract framework path from an incoming http request.
 * Heart of the framework routing system.
 *
 * @return string :
 * For "http://localhost/ulysse/www/public/index.php/hello/world" returns "hello/world"
 * For "http://ulysse.local/index.php/hello/world" returns "hello/world"
 * For "http://ulysse.local/index.php/hello/world?test=value" returns "hello/world".
 *
 * This path is then fetched in pages.php file. If a matching page is found,
 * page controller will be executed.
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
  $serverRequestUriWihoutBasePath = _removeBasePathFromRequestUri($serverRequestUri, $basePath);

  // "/admin/content/form" < "index.php/admin/content/form"
  $path = _removeScriptNameFromPath($serverRequestUriWihoutBasePath, $scriptName);

  // "admin/content/form" < "/admin/content/form"
  $path = _removeTrailingSlashFromPath($path);

  return $path;
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
 * Display information about framework setup.
 * @return string html
 */
function frameworkInstallationPage()
{
  $out = '';
  $out .= '<h1>' . getTranslation("ulysse.framework.installationTitle") . '</h1>';
  $out .= getTranslation('ulysse.framework.installationText');
  return $out;
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
 * @see config/pages.php
 * @param $path
 * @param $pages
 * @return array : page declaration
 */
function getPageDeclarationByPath($path, $pages)
{
  $page = [];
  foreach ($pages as $id => $datas)
  {
    if (isset($pages[$id]['path']) && $pages[$id]['path'] == $path)
    {
      $page = $pages[$id];
      $page['id'] = $id;
    }
  }
  return $page;
}

/**
 * Return a page by its key
 * @see config/pages.php file.
 * @param string $key
 * @return array : the page definition as an array
 */
function getPageDeclarationByKey($key)
{
  $pages = getConfig('pages');
  return $pages[$key];
}

/**
 * Render a page content using its path.
 * @see config/pages.php file
 * @param string $path
 * @return string (html, json, xml or whatever the controller return to us.)
 */
function renderPageByPath($path) {
  $pages = getConfig('pages');
  $page = getPageDeclarationByPath($path, $pages);
  if (!$page)
  {
    $page = getPageDeclarationByKey('__PAGE_NOT_FOUND__', $pages);
  }
  $output = renderPage($page);
  return $output;
}

function getConfigDirectoryPath()
{
  return frameworkIsInstalled() ? CONFIG_DIRECTORY_PATH : CONFIG_EXAMPLE_DIRECTORY_PATH;
}

/**
 * Return value of a site setting
 * @see config/settings.php file.
 * @param string $key = settings identifier
 * @return mixed
 */
function getSetting($key)
{
  $settings = getConfig('settings');
  return $settings[$key];
}

/**
 * Fire a Dom Event
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

function getConfig($type = null) {
  static $config = [];
  if (!$config)
  {
    include getConfigDirectoryPath() . '/config.php';
    // special local config file.
    if (is_readable(getConfigDirectoryPath() . '/config.local.php'))
    {
      include getConfigDirectoryPath() . '/config.local.php';
    }

  }
  if ($type)
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

function url($path, $queryString = '')
{
  $queryArray = [];
  if ($queryString) parse_str($queryString, $queryArray);
  if (isset($queryArray['form_redirection'])) {
    //$queryArray['form_redirection'] = urlencode($queryArray['form_redirection']);
  }
  // build back a query string
  $queryString = http_build_query($queryArray);
  $url = sanitizeValue(getBasePath() . getServerScriptName() . '/' . $path);
  if ($queryString) $url .= '?' . sanitizeValue($queryString);
  return $url;
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
 * @see config/pages.php
 * @param array $page : page array declaration as returned by getPageByPath() or getPageByKey()
 * @return bool
 */
function renderPage(array $page)
{
  $output = is_string($page['callable']) ? $page['callable'] : $page['callable']();
  if (!empty($page['layout'])) {
    $layoutVariables = [];

    if (!empty($page['layout_variables'])) {
      $layoutVariables = $page['layout_variables'];
    }
    $layoutVariables['content'] = $output;
    if (!empty($page['theme'])) {
      $themePath = THEMES_DIRECTORY . DIRECTORY_SEPARATOR . $page['theme'];
    }
    else {
      $themePath = getSetting('theme_path');
    }
    $output = template($page['layout'], $layoutVariables, $themePath);
  }
  return $output;
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
function setHttpResponseCode($code, $message = null, $protocol = null) {
  // most common response code and their associated messages.
  $codesMessages = [
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
 * Helper to merge settings from a file into settings of another file.
 * @param array $variable
 * @param string $filepath
 * @return array
 */
function mergeConfigFromFile($variables, $filepath)
{
  $newVariables = require $filepath;

  foreach ($newVariables as $key => $value) {
    $variables[$key] = $value;
  }
  return $variables;
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
 * @param string $themePath : force theme to use.
 * may be defined. A theme is a collection of templates.
 * @return string
 */
function template($templatePath, $variables = [], $themePath = null)
{
  $templateFound = FALSE;
  $searchPaths = [
    $themePath ? $themePath . DIRECTORY_SEPARATOR . $templatePath : getSetting('theme_path') . DIRECTORY_SEPARATOR . $templatePath,
    $templatePath,
  ];
  if ($variables) extract($variables);
  ob_start();
  foreach ($searchPaths as $path)
  {
    $include = @include($path);
    if ($include)
    {
      $templateFound = TRUE;
      break;
    }
  }
  if (!$templateFound)
  {
    writeLog(['level' => 'warning', 'detail' => sprintf("%s template is not readable or does not exist", sanitizeValue($path))]);
  }
  else {
    writeLog(['level' => 'notification', 'detail' => sprintf('Template "%s" rendered. ', sanitizeValue($path))]);
  }
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

  if ($formatters) {
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

function setHttpRedirection($path)
{
  $url = sanitizeValue(url($path));
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
 * @param string $path
 *
 */
function redirection($path = NULL) {
  if (is_null($path))
  {
    $path = _getFormRedirectionFromUrl();
  }
  setHttpRedirection($path);
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

