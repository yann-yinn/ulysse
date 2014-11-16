<?php

define('CONTENT_STATE_ONLINE', 'online');
define('CONTENT_STATE_DRAFT', 'draft');
define('CONTENT_STATE_TRASH', 'trash');

function getContentStates() {
  return [
    CONTENT_STATE_ONLINE => [
      'id' => CONTENT_STATE_ONLINE,
      'title' => 'Online',
    ],
    CONTENT_STATE_DRAFT => [
      'id' => CONTENT_STATE_DRAFT,
      'title' => 'Draft',
    ],
    CONTENT_STATE_TRASH => [
      'id' => CONTENT_STATE_TRASH,
      'title' => 'Trash can',
    ],
  ];
}

function viewContentById($id) {

}

function getContentById($id) {
  $db = getDbConnexion('db');
  $sql =  'SELECT * FROM content WHERE id = :id';
  $query = $db->prepare($sql);
  $query->bindParam(':id', $id, PDO::PARAM_INT);
  $query->execute();
  $datas = $query->fetch();
  return $datas;
}

function updateContentById($id, $datas) {

  $id = $datas['id'];
  $content = $datas['content'];
  $title   = $datas['title'];
  $state   = $datas['state'];
  $type    = 'content';
  $changed = time();

  $db      = getDbConnexion('db');
  $query   = "UPDATE content SET
   title = :title,
   content = :content,
   changed = :changed,
   state = :state
   WHERE id = :id";
  $query  = $db->prepare($query);

  $query->bindParam(':content', $content, PDO::PARAM_STR);
  $query->bindParam(':title', $title, PDO::PARAM_STR);
  $query->bindParam(':changed', $changed, PDO::PARAM_INT);
  $query->bindParam(':state', $state, PDO::PARAM_STR);
  $query->bindParam(':id', $id, PDO::PARAM_INT);

  return $query->execute();

}

function saveNewContent($datas) {

  $db      = getDbConnexion('db');
  $sql   = 'INSERT INTO content (type, machine_name, title, content, created, changed, state)
  VALUES (:type, :machine_name, :title, :content, :created, :changed, :state)';
  $query  = $db->prepare($sql);

  $content = $datas['content'];
  $title   = $datas['title'];
  $machine_name = $datas['machine_name'];
  $state = $datas['state'];
  $type    = 'content';
  $created = time();
  $changed = time();

  $query->bindParam(':content', $content, PDO::PARAM_STR);
  $query->bindParam(':machine_name', $machine_name, PDO::PARAM_STR);
  $query->bindParam(':type', $type, PDO::PARAM_STR);
  $query->bindParam(':title', $title, PDO::PARAM_STR);
  $query->bindParam(':created', $created, PDO::PARAM_INT);
  $query->bindParam(':changed', $changed, PDO::PARAM_INT);
  $query->bindParam(':state', $state, PDO::PARAM_INT);

  return $query->execute();
}