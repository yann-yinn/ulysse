<?php

?>

<div id="actions">
  <a href="<?php e(url('admin/content/form', 'form_redirection=admin')) ?>">+ Add content</a>
</div>

<div class="row">
  <div class="small-12 columns">


    <table>

      <caption><?php e($state) ?></caption>

      <thead>
      <th>Type</th>
      <th>title</th>
      <th>machine name</th>
      <th>Created</th>
      <th>Edit</th>
      </thead>

      <tbody>
      <?php foreach ($datas as $data): ?>
        <tr>

          <td><?php e($data['type']) ?></td>

          <td><?php e($data['title']) ?></td>

          <td><?php e($data['machine_name']) ?></td>

          <td><?php e($data['created']) ?></td>


          <td><a href="<?php e(url("admin/content/form", 'machine_name=' . $data['machine_name'] . '&form_redirection=admin')) ?>">Edit</a></td>


        </tr>
      <?php endforeach ?>
      </tbody>
    </table>

  </div>
</div>