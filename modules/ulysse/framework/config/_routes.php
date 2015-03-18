<?php

/**
 * Define pages url and content.
 */
// default homepage
$config['routes']['homepage'] = [
  'path'     => '',
  'format'  => 'html',
  'template' => 'page.php',
  'http method' => 'GET',
  'controller'  =>  function() {
      return ['content' => getTranslation('ulysse.framework.welcome')];
    }
];

// default html 404 page
$config['routes']['__HTTP_404__'] = [
  'template' => 'ulysse/framework/templates/page.php',
  'format'  => 'html',
  'http method' => 'GET',
  'controller' => function() {
      setHttpResponseCode(404);
      return ['content' => "Oooops page not found"];
    }
];

// default html 403 page.
$config['routes']['__HTTP_403__'] = [
  'template' => 'ulysse/framework/templates/page.php',
  'template' => 'page.php',
  'http method' => 'GET',
  'controller' => function() {
      setHttpResponseCode(403);
      return ['content' => "Access denied"];
    }
];



