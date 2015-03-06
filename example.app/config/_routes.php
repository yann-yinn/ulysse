<?php
/**
 * Define pages url and content.
 */

/**
 * Example route.
 * visit localhost/{yoursite}/www/public/index.php/hello to view it.
 */
$config['routes']['helloWorld'] = [
  'path' => 'hello',
  'controller' => function() {
      return template('page.php', ['content' => "Hello world"]);
    }
];

// override default homepage.
/*
$config['routes']['ulysse.framework.homepage'] = [
  'path'     => '',
  'layout' => 'page.php',
  'callable'  =>  function() {
      return getTranslation('ulysse.framework.welcome');
    }
];
*/

// override default 404 page.
/*
$config['routes']['__HTTP_404__'] = [
  'layout' => 'page.php',
  'callable' => function() {
      setHttpResponseCode(404);
      return "Oooops page not found";
    }
];
*/

// override default 403 page.
/*
$config['routes']['__HTTP_403__'] = [
  'layout' => 'page.php',
  'callable' => function() {
      setHttpResponseCode(403);
      return "Access denied";
    }
];
*/



