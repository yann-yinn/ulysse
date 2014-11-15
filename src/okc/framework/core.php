<?php
/**
 * PHP framework core file.
 */

define("CONFIG_EXAMPLE_DIRECTORY", 'example.config');
define('CONFIG_DIRECTORY_PATH', '../config');
define('USER_CONTENT_DIRECTORY', 'user_content');
define('PAGES_FILEPATH', '../config/pages.php');
define('STRINGS_TRANSLATIONS_FILEPATH', '../config/translations.php');
define('SETTINGS_FILEPATH', '../config/settings.php');
define('SETTINGS_LOCAL_FILEPATH', '../config/settings.local.php');
define('TEMPLATE_FORMATTERS_FILEPATH', '../config/template_formatters.php');

/**
 * Bootstrap the okc framework : listen http request and map it to
 * a php controller.
 *
 * @param array $contextVariables : array of values to define site context
 * Use this bootstrap in a script in "www" directory with following example code :
 * @code
 * require_once "../src/okc/framework/bootstrap.php";
 * bootstrapFramework();
 * @endocde
 */
function bootstrapFramework($contextVariables = [])
{
  // Framework is not setup if "example.config" directory has not be renamed to "config".
  // Inform user and stop here for now.
  if (!file_exists(CONFIG_DIRECTORY_PATH)) {
    echo frameworkInstallationPage($contextVariables);
    exit;
  }
  // Try to display framework logs even if php a fatal error occured
  register_shutdown_function("phpFatalErrorHandler");
  session_start();
  // Add include paths for class autoloaders
  $includePaths = ['..', '../src', '../vendors'];
  setPhpIncludePaths($includePaths);
  setPsr0ClassAutoloader();
  // register context variables in the site context
  setSiteContextVariable('time_start', microtime(TRUE));
  foreach ($contextVariables as $key => $contextVariable)
  {
    setSiteContextVariable($key, $contextVariable);
  }
  // include template formatters file, for template() function.
  require TEMPLATE_FORMATTERS_FILEPATH;
  // display page corresponding to submitted http request
  $controllerOutput = getPageFromHttpRequest();
  echo $controllerOutput;
  setLog(['level'    => 'notification', 'detail'   => "page content is '" . getSafeString($controllerOutput) . "'"]);
  if (getSiteSetting('display_developper_toolbar') === TRUE) require_once "../src/okc/framework/developper_toolbar.php";
}

function frameworkInstallationPage() {
  $out = '';
  $out .= '<h1>Welcome to framework installation</h1>';
  $out .= 'Please rename "example.config" directory to "config" to start using framework.';
  return $out;
}

function getAllLogs()
{
  return $GLOBALS['_LOGS'];
}

