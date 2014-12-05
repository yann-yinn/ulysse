<form method="POST" action="<?php echo href("ulysse.user.save", 'redirection=' . getRedirectionFromUrl()) ?>">

  <input type="hidden" name="action" value="<?php e($user['action']) ?>" >

  <input type="hidden" name="machine_name" value="<?php e($user['machine_name']) ?>" >

  <input type="hidden" name="role" value="<?php e($user['role']) ?>" />
  <?php if ($errors['role']) : ?>
    <small class="error"><?php echo implode("; ", $errors['role']) ?></small>
  <?php endif ?>

  <div class="row">
    <div class="large-12 columns">
      <label class="<?php if($errors['identifier']) echo 'error' ?>"> Identifier
        <input type="text" name="identifier" value="<?php echo $user['identifier'] ?>">
        <?php if ($errors['identifier']) : ?>
          <small class="error"><?php echo implode("; ", $errors['identifier']) ?></small>
        <?php endif ?>
      </label>
    </div>
  </div>

  <div class="row">
    <div class="large-12 columns">
      <label class="<?php if($errors['first_name']) echo 'error' ?>"> First Name
        <input type="text" name="first_name" value="<?php echo $user['first_name'] ?>">
        <?php if ($errors['first_name']) : ?>
          <small class="error"><?php echo implode("; ", $errors['first_name']) ?></small>
        <?php endif ?>
      </label>
    </div>
  </div>

  <div class="row">
    <div class="large-12 columns">
      <label class="<?php if($errors['mail']) echo 'error' ?>"> Mail
        <input type="text" name="mail" value="<?php echo $user['mail'] ?>">
        <?php if ($errors['mail']) : ?>
          <small class="error"><?php echo implode("; ", $errors['mail']) ?></small>
        <?php endif ?>
      </label>
    </div>
  </div>

  <div class="row">
    <div class="large-12 columns">
      <label class="<?php if($errors['password']) echo 'error' ?>"> Mot de passe
        <input type="text" name="password" value="<?php echo $user['password'] ?>">
        <?php if ($errors['password']) : ?>
          <small class="error"><?php echo implode("; ", $errors['password']) ?></small>
        <?php endif ?>
      </label>
    </div>
  </div>

  <div>
    <input class="button radius" value="Créer" type="submit"/>
  </div>

</form>

