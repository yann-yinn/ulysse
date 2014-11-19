<?php
/**
 * @FIXME add validation for content types.
 */

/**
 * Run all validators on content form.
 * @param $datas
 * @return array
 */
function validateContentForm($datas) {
  $errors = array();

  if (!validateContentType($datas['type']))
  {
    $errors['type'][] = "Invalid content type received";
  }

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
  $allowed = array_keys(getContentStates());
  if (in_array($value, $allowed)) {
    return TRUE;
  }
  return FALSE;
}

/**
 * Validate state of a content type.
 * @param string $value : state id string.
 * @return bool
 */
function validateContentType($value) {
  $allowed = array_keys(getContentTypes());
  if (in_array($value, $allowed)) {
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