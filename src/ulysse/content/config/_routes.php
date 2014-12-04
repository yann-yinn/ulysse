<?php

// list all site contents
$config['routes']['ulysse.content.list'] = [
  'path' => 'admin',
  'theme' => getSetting('theme_admin'),
  'layout' => 'page.php',
  'callable' => function() {
      $route = getRouteById('ulysse.content.list.online');
      return $route['callable']();
    }
];

// list all site contents
$config['routes']['ulysse.content.list.online'] = [
  'parent' => 'ulysse.content.list',
  'path' => 'admin/content/list/online',
  'theme' => getSetting('theme_admin'),
  'layout' => 'page.php',
  'callable' => function() {
      return template('ulysse/content/template/contentList.php', [
          'datas' => getContentList(CONTENT_STATE_ONLINE)
        ]);
    }
];

// list all site contents
$config['routes']['ulysse.content.list.draft'] = [
  'parent' => 'ulysse.content.list',
  'path' => 'admin/content/list/draft',
  'theme' => getSetting('theme_admin'),
  'layout' => 'page.php',
  'callable' => function() {
      return template('ulysse/content/template/contentList.php', [
          'datas' => getContentList(CONTENT_STATE_DRAFT)
        ]);
    }
];

// list all site contents
$config['routes']['ulysse.content.list.trash'] = [
  'parent' => 'ulysse.content.list',
  'path' => 'admin/content/list/trash',
  'theme' => getSetting('theme_admin'),
  'layout' => 'page.php',
  'callable' => function() {
      return template('ulysse/content/template/contentList.php', [
          'datas' => getContentList(CONTENT_STATE_TRASH)
        ]);
    }
];

// display an add / edit content form
$config['routes']['ulysse.content.create'] = [
  'path' => 'admin/content/create',
  'theme' => getSetting('theme_admin'),
  'layout' => 'page.php',
  'callable' => function() {
      return contentCreateController();
    }
];

// display an add / edit content form
$config['routes']['ulysse.content.update'] = [
  'path' => 'admin/content/edit',
  'theme' => getSetting('theme_admin'),
  'layout' => 'page.php',
  'callable' => function() {
      return contentUpdateController();
    }
];

// save content to database
$config['routes']['ulysse.content.save'] = [
  'path' => 'admin/content/save',
  'theme' => getSetting('theme_admin'),
  'layout' => 'page.php',
  'callable' => function () {
      return contentFormSaveController();
    }
];

// confirm deletion
$config['routes']['ulysse.content.confirmDeletion'] = [
  'path' => 'admin/content/delete/confirm',
  'theme' => getSetting('theme_admin'),
  'layout' => 'page.php',
  'callable' => function () {
      return contentDeleteConfirmController();
    }
];

// confirm deletion
$config['routes']['ulysse.content.delete'] = [
  'path' => 'admin/content/delete',
  'theme' => getSetting('theme_admin'),
  'layout' => 'page.php',
  'callable' => function () {
      return contentDeleteController();
    }
];


