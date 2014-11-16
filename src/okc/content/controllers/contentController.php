<?php

function contentFormPage() {
  $content = NULL;
  if (!empty($_GET['id'])) {
    $content = getContentById($_GET['id']);
  }
  return template('contentForm.php', ['content' => $content], 'okc/content/templates');
}

function contentFormSavePage() {
  if ($_POST) {
    $datas = $_POST;
    require_once 'okc/content/api/contentApi.php';

    if (empty($datas['id'])) {
      saveNewContent($datas);
      writeLog(['detail' => "Save new content"]);
    }
    else {
      updateContentById($datas['id'], $datas);
      writeLog(['detail' => "Update existing content ." . sanitizeString($datas['id'])]);
    }
    return "content has been saved";
  }
  else {
    setHttpResponseCode(403, 'No post datas received');
    exit;
  }
}