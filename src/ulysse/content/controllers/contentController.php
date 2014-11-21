<?php

require_once 'ulysse/content/datasValidators/contentDatasValidators.php';
require_once 'ulysse/content/api/contentApi.php';

/**
 * Display all sites content with edit / add / delete link.
 * @return string
 */
function contentListPage() {
  $datas = getContentList();
  return template('ulysse/content/templates/contentList.php', ['datas' => $datas]);
}

/**
 * Display a form to add / edit a content.
 * @return string
 */
function contentFormPage() {
  $content = NULL;
  // if there is an id in the url, this a existing content, pass
  // populated content array to the form.
  if (!empty($_GET['id'])) {
    $content = getContentById($_GET['id']);
  }
  return template('ulysse/content/templates/contentForm.php', ['content' => $content]);
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
  if (!$errors) {
    // if id is not set, this is a new content. Id is an hidden input field.
    if (empty($datas['id'])) {
      saveNewContent($datas);
      writeLog(['detail' => "Save new content"]);
    }
    // else, we are updating an existing content.
    else {
      updateContentById($datas['id'], $datas);
      writeLog(['detail' => "Update existing content ." . sanitizeString($datas['id'])]);
    }
    // redirect to "redirection" param passed in the url
    redirection();
  }
  // if thre are errors, display again the form populated with the posted values.
  else {
    return template('ulysse/content/templates/contentForm.php', ['content' => $_POST, 'errors' => $errors], 'ulysse/content/templates');
  }

}

