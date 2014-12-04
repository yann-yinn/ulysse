<?php

?>

<?php if($machine_name) : ?>
<div class="content-editable-wrapper">
<?php if (userHasPermission('edit content')) : ?>
<a class="content-editable-link" href="<?php echo url('admin/content/form', "redirection=" . getCurrentPath() .  "&machine_name=" . sanitizeValue($machine_name)) ?>">
  Edit</a>
<?php endif ?>
<?php e($content) ?>
</div>
<?php endif ?>