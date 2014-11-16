<?php

/**
 * Run all validators on content form.
 * @param $datas
 * @return array
 */
function validateContentForm($datas) {
  $errors = array();

  if (!validateContentState($datas['state']))
  {
    $errors['state'][] = "Invalid state received";
  }

  if (!validateMachineName($datas['machine_name']))
  {
    $errors['machine_name'][] = "Invalid machine name ! ";
  }

  if (contentMachineAlreadyExists($datas['machine_name'] && empty($datas['id'])))
  {
    $errors['machine_name'][] = "Machine name already exist ! ";
  }

  return $errors;
}

/**
 * Validate state of a content type.
 * @param string $value : state id string.
 * @return bool
 */
function validateContentState($value) {
  $allowedStates = array_keys(getContentStates());
  if (in_array($value, $allowedStates)) {
    return TRUE;
  }
  return FALSE;
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