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
  //
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
 * startUlysse();
 * @endocde
 */
function startUlysse($contextVariables = [])
{

  // if framework is not yet installed, display information about installation.
  if (!frameworkIsInstalled())
  {
    echo frameworkInstallationPage($contextVariables);
    exit;
  }

  // add some php usefull include paths.
  _addPhpIncludePaths(['../..', '../../src', '../../vendors']);
  registerPsr0ClassAutoloader();

  // Try to display framework logs even if php a fatal error occured
  register_shutdown_function("phpFatalErrorHandler");
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

  // executing controller and returning output to the browser
  echo renderPageByPath(getCurrentPath());

  // display developper informations.
  if (getSetting('display_developper_toolbar') === TRUE) {
    require_once "../src/ulysse/framework/developperToolbar.php";
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
 * Load all routes defined in files.
 * @return mixed
 */
function getAllPages()
{
  static $pages = [];
  if (!$pages) $pages = include getConfigDirectoryPath() . '/pages.php';
  return $pages;
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
  $pages = getAllPages();
  return $pages[$key];
}

/**
 * Render a page content using its path.
 * @see config/pages.php file
 * @param string $path
 * @return string (html, json, xml or whatever the controller return to us.)
 */
function renderPageByPath($path) {
  $pages = getAllPages();
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
  static $settings = [];
  if (!$settings) $settings = getAllSettings();
  if (!isset($settings[$key])) {
    writeLog(['level' => 'error', 'detail' => sanitizeValue($key) . ' setting not declared.']);
  }
  return $settings[$key];
}

/**
 * Get settings from settings.php file.
 * @return array
 *   an associative array of site settings.
 */
function getSettings()
{
  return include getConfigDirectoryPath() . '/settings.php';
}

/**
 * Return all settings defined in config/settings.php AND "settings.local.php"
 * @return array
 */
function getAllSettings()
{
  return array_merge(getSettings(), getLocalSettings());
}

/**
 * Return array of settings from settings.local.php
 * @return array
 */
function getLocalSettings()
{
  $settings = [];
  if (is_readable(getConfigDirectoryPath() . '/settings.local.php'))
  {
    $settings = include getConfigDirectoryPath() . '/settings.local.php';
  }
  return $settings;
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

function getAllTranslations()
{
  return include getConfigDirectoryPath() . '/translations.php';
}

/**
 * Get a translation for a specific string_id
 * @param $string_id
 * @param string $language
 * @return string : localized string
 */
function getTranslation($string_id, $language = NULL)
{
  static $translations = [];
  if (!$translations) $translations = getAllTranslations();
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
  $output = is_string($page['content']) ? $page['content'] : $page['content']();
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
function mergeConfigFromFile($variable, $filepath)
{
  return $variable += require_once $filepath;
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

function phpFatalErrorHandler()
{
  if(error_get_last() !== NULL) require "developperToolbar.php";
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

/* ==================
   UNIT FUNCTIONS
   ================== */

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
function _removeBasePathFromRequestUri($serverRequestUri, $basePath)
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
        $sqliteFile = FRAMEWORK_ROOT . DIRECTORY_SEPARATOR . "{$databaseDatas[$id]['sqlite_file']}";
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

function vd($value) {
  echo '<pre>';
  var_dump($value);
  echo '</pre>';
}

function vde($value) {
  var_dump($value);exit;
}

function pre($array) {
  echo '<pre>';
  print_r($array);
  echo '</pre>';
  exit;
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
