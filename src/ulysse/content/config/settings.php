<?php

/**
 * Define ulysse Content states :
 * - online : content must appear on the live site
 * - draft : content will not be visible on the live site
 * - trash : content has been deleted but may be recovered
 * Thos keys will be value stored in "states" column of "content" table.
 */
$settings['ulysse.content.states'] = [
  'online' => [
    'title' => 'Online',
  ],
  'draft' => [
    'title' => 'Draft',
  ],
  'trash' => [
    'title' => 'Trash can',
  ],
];

/**
 * Define allow content types on the site.
 */
$settings['ulysse.content.types'] = [
  'content' => [
    'title' => 'Default content',
  ],
  'setting' => [
    'title' => 'Site setting',
  ],
];


return $settings;