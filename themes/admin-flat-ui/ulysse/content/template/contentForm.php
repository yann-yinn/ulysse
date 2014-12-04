<form method="POST" action="<?php echo href("ulysse.content.save", 'form_redirection=' . getFormRedirectionFromUrl()) ?>">

  <input class="form-control" type="hidden" name="action" value="<?php e($content['action']) ?>" >

  <input type="hidden" name="type" value="<?php e($content['type']) ?>" />
  <?php if ($errors['type']) : ?>
    <small class="error"><?php echo implode("; ", $errors['type']) ?></small>
  <?php endif ?>

  <div class="input-group">
    <!--<span class="input-group-addon">Title</span>-->
    <input class="form-control flat" placeholder="Enter title" type="text" name="title" value="<?php echo $content['title'] ?>">
  </div>


  <!-- MACHINE NAME FIELD -->


      <!-- Display machine name as a textfield on creation, but hide this field on update -->
      <?php if ($content['action'] == 'create') : ?>
        <div class="input-group">
          <!--<span class="input-group-addon">Machine name</span>-->
          <input class="form-control flat" placeholder="Enter machine name" type="text" value="<?php e($content['machine_name']) ?>" name="machine_name">
        </div>
        <?php if ($errors['machine_name']) : ?>
          <small class="error"><?php echo implode("; ", $errors['machine_name']) ?></small>
        <?php endif ?>

      <?php else : ?>
        <input type="hidden" value="<?php e($content['machine_name']) ?>" name="machine_name">
        <div><?php echo $content['machine_name'] ?></div>

      <?php endif ?>


  <!-- CONTENT FIELD -->
  <div>
    <label><?php e('ulysse.content.form.body.label', 't') ?></label>
    <textarea class="form-control flat" rows="10" id="content" name="content"><?php echo $content['content'] ?></textarea>
    <script>
      // Replace the <textarea id="content"> with a CKEditor
      // instance, using default configuration.
      CKEDITOR.replace('content');
    </script>
  </div>

  <!-- STATE FIELD -->
  <div>

    <label><?php echo getTranslation('ulysse.content.form.status.label') ?>
      <select class="form-control select select-primary" name="state">
        <option <?php if($content['state'] == CONTENT_STATE_DRAFT) echo 'selected="selected"' ?> value="<?php echo CONTENT_STATE_DRAFT ?>"> Draft </option>
        <option <?php if($content['state'] == CONTENT_STATE_ONLINE) echo 'selected="selected"' ?> value="<?php echo CONTENT_STATE_ONLINE ?>"> Online </option>
        <option <?php if($content['state'] == CONTENT_STATE_TRASH) echo 'selected="selected"' ?> value="<?php echo CONTENT_STATE_TRASH ?>"> Trash </option>
      </select>
    </label>
    <?php if ($errors['state']) : ?>
      <small class="error"><?php echo implode("; ", $errors['state']) ?></small>
    <?php endif ?>
  </div>

  <div>
    <input class="btn btn-primary" value="<?php e('contentForm.save', 't') ?>" type="submit"/>
  </div>

</form>

