<?php
/**
 * @FIXME env draft et online (entry points ?)
 */

define('CONTENT_STATE_ONLINE', 'online');
define('CONTENT_STATE_DRAFT', 'draft');
define('CONTENT_STATE_TRASH', 'trash');

function getContentStates() {
  $contentStates = getSetting('ulysse.content.states');
  return $contentStates;
}

function getContentTypes() {
  $contentStates = getSetting('ulysse.content.types');
  return $contentStates;
}

/**
 * Get all content of the site
 * @param string $state
 * @return array
 */
function getContentList($state = null) {
  $db = getDbConnexion('db');
  $sql = [];
  $sql[] =  'SELECT * FROM content';
  if ($state) {
    $sql[] = 'WHERE state = :state';
  }
  $sql[] = 'ORDER BY created DESC';
  $query  = $db->prepare(implode(' ', $sql));
  if ($state) {
    $query->bindParam(':state', $state, PDO::PARAM_STR);
  }
  $query->execute();
  $datas = $query->fetchAll();
  return $datas;
}

/**
 * Fetch a content by its id
 * @param int $id
 * @return array of content datas
 * @deprecated, we use machine name each time now, for deployment reasons
function getContentById($id) {
$db = getDbConnexion('db');
$sql =  'SELECT * FROM content WHERE id = :id';
$query = $db->prepare($sql);
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();
$datas = $query->fetch();
return $datas;
}
 */

/**
 * Shortcut to get a published content.
 * @param $machineName
 * @return array
 */
function getContent($machineName, $field = []) {
  $datas = getContentByMachineName($machineName, $state = CONTENT_STATE_ONLINE);
  if ($field) {
    return $datas[$field];
  }
  return $datas;
}


/**
 * Get a content by its uniq machine name
 * @param string $machineName
 * @param string $state : online, trash, draft
 * @return array
 */
function getContentByMachineName($machineName, $state = CONTENT_STATE_ONLINE) {
  $db = getDbConnexion('db');
  $sql =  'SELECT * FROM content WHERE machine_name = :machine_name';

  if ($state != CONTENT_STATE_ONLINE) {
    $sql .= ' AND state = :state';
  }

  $query = $db->prepare($sql);
  $query->bindParam(':machine_name', $machineName, PDO::PARAM_STR);

  if ($state != CONTENT_STATE_ONLINE ) {
    $query->bindParam(':state', $state, PDO::PARAM_STR);
  }

  $query->execute();
  $datas = $query->fetch();
  return $datas;
}

/**
 * Update a content by its machine_name
 * @param string $machine_name
 * @param array $datas
 * @return bool
 */
function updateContent($machine_name, array $datas) {

  $content = $datas['content'];
  $title   = $datas['title'];
  $state   = $datas['state'];
  $changed = time();

  $db      = getDbConnexion('db');
  $query   = "UPDATE content SET
   title = :title,
   content = :content,
   changed = :changed,
   state = :state
   WHERE machine_name = :machine_name";
  $query  = $db->prepare($query);

  $query->bindParam(':content', $content);
  $query->bindParam(':title', $title);
  $query->bindParam(':changed', $changed, PDO::PARAM_INT);
  $query->bindParam(':state', $state);
  $query->bindParam(':machine_name', $machine_name);

  return $query->execute();

}

/**
 * Insert a new content in database
 * @param $machine_name.
 * @param $datas
 * @return bool
 */
function createContent($machine_name, $datas) {

  $db      = getDbConnexion('db');
  $sql   = 'INSERT INTO content
  (type, machine_name, title, content, created, changed, state)
  VALUES (:type, :machine_name, :title, :content, :created, :changed, :state)';
  $query  = $db->prepare($sql);

  $content = $datas['content'];
  $title   = $datas['title'];
  $state   = $datas['state'];
  $type    = $datas['type'];
  $created = time();
  $changed = time();

  $query->bindParam(':content', $content);
  $query->bindParam(':machine_name', $machine_name);
  $query->bindParam(':type', $type);
  $query->bindParam(':title', $title);
  $query->bindParam(':created', $created, PDO::PARAM_INT);
  $query->bindParam(':changed', $changed, PDO::PARAM_INT);
  $query->bindParam(':state', $state);

  return $query->execute();
}

function deleteContent($machine_name) {
  $db      = getDbConnexion('db');
  $sql = "DELETE FROM content
  WHERE machine_name=:machine_name;";
  $query  = $db->prepare($sql);
  $query->bindParam(':machine_name', $machine_name);
  return $query->execute();
}

function contentMachineAlreadyExists($machine_name) {
  $db = getDbConnexion();
  $sql = "SELECT machine_name FROM content WHERE machine_name = :machine_name";
  $query = $db->prepare($sql);
  $query->bindParam(':machine_name', $machine_name);
  $query->execute();
  $datas = $query->fetch();
  if ($datas) {
    return TRUE;
  }
  return FALSE;
}