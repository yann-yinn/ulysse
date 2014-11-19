<?php
/**
 * Define pages url and content.
 */

$pages = [];

$pages['__PAGE_NOT_FOUND__'] = [
  'layout' => 'page.php',
  'content' => function() {
      addHttpResponseHeader(404);
      return "Oooops page not found";
    }
];
$pages['__ACCESS_DENIED__'] = [
  'path'     => '',
  'layout' => 'page.php',
  'content' => function() {
      addHttpResponseHeader(403);
      return "Access denied";
    }
];
$pages['okc.framework.homepage'] = [
  'path'     => '',
  'layout' => 'page.php',
  'content'  =>  function() {
      $out = '';
      $out .= getTranslation('okc.framework.welcome');
      $datas = getContentByMachineName('homepage_bloc_1');
      if ($datas) {
        $out .= template('contentView.php', $datas, 'okc/content/templates');
      }
      return $out;
    }
];

// how to merge setting from another file :
$pages = mergeConfigFromFile($pages, 'okc/content/config/pages.php');

return $pages;