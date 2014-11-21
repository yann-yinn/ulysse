<?php

?>

<div class="content-editable-wrapper panel">
<?php if (userHasPermission('edit content')) : ?>
<a class="content-editable-link" href="<?php echo url('admin/content/form', "form_redirection=" . getCurrentPath() .  "&id=" . sanitizeString($id)) ?>">
  Edit</a>
<?php endif ?>

<h2><?php e($title) ?></h2>

<p><?php e($content) ?></p>
</div>