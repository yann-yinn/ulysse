<?php

require_once 'ulysse/content/datasValidators/contentDatasValidators.php';

/**
 * Display all sites content with edit / add / delete link, in all states
 * (draft, online, trash )
 * @return string
 */
function contentListPage() {
  return template('ulysse/content/templates/contentDashboard.php');
}

/**
 * Display a form to add / edit a content.
 * @return string
 */
function contentFormPage() {
  $content = NULL;
  // if there is an id in the url, this a existing content, pass
  // populated content array to the form.
  if (!empty($_GET['machine_name'])) {
    $content = getContentByMachineName($_GET['machine_name']);
  }
  return template('ulysse/content/templates/contentForm.php', ['content' => $content]);
}

/**
 * Confirm deletion page
 * @return string
 */
function contentDeleteConfirmPage() {
  if (empty($_GET['machine_name'])) {
    setHttpResponseCode(404);
    return "Machine name is missing.";
  }

  $content = getContentByMachineName($_GET['machine_name']);
  if (!$content) {
    setHttpResponseCode(404);
    return "Machine name not found : " . sanitizeValue($_GET['machine_name']);
  }

  return template('ulysse/content/templates/contentDeleteConfirmForm.php', ['content' => $content]);
}

/**
 * Deletion page
 * @return string
 */
function contentDeletePage() {

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

/**
 * Page to save the content to the database
 * Display the form again if validation errors are found.
 * @return string
 */
function contentFormSavePage() {
  if (!$_POST)
  {
    setHttpResponseCode(403, 'No post datas received');
    return "No POST datas detected";
    exit;
  }

  $datas = $_POST;
  // validate datas posted by the form.
  $errors = validateContentForm($datas);
  if (!$errors)
  {
    // if machine name does not exist yet in database, this is a new content.
    if (!getContentByMachineName($datas['machine_name']))
    {
      saveNewContent($datas['machine_name'], $datas);
      writeLog(['detail' => "Save new content"]);
    }
    // else, we are updating an existing content.
    else
    {
      updateContentByMachineName($datas['machine_name'], $datas);
      writeLog(['detail' => "Update existing content ." . sanitizeValue($datas['machine_name'])]);
    }
    // redirect to "redirection" param passed in the url
    redirection();
  }
  // if thre are errors, display again the form populated with the posted values.
  else
  {
    return template('ulysse/content/templates/contentForm.php', ['content' => $_POST, 'errors' => $errors], 'ulysse/content/templates');
  }

}

