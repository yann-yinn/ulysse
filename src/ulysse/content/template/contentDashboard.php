
<dl class="tabs" data-tab>
  <dd class="active"><a href="#panel1">Online</a></dd>
  <dd><a href="#panel2">Draft</a></dd>
  <dd><a href="#panel3">Trash</a></dd>
</dl>

<div class="tabs-content">
  <div class="content active" id="panel1">

    <?php
    echo template('ulysse/content/template/contentList.php', [
        'state' => CONTENT_STATE_ONLINE,
        'state_title' => 'ONLINE CONTENTS',
        'datas' => getContentList(CONTENT_STATE_ONLINE)
      ]);
    ?>

  </div>
  <div class="content" id="panel2">
    <?php
    echo template('ulysse/content/template/contentList.php', [
        'state' => CONTENT_STATE_DRAFT,
        'state_title' => 'DRAFT CONTENTS',
        'datas' => getContentList(CONTENT_STATE_DRAFT)
      ]);
    ?>
  </div>
  <div class="content" id="panel3">
    <?php
    echo template('ulysse/content/template/contentList.php', [
        'state' => CONTENT_STATE_TRASH,
        'state_title' => 'TRASH',
        'datas' => getContentList(CONTENT_STATE_TRASH)
      ]);
    ?>
  </div>
</div>
