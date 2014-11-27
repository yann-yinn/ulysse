<?php
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
