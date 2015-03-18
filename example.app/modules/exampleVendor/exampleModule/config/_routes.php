<?php
/**
 * Define pages url and content.
 */

/**
 * Override default homepage
 */
$config['routes']['homepage'] = [
  'path'     => '',
  'http method' => 'GET',
  'template' => 'exampleVendor/exampleModule/templates/page.php',
  'format' => 'html',
  'controller'  =>  function() {
      return ['content' => "Ulysse works."];
    }
];

/**
 * Example route.
 *
 * visit localhost/{yoursite}/www/index.php/hello to view it.
 */
$config['routes']['helloWorld'] = [
  'path' => 'hello',
  'http method' => 'GET',
  'template' => 'exampleVendor/exampleModule/templates/page.php',
  'format' => 'html',
  'controller' => function() {
      return ['content' => "Hello world"];
    },
];



