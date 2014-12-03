<style>
  table {
    width: 100%;
  }
</style>
<div class="row">
  <div class="small-12 columns">


    <table>

      <caption><?php e($state_title) ?></caption>

      <thead>
      <th class="show-for-large-up">Type</th>
      <th>title</th>
      <th class="show-for-large-up">machine name</th>
      <th>Created</th>
      <th>Edit</th>
      <?php if ($state == CONTENT_STATE_TRASH) : ?>
        <th>Delete</th>
      <?php endif ?>
      </thead>

      <tbody>
      <?php foreach ($datas as $data): ?>
        <tr>

          <td class="show-for-large-up"><?php e($data['type']) ?></td>

          <td><?php e($data['title']) ?></td>

          <td class="show-for-large-up"><?php e($data['machine_name']) ?></td>

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