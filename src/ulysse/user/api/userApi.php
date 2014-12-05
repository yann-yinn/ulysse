<?php
/**
 * @FIXME env draft et online (entry points ?)
 */

define('USER_STATE_ACTIVE', 'active');
define('USER_STATE_BLOCKED', 'blocked');

/**
 * Get all content of the site
 * @param string $state
 * @return array
 */
function getUserList($state = null) {
  $db = getDbConnexion('db');
  $sql = [];
  $sql[] =  'SELECT * FROM users';
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
 * Shortcut to get a published content.
 * @param string $machineName
 * @param string $field : to return only a particular field of a content
 * @return array
 */
function getUser($machineName, $field = '') {
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
function getUserByMachineName($machineName, $state = CONTENT_STATE_ONLINE) {
  $db = getDbConnexion('db');
  $sql =  'SELECT * FROM users WHERE machine_name = :machine_name';

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
function updateUser($machine_name, array $datas) {

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
function createUser($datas) {

  $db      = getDbConnexion('db');
  $sql   = 'INSERT INTO users
  (machine_name, identifier, mail, first_name, password)
  VALUES (:machine_name, :identifier, :mail, :first_name, :password)';
  $query  = $db->prepare($sql);

  $identifier = $datas['identifier'];
  $mail = $datas['mail'];
  $first_anem = $datas['first_name'];
  $password = $datas['password'];
  $role = $datas['role'];

  $query->bindParam(':identifier', $identifier);
  $query->bindParam(':first_name', $first_name);
  $query->bindParam(':machine_name', $machine_name);
  $query->bindParam(':mail', $mail);
  $query->bindParam(':password', $password);


  return $query->execute();
}

function deleteUser($machine_name) {
  $db      = getDbConnexion('db');
  $sql = "DELETE FROM users
  WHERE machine_name=:machine_name;";
  $query  = $db->prepare($sql);
  $query->bindParam(':machine_name', $machine_name);
  return $query->execute();
}

function UserMachineAlreadyExists($machine_name) {
  $db = getDbConnexion();
  $sql = "SELECT machine_name FROM users WHERE machine_name = :machine_name";
  $query = $db->prepare($sql);
  $query->bindParam(':machine_name', $machine_name);
  $query->execute();
  $datas = $query->fetch();
  if ($datas) {
    return TRUE;
  }
  return FALSE;
}