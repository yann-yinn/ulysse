<?php

?>

<div id="actions">
  <a href="<?php e(url('admin/content/form', 'form_redirection=admin')) ?>">+ Add content</a>
</div>

<div class="row">
  <div class="small-12 columns">


    <table>

      <caption><?php e($state_title) ?></caption>

      <thead>
      <th>Type</th>
      <th>title</th>
      <th>machine name</th>
      <th>Created</th>
      <th>Edit</th>
      <?php if ($state == CONTENT_STATE_TRASH) : ?>
        <th>Delete</th>
      <?php endif ?>
      </thead>

      <tbody>
      <?php foreach ($datas as $data): ?>
        <tr>

          <td><?php e($data['type']) ?></td>

          <td><?php e($data['title']) ?></td>

          <td><?php e($data['machine_name']) ?></td>

          <td><?php e($data['created'], 'dateFull') ?></td>


          <td><a href="<?php e(url("admin/content/form", 'machine_name=' . $data['machine_name'] . '&form_redirection=admin')) ?>">Edit</a></td>
          <?php if ($state == CONTENT_STATE_TRASH) : ?>
            <td><a href="<?php e(url("admin/content/delete/confirm", 'machine_name=' . $data['machine_name'] . '&form_redirection=admin')) ?>">Delete</a></td>
          <?php endif ?>

        </tr>
      <?php endforeach ?>
      </tbody>
    </table>

  </div>
</div>