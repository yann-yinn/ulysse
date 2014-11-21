<?php

?>

<div id="actions">
  <a href="<?php e(url('admin/content/form', 'form_redirection=admin/content')) ?>">+ Add content</a>
</div>

<div class="row">
  <div class="small-12 columns">

    <table>
      <thead>
      <th>Type</th>
      <th>title</th>
      <th>machine name</th>
      <th>Created</th>
      <th>State</th>
      <th>Edit</th>
      </thead>

      <tbody>
      <?php foreach ($datas as $data): ?>
        <tr>

          <td><?php e($data['type']) ?></td>

          <td><?php e($data['title']) ?></td>

          <td><?php e($data['machine_name']) ?></td>

          <td><?php e($data['created']) ?></td>

          <td><?php e($data['state']) ?></td>

          <td><a href="<?php e(url("admin/content/form", 'id=' . $data['id'] . '&form_redirection=admin/content')) ?>">Edit</a></td>

        </tr>
      <?php endforeach ?>
      </tbody>
    </table>

  </div>
</div>