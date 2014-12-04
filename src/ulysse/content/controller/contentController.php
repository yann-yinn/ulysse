<?php

require_once 'ulysse/content/dataValidator/contentDataValidator.php';

/**
 * Display all sites content with edit / add / delete link, in all states
 * (draft, online, trash )
 * @return string
 */
function contentListController() {
  return template('ulysse/content/template/contentDashboard.php');
}

/**
 * Display a form to add / edit a content.
 * @return string
 */
function contentCreateController() {

  $content['action'] = 'create';
  $content['title'] = '';
  $content['content'] = '';
  $content['state'] = CONTENT_STATE_DRAFT;
  $content['machine_name'] = '';
  $content['type'] = 'content';

  return template('ulysse/content/template/contentForm.php', ['content' => $content]);
}

/**
 * Display a form to add / edit a content.
 * @return string
 */
function contentUpdateController() {

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
function contentFormSaveController() {
  if (!$_POST)
  {
    setHttpResponseCode(403, 'No post datas received');
    return "No POST datas detected";
    exit;
  }

  $datas = $_POST;
  // validate datas posted by the form.
  $errors = contentFormDataValidator($datas);

  // if there is no errors, create or update content.
  if ($errors['count'] == 0)
  {
    // if action is "create", we have to create a new content
    if (($datas['action'] == 'create'))
    {
      createContent($datas['machine_name'], $datas);
      writeLog(['detail' => "Save new content"]);
    }
    // else, we are updating an existing content.
    else
    {
      updateContent($datas['machine_name'], $datas);
      writeLog(['detail' => "Update existing content ." . sanitizeValue($datas['machine_name'])]);
    }
    // redirect to "redirection" param passed in the url
    redirection();
  }
  // if thre are errors, display again the form populated with the posted values.
  else
  {
    return template('ulysse/content/template/contentForm.php', ['content' => $_POST, 'errors' => $errors['fields']], 'ulysse/content/template');
  }

}

/**
 * Confirm deletion page
 * @return string
 */
function contentDeleteConfirmController() {
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
function contentDeleteController() {

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
