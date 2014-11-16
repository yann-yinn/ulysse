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
$pages['homepage'] = [
  'path'     => '',
  'layout' => 'page.php',
  'layout_variables' => [
    'zone_top' => function() {
        $content =  getContentByMachineName("homepage_bloc_1");
        return template('contentView.php', $content, 'okc/content/templates');
      },
  ],
  'content'  =>  function() {
      template('homepage.php');
    }
];

// how to merge setting from another file :
$pages = mergeConfigFromFile($pages, 'okc/content/config/pages.php');

return $pages;