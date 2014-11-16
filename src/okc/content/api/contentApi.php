<?php
/**
 * @FIXME env draft et online (entry points ?)
 */

define('CONTENT_STATE_ONLINE', 'online');
define('CONTENT_STATE_DRAFT', 'draft');
define('CONTENT_STATE_TRASH', 'trash');

function getContentStates() {
  $contentStates = getSetting('okc.content.states');
  return $contentStates;
}

function viewContentById($id) {

}

/**
 * Get all content of the site
 * @return array
 */
function getContentList() {
  $db      = getDbConnexion('db');
  $sql =  'SELECT * FROM content ORDER BY id DESC';
  $query  = $db->prepare($sql);
  $query->execute();
  $datas = $query->fetchAll();
  return $datas;
}

/**
 * Fetch a content by its id
 * @param int $id
 * @return array of content datas
 */
function getContentById($id) {
  $db = getDbConnexion('db');
  $sql =  'SELECT * FROM content WHERE id = :id';
  $query = $db->prepare($sql);
  $query->bindParam(':id', $id, PDO::PARAM_INT);
  $query->execute();
  $datas = $query->fetch();
  return $datas;
}

/**
 * Get a content by its uniq machine name
 * @param string $machineName
 * @return array
 */
function getContentByMachineName($machineName) {
  $db = getDbConnexion('db');
  $sql =  'SELECT * FROM content WHERE machine_name = :machine_name';
  $query = $db->prepare($sql);
  $query->bindParam(':machine_name', $machineName, PDO::PARAM_STR);
  $query->execute();
  $datas = $query->fetch();
  return $datas;
}

/**
 * Update a content by its id
 * @param int $id
 * @param array $datas
 * @return bool
 */
function updateContentById($id, array $datas) {

  $content = $datas['content'];
  $title   = $datas['title'];
  $state   = $datas['state'];
  $machine_name = $datas['machine_name'];
  $changed = time();

  $db      = getDbConnexion('db');
  $query   = "UPDATE content SET
   title = :title,
   content = :content,
   changed = :changed,
   state = :state,
   machine_name = :machine_name
   WHERE id = :id";
  $query  = $db->prepare($query);

  $query->bindParam(':content', $content, PDO::PARAM_STR);
  $query->bindParam(':title', $title, PDO::PARAM_STR);
  $query->bindParam(':changed', $changed, PDO::PARAM_INT);
  $query->bindParam(':state', $state, PDO::PARAM_STR);
  $query->bindParam(':id', $id, PDO::PARAM_INT);
  $query->bindParam(':machine_name', $machine_name, PDO::PARAM_STR);

  return $query->execute();

}

/**
 * Insert a new content in database
 * @param $datas
 * @return bool
 */
function saveNewContent($datas) {

  $db      = getDbConnexion('db');
  $sql   = 'INSERT INTO content (type, machine_name, title, content, created, changed, state)
  VALUES (:type, :machine_name, :title, :content, :created, :changed, :state)';
  $query  = $db->prepare($sql);

  $content = $datas['content'];
  $title   = $datas['title'];
  $machine_name = $datas['machine_name'];
  $state   = $datas['state'];
  $type    = $datas['type'];
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