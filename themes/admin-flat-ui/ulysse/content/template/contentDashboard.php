<div class="row">
  <div class="col-xs-12">


    <img src="<?php echo getBasePath() ?>libraries/designmodo/flat-ui/img/icons/svg/pencils.svg" />


    <div class="panel panel-success">
      <div class="panel-heading">ONLINE</div>
      <div class="panel-body">
        <?php
        echo template('ulysse/content/template/contentList.php', [
            'state' => CONTENT_STATE_ONLINE,
            'state_title' => 'online',
            'datas' => getContentList(CONTENT_STATE_ONLINE)
          ]);
        ?>
      </div>
    </div>

    <div class="panel panel-info">
      <div class="panel-heading">DRAFTS</div>
      <div class="panel-body">
        <?php
        echo template('ulysse/content/template/contentList.php', [
            'state' => CONTENT_STATE_DRAFT,
            'state_title' => 'draft',
            'datas' => getContentList(CONTENT_STATE_DRAFT)
          ]);
        ?>
      </div>
    </div>

    <div class="panel panel-danger">
      <div class="panel-heading">TRASH</div>
      <div class="panel-body">

        <?php
        echo template('ulysse/content/template/contentList.php', [
            'state' => CONTENT_STATE_TRASH,
            'state_title' => 'trash',
            'datas' => getContentList(CONTENT_STATE_TRASH)
          ]);
        ?>
      </div>
    </div>


  </div>
</div>
