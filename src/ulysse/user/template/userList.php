<dl class="sub-nav">
  <dd class="<?php if(getCurrentRouteId() == 'ulysse.content.list.all') echo 'active' ?>">
    <a href="<?php echo href('ulysse.content.list.all') ?>">All</a>
  </dd>
  <dd class="<?php if(getCurrentRouteId() == 'ulysse.content.list.online' || getCurrentRouteId() == 'ulysse.content.list') echo 'active' ?>">
    <a href="<?php echo href('ulysse.content.list.online') ?>">Online</a>
  </dd>
  <dd class="<?php if(getCurrentRouteId() == 'ulysse.content.list.draft') echo 'active' ?>">
    <a href="<?php echo href('ulysse.content.list.draft') ?>">Draft</a>
  </dd>
  <dd class="<?php if(getCurrentRouteId() == 'ulysse.content.list.trash') echo 'active' ?>">
    <a href="<?php echo href('ulysse.content.list.trash') ?>">Trash</a>
  </dd>
</dl>

<?php if (!$datas) : ?>
  There is currently no content in <?php echo $state_title ?> state.
<?php else : ?>

<table class="table table-stripped">


  <thead>
  <th class="show-for-large-up">Type</th>
  <?php if (!$state) : ?>
    <th>State</th>
  <?php endif ?>
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

      <?php if (!$state) : ?>
        <td><?php e($data['state']) ?></td>
      <?php endif ?>

      <td><?php e($data['title']) ?></td>

      <td class="show-for-large-up"><?php e($data['machine_name']) ?></td>

      <td><?php e($data['created'], 'dateFull') ?></td>

      <td><a class="btn btn-block btn-lg btn-primary" href="<?php e(href('ulysse.content.update', 'machine_name=' . $data['machine_name'] . '&' . buildAutoRedirectionQueryString())) ?>">Edit</a></td>
      <?php if ($state == CONTENT_STATE_TRASH) : ?>
        <td><a class="btn btn-block btn-lg btn-warning" href="<?php e(href('ulysse.content.confirmDeletion', 'machine_name=' . $data['machine_name'] . '&' . buildAutoRedirectionQueryString())) ?>">Delete</a></td>
      <?php endif ?>

    </tr>
  <?php endforeach ?>
  </tbody>
</table>

<?php endif ?>