<?php
/**
 * PHP framework.
 *
 * Features :
 * - router
 * - logs
 * - config files by environment (dev, prod etc...)
 * - string translations
 * - theming engine : themes, template and template formatters
 */

define("CONFIG_EXAMPLE_DIRECTORY", 'example.config');
define('CONFIG_DIRECTORY', '../config');
define('CONTENT_DIRECTORY', 'static_content');
define('ROUTES_FILE', '../config/routes.php');
define('TRANSLATIONS_FILEPATH', '../config/translations.php');
define('SETTINGS_FILENAME', 'settings');
define('TEMPLATE_FORMATTERS_FILE', '../config/template_formatters.php');

/* =====================
   FRAMEWORK BOOTSTRAP
   ===================== */

/**
 * Bootstrap the okc framework : listen http request and map it to
 * a php controller.
 *
 * @param string $env
 *   Environment id : "dev", "prod" or whatever is needed.
 *   For a "dev" environment, "settings_dev.php" file will be load automatically.
 * @code
 * require_once "../src/okc/framework/bootstrap.php";
bootstrapFramework('dev');
 * @endocde
 */
function bootstrapFramework($env = '')
{

  if (!file_exists(CONFIG_DIRECTORY)) {
    echo frameworkInstallationPage($env);
    exit;
  }

  writeLog(['level' => 'notification', 'detail' => 'framework environment is set to : ' . sanitizeString($env)]);
  // try to display framework log even if php a fatal error occured
  register_shutdown_function("phpFatalErrorHandler");
  session_start();

  // add include paths for class autoloaders
  $includePaths = ['..', '../src', '../vendors'];
  addPhpIncludePaths(['..', '../src', '../vendors']);
  writeLog(['detail' => "Add following php include paths : " . implode(', ', $includePaths)]);

  registerPsr0ClassAutoloader();

  setContextVariable('env', $env);
  setContextVariable('time_start', microtime(TRUE));

  loadTranslations();
  $settings = loadSettings($env);

  require TEMPLATE_FORMATTERS_FILE;

  $controllerOutput = executeControllerFromHttpRequest(getRoutes());
  echo $controllerOutput;

  writeLog(['level'    => 'notification', 'detail'   => "page content is '" . sanitizeString($controllerOutput) . "'"]);
  writeLog(['level' => 'notification', 'detail' => "Settings loaded : " . var_export($settings, TRUE)]);

  if (getSetting('display_developper_toolbar') === TRUE)
  {
    require_once "../src/okc/framework/developper_toolbar.php";
  }
}

function frameworkInstallationPage($env) {
  $out = '';
  $out .= '<h1>Welcome to framework installation</h1>';
  $out .= 'Please rename "example.config" directory to "config" to start using framework.';
  return $out;
}

/* =====================
   LOGS
   ===================== */

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

function getLogs()
{
  return $GLOBALS['_LOGS'];
}

/* =====================
   CONTEXT
   ===================== */

function setContextVariable($id, $value)
{
  $GLOBALS['_CONTEXT'][$id] = $value;
}

function getContextVariable($id)
{
  return $GLOBALS['_CONTEXT'][$id];
}

function getAppContext()
{
  return $GLOBALS['_CONTEXT'];
}

/* =====================
   CONTEXT
   ===================== */

/**
 * Add an http header, using http or https given the curre
 * @param int $code : http response code 200, 400 etc...
 * @param $message : message associated to the http response code
 * @param $protocol (
 */
function addHttpResponseHeader($code, $message = null, $protocol = null) {
  $list = [
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
    if (!empty($list[$code]))
    {
      $message = $list[$code];
    }
    else
    {
      writeLog(['level' => 'warning', 'detail' => sprintf("No message found for http code %s", sanitizeString($code))]);
    }
  }
  header(sprintf("%s %s %s", sanitizeString($protocol), sanitizeString($code), sanitizeString($message)));
}

