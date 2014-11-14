<?php
/**
 * Define pages url and content.
 */

$pages = [];

$pages['homepage'] = [
    'path'     => '',
    'template' => 'layout.php',
    'content'  =>  template('homepage.php'),
];

// how to merge setting from another file :
// $pages = merge_config_from_file($pages, 'myvendor/mymodule/config/pages.php');

return $pages;