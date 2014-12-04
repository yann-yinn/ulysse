
<?php
echo template('ulysse/content/template/contentList.php', [
    'state' => CONTENT_STATE_ONLINE,
    'state_title' => 'ONLINE CONTENTS',
    'datas' => getContentList(CONTENT_STATE_ONLINE)
  ]);
?>

<?php
echo template('ulysse/content/template/contentList.php', [
    'state' => CONTENT_STATE_DRAFT,
    'state_title' => 'DRAFT CONTENTS',
    'datas' => getContentList(CONTENT_STATE_DRAFT)
  ]);
?>

<?php
echo template('ulysse/content/template/contentList.php', [
    'state' => CONTENT_STATE_TRASH,
    'state_title' => 'TRASH',
    'datas' => getContentList(CONTENT_STATE_TRASH)
  ]);
?>
