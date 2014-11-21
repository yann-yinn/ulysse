<form method="POST" action="<?php echo url("admin/content/form/save", 'form_redirection=' . getFormRedirectionFromUrl()) ?>">

  <!-- ID FIELD -->
  <?php if(!empty($content['id'])) : ?>
    <input type="hidden" name="id" value="<?php echo $content['id'] ?>" />
  <?php endif ?>

  <input type="hidden" name="type" value="<?php print !empty($_GET['type']) ? $_GET['type'] : 'content' ?>" />
  <?php if (!empty($errors['type'])) : ?>
    <small class="error"><?php echo implode("; ", $errors['type']) ?></small>
  <?php endif ?>

  <div class="row">
    <div class="large-12 columns">
      <label>Title
        <input type="text" name="title" value="<?php if(!empty($content['title'])) echo $content['title'] ?>">
      </label>
    </div>
  </div>

  <!-- MACHINE NAME FIELD -->
  <div class="row">
    <div class="small-12 columns">
      <label>Machine name :</label>
      <?php if (!empty($errors['machine_name'])) : ?>
        <small class="error"><?php echo implode("; ", $errors['machine_name']) ?></small>
      <?php endif ?>

      <input type="<?php empty($content['machine_name']) ? print "text" : print "hidden" ?>" value="<?php if(!empty($content['machine_name'])) echo $content['machine_name'] ?>" name="machine_name">
      <?php if (!empty($content['machine_name'])) : ?>
        <?php e($content['machine_name']) ?>
      <?php endif ?>
    </div>
  </div>

  <!-- CONTENT FIELD -->
  <div>
    <label>Content</label>
    <?php if (!empty($errors['content'])) : ?>
      <div class="error"><?php echo implode("; ", $errors['content']) ?></div>
    <?php endif ?>
    <textarea rows="10" id="content" name="content"><?php if(!empty($content['content'])) echo $content['content'] ?></textarea>
    <script>
      // Replace the <textarea id="content"> with a CKEditor
      // instance, using default configuration.
      CKEDITOR.replace('content');
    </script>
  </div>

  <!-- STATE FIELD -->
  <div>
    <label>Content state
      <select name="state">
        <option <?php if(!empty($content['state']) && $content['state'] == CONTENT_STATE_DRAFT) echo 'selected="selected"' ?> value="<?php print CONTENT_STATE_DRAFT ?>"> Draft </option>
        <option <?php if(!empty($content['state']) && $content['state'] == CONTENT_STATE_ONLINE) echo 'selected="selected"' ?> value="<?php print CONTENT_STATE_ONLINE ?>"> Online </option>
        <option <?php if(!empty($content['state']) && $content['state'] == CONTENT_STATE_TRASH) echo 'selected="selected"' ?> value="<?php print CONTENT_STATE_TRASH ?>"> Trash </option>
      </select>
    </label>
    <?php if (!empty($errors['state'])) : ?>
      <small class="error"><?php echo implode("; ", $errors['state']) ?></small>
    <?php endif ?>
  </div>

  <div>
    <input class="button radius" value="<?php echo getTranslation('contentForm.save') ?>" type="submit"/>
  </div>

</form>

