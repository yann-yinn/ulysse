<form method="POST" action="<?php echo url("admin/content/form/save", ['redirection' => getHttpRedirectionFromUrl()]) ?>">

  <!-- ID FIELD -->
  <?php if(!empty($content['id'])) : ?>
    <input type="hidden" name="id" value="<?php echo $content['id'] ?>" />
  <?php endif ?>

  <input type="hidden" name="type" value="content" />

  <div>
    <label>Title</label>
    <input type="textfield" name="title" value="<?php if(!empty($content['title'])) echo $content['title'] ?>">
  </div>

  <!-- MACHINE NAME FIELD -->
  <div>
    <label>Machine name :</label>
    <?php if (!empty($errors['machine_name'])) : ?>
      <div class="error"><?php echo implode("; ", $errors['machine_name']) ?></div>
    <?php endif ?>

    <input type="<?php empty($content['machine_name']) ? print "textfield" : print "hidden" ?>" value="<?php if(!empty($content['machine_name'])) echo $content['machine_name'] ?>" name="machine_name">
    <?php if (!empty($content['machine_name'])) : ?>
      <?php e($content['machine_name']) ?>
    <?php endif ?>
  </div>

  <!-- CONTENT FIELD -->
  <div>
    <label>Content</label>
    <?php if (!empty($errors['content'])) : ?>
      <div class="error"><?php echo implode("; ", $errors['content']) ?></div>
    <?php endif ?>
    <textarea id="content" name="content"><?php if(!empty($content['content'])) echo $content['content'] ?></textarea>
    <script>
      // Replace the <textarea id="content"> with a CKEditor
      // instance, using default configuration.
      CKEDITOR.replace('content');
    </script>
  </div>

  <!-- STATE FIELD -->
  <div>
    <label>Content state</label>
    <?php if (!empty($errors['state'])) : ?>
      <div class="error"><?php echo implode("; ", $errors['state']) ?></div>
    <?php endif ?>
    <select name="state">
      <option <?php if(!empty($content['state']) && $content['state'] == CONTENT_STATE_DRAFT) echo 'selected="selected"' ?> value="<?php print CONTENT_STATE_DRAFT ?>"> Draft </option>
      <option <?php if(!empty($content['state']) && $content['state'] == CONTENT_STATE_ONLINE) echo 'selected="selected"' ?> value="<?php print CONTENT_STATE_ONLINE ?>"> Online </option>
      <option <?php if(!empty($content['state']) && $content['state'] == CONTENT_STATE_TRASH) echo 'selected="selected"' ?> value="<?php print CONTENT_STATE_TRASH ?>"> Trash </option>
    </select>
  </div>

  <div>
    <input value="<?php echo getTranslation('contentForm.save') ?>" type="submit"/>
  </div>

</form>