/* =====================
   ROUTER
   ===================== */

/**
 * Run our virtual server.
 * @param $routes
 * @return bool
 */
function executeControllerFromHttpRequest($routes)
{
  $script_name = getAppScriptName();
  writeLog(['level'  => 'notification', 'detail' => sprintf("Script name is %s", sanitizeString($script_name))]);

  $base_path = getAppBasePath($script_name);
  writeLog(['level' => 'notification', 'detail' => sprintf("base path is %s", sanitizeString($base_path))]);
  setContextVariable('base_path', sanitizeString($base_path));

  $path = extractPathFromHttpRequest($script_name, $base_path);
  writeLog(['level'    => 'notification', 'detail'   => "Framework determine path from http request as '$path'"]);
  setContextVariable('path', sanitizeString($path));

  $route = getRouteByPath($path, $routes);
  if (!$route) {
    $controller = getSetting('route_not_found_controller');
    $content = $controller['return']();
  }
  else {
    writeLog(['level'    => 'notification', 'detail'   => "found route : <pre>" . var_export($route, TRUE) . '</pre>']);
    $_CONTEXT[__NAMESPACE__]['content'] = $content = getRouteOutput($route);

  }
  return $content;
}

/**
 * Load all routes defined in files.
 * @return mixed
 */
function getRoutes()
{
  return include ROUTES_FILE;
}

/**
 * Extract path from incoming http request.
 *
 * @param string $script_name as returned by getAppScriptName()
 * @param string $base_path as returned by getAppBasePath()
 * @return string
 */
function extractPathFromHttpRequest($script_name, $base_path)
{
  // Remove base path (for installations in subdirectories) from URI.
  $route_path = substr_replace($_SERVER['REQUEST_URI'], '', 0, strlen($base_path));
  // Remove scriptname "index.php" if present. scriptname is not present at all if .htaccess is enabled.
  if (strpos($route_path, $script_name) === 0)
  {
    $route_path = str_replace($script_name, '', $route_path);
  }
  // remove query string and slashes.
  return trim(parse_url($route_path, PHP_URL_PATH), '/');
}

/**
 * If there is several routes, last found route will be used.
 * @param $path
 * @param $routes
 * @return array
 */
function getRouteByPath($path, $routes)
{
  $route = [];
  foreach ($routes as $id => $datas)
  {
    if ($routes[$id]['path'] == $path)
    {
      $route = $routes[$id];
      $route['id'] = $id;
    }
  }
  return $route;
}

/**
 * @param string $route as returned by getRouteByPath()
 * @return bool
 */
function getRouteOutput($route)
{
  return is_string($route['return']) ? $route['return'] : $route['return']();
}

/**
 * Return base path, if applications is installed in a subfolder.
 *
 * @param string $script_name as returned by getAppScriptName()
 * @return string
 */
function getAppBasePath($script_name)
{
  return str_replace($script_name, '', $_SERVER['SCRIPT_NAME']);
}

/**
 * file php which is the entry point of your application; usually "index.php".
 */
function getAppScriptName()
{
  return basename($_SERVER['SCRIPT_NAME']);
}

/* =====================
   SETTINGS
   ===================== */

function loadSettings($env) {
  // load settings and environment settings if any
  // for example, load settings.php file AND settings_dev.php file
  // if settings_dev.php is readable.
  $settings = include CONFIG_DIRECTORY . DIRECTORY_SEPARATOR . SETTINGS_FILENAME . ".php";
  if ($env)
  {
    $settings_env_file = CONFIG_DIRECTORY . DIRECTORY_SEPARATOR . SETTINGS_FILENAME . "_$env.php";
    if (is_readable($settings_env_file))
    {
      $settings_env = include $settings_env_file;
      $settings = array_merge($settings, $settings_env);
    }
  }
  $GLOBALS['_SETTINGS'] = $settings;
  return $settings;
}

