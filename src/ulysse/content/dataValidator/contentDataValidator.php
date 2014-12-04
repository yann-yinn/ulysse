<?php
/**
 * @FIXME add validation for content types.
 */

/**
 * Run all validators on content form.
 * @param $datas
 * @return array
 */
function contentFormDataValidator($datas) {

  $errors = [
    'count' => 0,
    'fields'  => [
      'type'  => [],
      'state' => [],
      'title' => [],
      'content' => [],
      'machine_name' => [],
    ],
  ];

  if (!contentTypeDataValidator($datas['type']))
  {
    $errors['fields']['type'][] = "Invalid content type received";
    $errors['count']++;
  }

  if (!contentStateDataValidator($datas['state']))
  {
    $errors['fields']['state'][] = "Invalid state received";
    $errors['count']++;
  }

  if (!_validateMachineName($datas['machine_name']))
  {
    $errors['fields']['machine_name'][] = "Invalid machine name ! ";
    $errors['count']++;
  }

  if (contentMachineAlreadyExists($datas['machine_name']) && $datas['action'] == 'create')
  {
    $errors['fields']['machine_name'][] = "Machine name already exist ! ";
    $errors['count']++;
  }

  return $errors;
}

/**
 * Validate state of a content type.
 * @param string $value : state id string.
 * @return bool
 */
function contentStateDataValidator($value) {
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
function contentTypeDataValidator($value) {
  $allowed = array_keys(getContentTypes());
  if (in_array($value, $allowed)) {
    return TRUE;
  }
  return FALSE;
}

