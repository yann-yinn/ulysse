<?php

?>

<div class="content-editable-wrapper">
<?php if (userHasPermission('edit content')) : ?>
<a class="content-editable-link" href="<?php echo url('admin/content/form', "form_redirection=" . getCurrentPath() .  "&id=" . sanitizeValue($id)) ?>">
  Edit</a>
<?php endif ?>
<?php e($content) ?>
</div>