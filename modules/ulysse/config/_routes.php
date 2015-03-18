<?php

/**
 * Define pages url and content.
 */
// default homepage
$config['routes']['']['GET'] = [
  'format'      => 'html',
  'template'    => 'page.php',
  'controller'  =>  function() {
      return ['content' => getTranslation('ulysse.framework.welcome')];
    }
];

// default html 404 page
$config['routes']['__HTTP_404__']['GET'] = [
  'template'   => 'ulysse/templates/page.php',
  'format'     => 'html',
  'controller' => function() {
      setHttpResponseCode(404);
      return ['content' => "Oooops page not found"];
    }
];

// default html 403 page.
$config['routes']['__HTTP_403__']['GET'] = [
  'template'   => 'ulysse/templates/page.php',
  'format'     => 'html',
  'controller' => function() {
      setHttpResponseCode(403);
      return ['content' => "Access denied"];
    }
];



