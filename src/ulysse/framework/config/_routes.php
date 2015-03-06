<?php
/**
 * Define pages url and content.
 */
// default homepage
$config['routes']['ulysse.framework.homepage'] = [
  'path'     => '',
  'template' => 'page.php',
  'datas'  =>  function() {
      return ['content' => getTranslation('ulysse.framework.welcome')];
    }
];

// default 404 page
$config['routes']['__HTTP_404__'] = [
  'template' => 'page.php',
  'datas' => function() {
      setHttpResponseCode(404);
      return ['content' => "Oooops page not found"];
    }
];

// default 403 page.
$config['routes']['__HTTP_403__'] = [
  'template' => 'page.php',
  'datas' => function() {
      setHttpResponseCode(403);
      return ['content' => "Access denied"];
    }
];