function getSetting($id)
{
  return $GLOBALS['_SETTINGS'][$id];
}

function merge_config_file($variables, $filepath) {
  return $variables += include $filepath;
}

/* =====================
   TRANSLATION
   ===================== */

function getCurrentLanguage()
{
  $current_language = getSetting('language_default');
  if (isset($_REQUEST['language'])) {
    $requested_language = (string)sanitizeString($_REQUEST['language']);
    $defined_languages = getSetting('languages');
    foreach ($defined_languages as $id => $datas) {
      if ($defined_languages[$id]['query'] == $requested_language) {
        $current_language = $requested_language;
      }
    }
  }
  return $current_language;
}

function loadTranslations()
{
  return $GLOBALS['_TRANSLATIONS'] = include TRANSLATIONS_FILEPATH;
}

function getTranslation($id, $language = NULL)
{
  if (!$language)
  {
    $language = getCurrentLanguage();
  }

  return $GLOBALS['_TRANSLATIONS'][$id][$language];
}

/* =====================
   MISC
   ===================== */

function getStaticContent($path, $language = '') {
  if (!$language) {
    $language = getCurrentLanguage();
  }
  $content_filepath = '../' . DIRECTORY_SEPARATOR . CONTENT_DIRECTORY . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . $path;
  return file_get_contents($content_filepath);
}

/**
 * Register a basic psr0 class autoloader.
 */
function registerPsr0ClassAutoloader()
{
  spl_autoload_register(function($class){require_once str_replace('\\','/', $class).'.php';});
}

/**
 * @param array $include_paths
 *   a list of php path that will be add to php include paths
 */
function addPhpIncludePaths($include_paths)
{
  set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $include_paths));
}

/**
 * Sanitize a variable, to display it.
 * @param string $value
 * @return string html encoded value
 */
function sanitizeString($value)
{
  return htmlspecialchars($value, ENT_QUOTES, 'utf-8');
}

/**
 * Shortcut do display a print_r for debug purposes
 * @param array $variable
 */
function pr($variable)
{
  printf("%s %s %s", 'function ' .  __FUNCTION__, 'line ' . __LINE__, 'file ' .  __FILE__);
  echo '<pre>' . print_r($variable) . '</pre>';
}

function phpFatalErrorHandler()
{
  if(error_get_last() !== NULL) {
    require_once "developper_toolbar.php";
  }
}

/* =====================
   THEMING / TEMPLATE
   ===================== */

/**
 * Render a specific template file
 *
 * @param string $file : file path
 * @param array $variables
 * @param string $theme_path : theme to use. Different themes
 * may be defined. A theme is a collection of templates.
 * @return string
 */
function template($file, $variables, $theme_path = NULL)
{
  if (!$theme_path)
  {
    $theme_path = getSetting('theme_path');
  }
  if ($variables) extract($variables);
  ob_start();
  if (!is_readable($theme_path . '/' . $file))
  {
    writeLog(['level' => 'warning', 'detail' => sprintf("%s template is not readable or does not exist", sanitizeString($theme_path . '/' . $file))]);
  }
  include($theme_path . '/' . $file);
  return ob_get_clean();
}

/**
 * Print a value in a secured way. Do not use "print" or "echo" in templates when possible, as
 * this function take care of encoding malicious entities.
 *
 * @param string $value : a single string value to print.
 * @param array $formatters : array of function names to format the value.
 * @return string
 */
function e($value, $formatters = [])
{
  // secure variable
  if (!isset($formatters['raw']))
  {
    $output = sanitizeString($value);
  }
  else
  {
    $output = $value;
  }

  // then apply formatters if any
  if ($formatters)
  {
    if (is_string($formatters))
    {
      $function = $formatters;
      $output = $function($output);
    }
    else
    {
      foreach ($formatters as $function)
      {
        if ($function != "raw")
        {
          $output = $function($output);
        }
      }
    }
  }
  print $output;
}
