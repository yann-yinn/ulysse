<?php

?>

<div id="actions">
  <a href="<?php e(url('admin/content/form', ['redirection' => 'admin/content'])) ?>">+ Add content</a>
</div>

<table>
  <th>Type</th>
  <th>title</th>
  <th>machine name</th>
  <th>Created</th>
  <th>State</th>
  <th>Edit</th>
  <?php foreach ($datas as $data): ?>
    <tr>

      <td><?php e($data['type']) ?></td>

      <td><?php e($data['title']) ?></td>

      <td><?php e($data['machine_name']) ?></td>

      <td><?php e($data['created']) ?></td>

      <td><?php e($data['state']) ?></td>

      <td><a href="<?php e(url("admin/content/form?id={$data['id']}&redirection=" . urlencode("admin/content"))) ?>">Edit</a></td>

    </tr>
  <?php endforeach ?>
</table>