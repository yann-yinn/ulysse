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
  'template' => 'page.php',
  'format' => 'html',
  'datas' => function() {
    return ['content' => "Hello world"];
  },
];


// override default homepage.
/*
$config['routes']['ulysse.framework.homepage'] = [
  'path'     => '',
  'controller'  =>  function() {
      return getTranslation('ulysse.framework.welcome');
    }
];
*/

// override default 404 page.
/*
$config['routes']['__HTTP_404__'] = [
  'controller' => function() {
      setHttpResponseCode(404);
      return "Oooops page not found";
    }
];
*/

// override default 403 page.
/*
$config['routes']['__HTTP_403__'] = [
  'controller' => function() {
      setHttpResponseCode(403);
      return "Access denied";
    }
];
*/



