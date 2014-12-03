<?php
/**
 * Define pages url and content.
 */


$config['routes']['__HTTP_404__'] = [
  'layout' => 'page.php',
  'callable' => function() {
      setHttpResponseCode(404);
      return "Oooops page not found";
    }
];
$config['routes']['__HTTP_403__'] = [
  'path'     => '',
  'layout' => 'page.php',
  'callable' => function() {
      setHttpResponseCode(403);
      return "Access denied";
    }
];
$config['routes']['ulysse.framework.homepage'] = [
  'path'     => '',
  'layout' => 'page.php',
  'callable'  =>  function() {
      $out = '';
      $out .= getTranslation('ulysse.framework.welcome');
      return $out;
    }
];
