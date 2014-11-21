<?php
/**
 * Define pages url and content.
 */

$pages = [];

$pages['__PAGE_NOT_FOUND__'] = [
  'template' => 'page.php',
  'content' => function() {
      setHttpResponseCode(404);
      return "Oooops page not found";
    }
];

$pages['__ACCESS_DENIED__'] = [
  'path'     => '',
  'template' => 'page.php',
  'content' => function() {
      setHttpResponseCode(403);
      return "Access denied";
    }
];

$pages['homepage'] = [
    'path'     => '',
    'template' => 'page.php',
    'content'  =>  template('homepage.php'),
];

// how to merge setting from another file :
// $pages = mergeConfigFromFile($pages, 'myvendor/mymodule/config/pages.php');

return $pages;