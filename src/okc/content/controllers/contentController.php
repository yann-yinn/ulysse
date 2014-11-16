<?php

require_once 'okc/content/datasValidators/contentDatasValidators.php';
require_once 'okc/content/api/contentApi.php';

function contentListPage() {
  $datas = getContentList();
  return template('contentList.php', ['datas' => $datas], 'okc/content/templates');
}

function contentFormPage() {
  $content = NULL;
  if (!empty($_GET['id'])) {
    $content = getContentById($_GET['id']);
  }
  return template('contentForm.php', ['content' => $content], 'okc/content/templates');
}

function contentFormSavePage() {
  if (!$_POST)
  {
    addHttpResponseHeader(403, 'No post datas received');
    exit;
  }

  $datas = $_POST;
  $errors = validateContentForm($datas);
  if (!$errors) {
    if (empty($datas['id'])) {
      saveNewContent($datas);
      writeLog(['detail' => "Save new content"]);
    }
    else {
      updateContentById($datas['id'], $datas);
      writeLog(['detail' => "Update existing content ." . sanitizeString($datas['id'])]);
    }
    redirection();
  }
  else {
    return template('contentForm.php', ['content' => $_POST, 'errors' => $errors], 'okc/content/templates');
  }

}

