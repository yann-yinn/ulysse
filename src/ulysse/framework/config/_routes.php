<?php
/**
 * Define pages url and content.
 */

// default homepage
$config['routes']['ulysse.framework.homepage'] = [
  'path'     => '',
  'layout' => 'page.php',
  'callable'  =>  function() {
      return getTranslation('ulysse.framework.welcome');
    }
];

// hello world test page.
$config['routes']['helloWorld'] = [
  'path' => 'hello-world',
  'layout' => 'page.php',
  'callable' => function() {
      return "Hello World";
    }
];

// default 404 page
$config['routes']['__HTTP_404__'] = [
  'layout' => 'page.php',
  'callable' => function() {
      setHttpResponseCode(404);
      return "Oooops page not found";
    }
];

// default 403 page.
$config['routes']['__HTTP_403__'] = [
  'layout' => 'page.php',
  'callable' => function() {
      setHttpResponseCode(403);
      return "Access denied";
    }
];



