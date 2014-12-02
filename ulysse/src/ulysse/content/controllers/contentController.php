<?php

require_once 'ulysse/content/datasValidators/contentDatasValidators.php';

/**
 * Display all sites content with edit / add / delete link.
 * @return string
 */
function contentListPage() {
  $datas = getContentList(CONTENT_STATE_ONLINE);
  $out = '';
  $out.= template('ulysse/content/templates/contentList.php', ['state' => 'Online', 'datas' => $datas]);
  $datas = getContentList(CONTENT_STATE_DRAFT);
  $out.= template('ulysse/content/templates/contentList.php', ['state' => 'Draft', 'datas' => $datas]);
  $datas = getContentList(CONTENT_STATE_TRASH);
  $out.= template('ulysse/content/templates/contentList.php', ['state' => 'Trash', 'datas' => $datas]);
  return $out;
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
    // if machine name does not exist yet, this is a new content.
    if (!getContentByMachineName($datas['machine_name']))
    {
      saveNewContent($datas);
      writeLog(['detail' => "Save new content"]);
    }
    // else, we are updating an existing content.
    else
    {
      updateContentByMachineName($datas['machine_name'], $datas);
      writeLog(['detail' => "Update existing content ." . sanitizeValue($datas['id'])]);
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

