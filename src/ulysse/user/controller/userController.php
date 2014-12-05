<?php

require_once 'ulysse/user/dataValidator/userDataValidator.php';

/**
 * Display all sites content with edit / add / delete link, in all states
 * (draft, online, trash )
 * @return string
 */
function userListController() {
  return template('ulysse/content/template/contentDashboard.php');
}

/**
 * Display a form to add / edit a content.
 * @return string
 */
function userCreateController() {

  $user['role'] = 'admin';
  $user['action'] = 'create';
  $user['identifier'] = '';
  $user['password'] = '';
  $user['first_name'] = '';
  $user['last_name'] = '';
  $user['mail'] = '';

  return template('ulysse/user/template/userForm.php', ['user' => $user]);
}

/**
 * @return string
 */
function userUpdateController() {

  if (empty($_GET['machine_name'])) {
    setHttpResponseCode(404);
    return "Error : Machine name not found.";
  }

  $content = getContentByMachineName($_GET['machine_name']);
  if (!$content) {
    setHttpResponseCode(404);
    return "Error : No content found with machine name " . sanitizeValue($_GET['machine_name']);
  }

  $content['action'] = 'update';
  return template('ulysse/content/template/contentForm.php', ['content' => $content]);
}

/**
 * Page to save the content to the database
 * Display the form again if validation errors are found.
 * @return string
 */
function userFormSaveController() {

  if (!$_POST)
  {
    setHttpResponseCode(403, 'No post datas received');
    return "No POST datas detected";
    exit;
  }

  $datas = $_POST;
  // automatically generate a unique machine_name for users.
  if ($datas['action'] == 'create') {
    $datas['machine_name'] = generateRandomId();
  }

  // validate datas posted by the form.
  $errors = userFormDataValidator($datas);

  // if there is no errors, create or update content.
  if ($errors['count'] == 0)
  {
    // if action is "create", we have to create a new content
    if (($datas['action'] == 'create'))
    {
      createUser($datas);
      writeLog(['detail' => "Save new content"]);
    }
    // else, we are updating an existing content.
    else
    {
      updateUser($datas['machine_name'], $datas);
      writeLog(['detail' => "Update existing content ." . sanitizeValue($datas['machine_name'])]);
    }
    // redirect to "redirection" param passed in the url
    redirection();
  }
  // if thre are errors, display again the form populated with the posted values.
  else
  {
    return template('ulysse/user/template/userForm.php', ['user' => $_POST, 'errors' => $errors['fields']]);
  }

}

/**
 * Confirm deletion page
 * @return string
 */
function userDeleteConfirmController() {
  if (empty($_GET['machine_name'])) {
    setHttpResponseCode(404);
    return "Machine name is missing.";
  }

  $content = getContentByMachineName($_GET['machine_name']);
  if (!$content) {
    setHttpResponseCode(404);
    return "Machine name not found : " . sanitizeValue($_GET['machine_name']);
  }

  return template('ulysse/content/template/contentDeleteConfirmForm.php', ['content' => $content]);
}

/**
 * Deletion page
 * @return string
 */
function userDeleteController() {

  if (empty($_POST['machine_name'])) {
    setHttpResponseCode(404);
    return "Machine name is missing.";
  }

  $content = getContentByMachineName($_POST['machine_name']);
  if (!$content) {
    setHttpResponseCode(404);
    return "Machine name not found : " . sanitizeValue($_POST['machine_name']);
  }

  deleteContent($_POST['machine_name']);
  redirection();
  return "Your content has been deleted";

}
