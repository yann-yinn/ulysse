<div id="actions">
  <a href="<?php e(url('admin/content/form', 'form_redirection=admin')) ?>">+ Add content</a>
</div>

<?php
echo template('ulysse/content/templates/contentList.php', [
    'state' => CONTENT_STATE_ONLINE,
    'state_title' => 'Online',
    'datas' => getContentList(CONTENT_STATE_ONLINE)
  ]);
?>

<?php
echo template('ulysse/content/templates/contentList.php', [
    'state' => CONTENT_STATE_DRAFT,
    'state_title' => 'Drafts',
    'datas' => getContentList(CONTENT_STATE_DRAFT)
  ]);
?>

<?php
echo template('ulysse/content/templates/contentList.php', [
    'state' => CONTENT_STATE_TRASH,
    'state_title' => 'Trash',
    'datas' => getContentList(CONTENT_STATE_TRASH)
  ]);
?>
