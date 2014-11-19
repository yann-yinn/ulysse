<?php

?>

<div class="wrapper-content-editable panel">
<?php if (userHasPermission('edit content')) : ?>
<a class="content-editable" href="<?php echo url('admin/content/form', "form_redirection=" . getCurrentPath() .  "&id=" . sanitizeString($id)) ?>">
  Edit this content</a>
<?php endif ?>

<h2><?php e($title) ?></h2>

<p><?php e($content) ?></p>
</div>