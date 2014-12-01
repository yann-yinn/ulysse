<?php
/**
 * Define pages url and content.
 */

/**
 * You may redefined as you wish those existings pages.
 */
/*
// customize page for 404 not found error
$config['pages']['__HTTP_404__'] = [
  'layout' => 'page.php',
  'callable' => function() {
      setHttpResponseCode(404);
      return "Oooops page not found";
    }
];
// customize page for 403 not found error
$config['pages']['__HTTP_403__'] = [
  'layout' => 'page.php',
  'callable' => function() {
      setHttpResponseCode(403);
      return "Access denied";
    }
];
$config['pages']['ulysse.framework.homepage'] = [
  'path'     => '',
  'layout' => 'page.php',
  'callable'  =>  function() {
      $out = '';
      $out .= getTranslation('ulysse.framework.welcome');
      $datas = getContentByMachineName('homepage_bloc_1');
      if ($datas) {
        $out .= template('ulysse/content/templates/contentView.php', $datas);
      }
      return $out;
    }
];
*/
