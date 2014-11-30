<?php

?>

<?php if($machine_name) : ?>
<div class="content-editable-wrapper panel">
<?php if (userHasPermission('edit content')) : ?>
<a class="content-editable-link" href="<?php echo url('admin/content/form', "form_redirection=" . getCurrentPath() .  "&machine_name=" . sanitizeValue($machine_name)) ?>">
  Edit</a>
<?php endif ?>

<h2><?php e($title) ?></h2>

<p><?php e($content) ?></p>
</div>
<?php endif ?>