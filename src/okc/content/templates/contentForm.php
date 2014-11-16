<form method="POST" action="<?php echo getUrlFromPath('admin/content/form/save') ?>">

  <?php if(!empty($content['id'])) : ?>
    <input type="hidden" name="id" value="<?php echo $content['id'] ?>" />
  <?php endif ?>

  <div>
    <input type="textfield" name="title" value="<?php if(!empty($content['title'])) echo $content['title'] ?>">
  </div>
  <div>
    <input type="textfield" value="<?php if(!empty($content['machine_name'])) echo $content['machine_name'] ?>" name="machine_name">
  </div>
  <div>
    <textarea id="content" name="content"><?php if(!empty($content['content'])) echo $content['content'] ?></textarea>
    <script>
      // Replace the <textarea id="content"> with a CKEditor
      // instance, using default configuration.
      CKEDITOR.replace('content');
    </script>
  </div>

  <div>
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

