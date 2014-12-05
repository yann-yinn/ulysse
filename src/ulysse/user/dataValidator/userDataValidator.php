<?php
/**
 * @FIXME add validation for content types.
 */

/**
 * Run all validators on content form.
 * @param $datas
 * @return array
 */
function userFormDataValidator($datas) {

  $errors = [
    'count' => 0,
    'fields'  => [
      'role'  => [],
      'mail' => [],
      'identifier' => [],
      'first_name' => [],
      'last_name' => [],
      'password' => [],
    ],
  ];

  $mail = trim($datas['mail']);
  if (empty($mail))
  {
    $errors['fields']['mail'][] = "Le champ email est requis";
    $errors['count']++;
  }

  if (!userEmailDataValidator($datas['mail']))
  {
    $errors['fields']['mail'][] = "L'email est invalide.";
    $errors['count']++;
  }

  $password = trim($datas['password']);
  if (empty($password))
  {
    $errors['fields']['password'][] = "Le champ password est requis";
    $errors['count']++;
  }

  if (!userPasswordDataValidator($datas['password']))
  {
    $errors['fields']['password'][] = "Le mot de passe est invalide.";
    $errors['count']++;
  }

  $identifier = trim($datas['identifier']);
  if (empty($identifier))
  {
    $errors['fields']['identifier'][] = "Le champ identifier est requis";
    $errors['count']++;
  }

  return $errors;
}

/**
 * Validate an email
 * @param string $value : email
 * @return bool
 */
function userEmailDataValidator($value) {
  return filter_var($value, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate state a user password
 * @param string $password : password
 * @return bool
 */
function userPasswordDataValidator($password) {
  $uppercase = preg_match('@[A-Z]@', $password);
  $lowercase = preg_match('@[a-z]@', $password);
  $number    = preg_match('@[0-9]@', $password);

  if(!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
    return FALSE;
  }
  return TRUE;
}

