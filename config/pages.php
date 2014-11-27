<?php
/**
 * Define pages url and content.
 */

$pages = [];

$pages['__PAGE_NOT_FOUND__'] = [
  'layout' => 'page.php',
  'callable' => function() {
      setHttpResponseCode(404);
      return "Oooops page not found";
    }
];
$pages['__ACCESS_DENIED__'] = [
  'path'     => '',
  'layout' => 'page.php',
  'callable' => function() {
      setHttpResponseCode(403);
      return "Access denied";
    }
];
$pages['ulysse.framework.homepage'] = [
  'path'     => '',
  'layout' => 'page.php',
  'callable'  =>  function() {
      $out = '';
      $out .= getTranslation('ulysse.framework.welcome');
      $datas = getContentByMachineName('homepage_bloc_1');
      if ($datas) {
        $out .= template('ulysse/content/templates/contentView.php', $datas);
      }
      return $out;
    }
];
