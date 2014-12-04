<form method="POST" action="<?php echo href("ulysse.content.save", 'form_redirection=' . getFormRedirectionFromUrl()) ?>">

  <input type="hidden" name="action" value="<?php e($content['action']) ?>" >

  <input type="hidden" name="type" value="<?php e($content['type']) ?>" />
  <?php if ($errors['type']) : ?>
    <small class="error"><?php echo implode("; ", $errors['type']) ?></small>
  <?php endif ?>

  <div class="row">
    <div class="large-12 columns">
      <label><?php e('ulysse.content.form.title.label', 't') ?>
        <input type="text" name="title" value="<?php echo $content['title'] ?>">
      </label>
    </div>
  </div>

  <!-- MACHINE NAME FIELD -->
  <div class="row">
    <div class="small-12 columns">

      <label class="<?php if ($errors['machine_name']) echo 'error' ?>">
        Machine name :

        <!-- Display machine name as a textfield on creation, but hide this field on update -->
        <?php if ($content['action'] == 'create') : ?>
          <input type="text" value="<?php e($content['machine_name']) ?>" name="machine_name">

          <?php if ($errors['machine_name']) : ?>
            <small class="error"><?php echo implode("; ", $errors['machine_name']) ?></small>
          <?php endif ?>

        <?php else : ?>
          <input type="hidden" value="<?php e($content['machine_name']) ?>" name="machine_name">
          <div><?php echo $content['machine_name'] ?></div>

        <?php endif ?>

      </label>
    </div>
  </div>

  <!-- CONTENT FIELD -->
  <div>
    <label><?php e('ulysse.content.form.body.label', 't') ?></label>
    <textarea rows="10" id="content" name="content"><?php echo $content['content'] ?></textarea>
    <script>
      // Replace the <textarea id="content"> with a CKEditor
      // instance, using default configuration.
      CKEDITOR.replace('content');
    </script>
  </div>

  <!-- STATE FIELD -->
  <div>
    <label><?php echo getTranslation('ulysse.content.form.status.label') ?>
      <select name="state">
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
    <input class="button radius" value="<?php e('contentForm.save', 't') ?>" type="submit"/>
  </div>

</form>

