<style>

</style>
<div class="row">

  <h1><?php e($state_title) ?></h1>


      <?php foreach ($datas as $data): ?>
        <div class="contentThumb panel" style="float:left">
          <h2><?php e($data['title']) ?></h2>
          <h3><?php e($data['type']) ?></h3>
          <p><?php e($data['created'], 'dateFull') ?></p>
          <a href="<?php e(url("admin/content/form", 'machine_name=' . $data['machine_name'] . '&form_redirection=admin')) ?>">Edit</a>
          <?php if ($state == CONTENT_STATE_TRASH) : ?>
           <a href="<?php e(url("admin/content/delete/confirm", 'machine_name=' . $data['machine_name'] . '&form_redirection=admin')) ?>">Delete</a>
          <?php endif ?>
        </div>


      <?php endforeach ?>

</div>