function getCurrentLanguage()
{
  $currentLanguage = getSiteSetting('language_default');
  if (isset($_REQUEST['language']))
  {
    $requestedLanguage = (string)getSafeString($_REQUEST['language']);
    $definedLanguages = getSiteSetting('languages');
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
function getPages()
{
  return include PAGES_FILEPATH;
}

/**
 * If there is several routes, last found route will be used.
 * @param $path
 * @param $pages
 * @return array
 */
function getPageByPath($path, $pages)
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

function getPageById($key, $pages) {
  return $pages[$key];
}

function getPageContentByPath($path) {
  static $pages = [];
  if (!$pages) $pages = getPages();
  $page = getPageByPath($path, $pages);
  if (!$page) {
    $page = getPageById('__PAGE_NOT_FOUND__', $pages);
  }
  $output = renderPage($page);
  return $output;
}

/**
 * Run our virtual server.
 * @return bool
 */
function getPageFromHttpRequest()
{
  $scriptName = getSiteScriptName();
  setLog(['level'  => 'notification', 'detail' => sprintf("Script name is %s", getSafeString($scriptName))]);

  $basePath = getSiteBasePath($scriptName);
  setLog(['level' => 'notification', 'detail' => sprintf("base path is %s", getSafeString($basePath))]);
  setSiteContextVariable('basePath', getSafeString($basePath));

  $path = getPagePathFromHttpRequest($scriptName, $basePath);
  setLog(['level'    => 'notification', 'detail'   => "Framework determine path from http request as '$path'"]);
  setSiteContextVariable('path', getSafeString($path));

  return getPageContentByPath($path);
}

/**
 * Extract path from incoming http request.
 *
 * @param string $script_name as returned by getSiteScriptName()
 * @param string $base_path as returned by getSiteBasePath()
 * @return string
 */
function getPagePathFromHttpRequest($script_name, $base_path)
{
  // Remove base path (for installations in subdirectories) from URI.
  $pagePath = substr_replace($_SERVER['REQUEST_URI'], '', 0, strlen($base_path));
  // Remove scriptname "index.php" if present. scriptname is not present at all if .htaccess is enabled.
  if (strpos($pagePath, $script_name) === 0) $pagePath = str_replace($script_name, '', $pagePath);
  // remove query string and slashes from pagePath.
  return trim(parse_url($pagePath, PHP_URL_PATH), '/');
}

function getSiteSetting($id)
{
  static $settings = [];
  if (!$settings) $settings = getSiteAllSettings();
  return $settings[$id];
}

function getSiteAllSettings() {
  $siteSettings = include SETTINGS_FILEPATH;
  if (is_readable(SETTINGS_LOCAL_FILEPATH))
  {
    $siteSettingsLocal = include SETTINGS_LOCAL_FILEPATH;
    $siteSettings = array_merge($siteSettings, $siteSettingsLocal);
  }
  return $siteSettings;
}

function getSiteContext()
{
  return $GLOBALS['_CONTEXT'];
}

function getSiteContextVariable($id)
{
  return $GLOBALS['_CONTEXT'][$id];
}

/**
 * Return base path, if applications is installed in a subfolder.
 *
 * @param string $scriptName as returned by getSiteScriptName()
 * @return string
 */
function getSiteBasePath($scriptName)
{
  return str_replace($scriptName, '', $_SERVER['SCRIPT_NAME']);
}

/**
 * file php which is the entry point of your application; usually "index.php".
 */
function getSiteScriptName()
{
  return basename($_SERVER['SCRIPT_NAME']);
}

function getTranslationByStringId($id, $language = NULL)
{
  static $translations = [];
  if (!$translations) $translations = include STRINGS_TRANSLATIONS_FILEPATH;
  if (!$language) $language = getCurrentLanguage();
  return $translations[$id][$language];
}

function getUrlFromPath($path)
{
  $scriptName = getSafeString(getSiteScriptName());
  $url = getSiteBasePath($scriptName) . $scriptName . '/' . $path;
  return $url;
}

function isCurrentPath($path)
{
  $scriptName  = getSafeString(getSiteScriptName());
  $basePath    = getSiteBasePath($scriptName);
  $urlPath     = getPagePathFromHttpRequest($scriptName, $basePath);
  return $path == $urlPath ? TRUE : FALSE;
}

/**
 * @param array $page as returned by getPageByPath()
 * @return bool
 */
function renderPage(array $page)
{
  $output = is_string($page['content']) ? $page['content'] : $page['content']();
  if (!empty($page['template'])) {
    if (empty($page['template_content_variable'])) $page['template_content_variable'] = 'content';
    $output = template($page['template'], [$page['template_content_variable'] => $output]);
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
function setLog($log)
{
  $GLOBALS['_LOGS'][] = $log;
}

/**
 * Add an http header, using http or https given the curre
 * @param int $code : http response code 200, 400 etc...
 * @param $message : message associated to the http response code
 * @param $protocol (
 */
function setHttpResponseHeader($code, $message = null, $protocol = null) {
  $httpCodes = [
    200 => 'OK',
    403 => 'Forbidden',
    404 => 'Not Found',
  ];
  if (!$protocol)
  {
    $protocol = $_SERVER["SERVER_PROTOCOL"];
  }
  if (!$message)
  {
    if (!empty($httpCodes[$code]))
    {
      $message = $httpCodes[$code];
    }
    else
    {
      setLog(['level' => 'warning', 'detail' => sprintf("No message found for http code %s", getSafeString($code))]);
    }
  }
  header(sprintf("%s %s %s", getSafeString($protocol), getSafeString($code), getSafeString($message)));
}

function setSiteContextVariable($id, $value)
{
  $GLOBALS['_CONTEXT'][$id] = $value;
}

function mergeConfigFromFile($variables, $filepath)
{
  return $variables += include $filepath;
}

/**
 * Register a basic psr0 class autoloader.
 */
function setPsr0ClassAutoloader()
{
  spl_autoload_register(function($class){require_once str_replace('\\','/', $class).'.php';});
}

/**
 * @param array $include_paths
 *   a list of php path that will be add to php include paths
 */
function setPhpIncludePaths($include_paths)
{
  set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $include_paths));
}

/**
 * Sanitize a variable, to display it.
 * @param string $value
 * @return string html encoded value
 */
function getSafeString($value)
{
  return htmlspecialchars($value, ENT_QUOTES, 'utf-8');
}

function phpFatalErrorHandler()
{
  if(error_get_last() !== NULL) require "developper_toolbar.php";
}

/**
 * Render a specific template file
 *
 * @param string $templatePath : file path
 * @param array $variables
 * @param string $themePath : theme to use. Different themes
 * may be defined. A theme is a collection of templates.
 * @return string
 */
function template($templatePath, $variables = [], $themePath = NULL)
{
  if (!$themePath)
  {
    $themePath = getSiteSetting('theme_path');
  }
  if ($variables) extract($variables);
  ob_start();
  if (!is_readable($themePath . DIRECTORY_SEPARATOR . $templatePath))
  {
    setLog(['level' => 'warning', 'detail' => sprintf("%s template is not readable or does not exist", getSafeString($themePath . '/' . $templatePath))]);
  }
  include($themePath . '/' . $templatePath);
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
  $output = ($formatters != "raw" || !in_array('raw', $formatters)) ? $value : getSafeString($value);

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
