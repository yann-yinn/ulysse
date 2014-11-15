<?php

$pages = [];

require "okc/atomContent/atomContent.php";

$pages['atom_content_form'] = [
  'path' => 'admin/content/form',
  'template' => 'page.php',
  'content' => function() {
      return template('atomContentForm.php', [], 'okc/atomContent/templates');
  }
];

$pages['atom_content_form_save'] = [
  'path' => 'admin/content/form/save',
  'template' => 'page.php',
  'content' => function () {
      return atomContentFormSavePage();
    }
];

return $pages;