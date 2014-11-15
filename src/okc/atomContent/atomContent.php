<?php

function atomContentFormSavePage() {
  $datas = $_POST;
  atomContentSave($datas);
  return "content has been saved";
}

function atomContentSave($datas) {
  $db = getContextVariable('db');
  $insert = $db->prepare('INSERT INTO atom_content (content, created, changed) VALUES (:content, :created, :changed)');

  $content = $datas['content'];
  $created = time();
  $changed = time();
  $insert->bindParam(':content', $content, PDO::PARAM_STR);
  $insert->bindParam(':created', $created, PDO::PARAM_INT);
  $insert->bindParam(':changed', $changed, PDO::PARAM_INT);
  return $insert->execute();
}