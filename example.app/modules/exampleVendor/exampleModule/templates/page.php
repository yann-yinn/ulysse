<!doctype html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="utf-8">
  <title> Demo page </title>
</head>

<body>

<div class="row">

  <h1>Ulysse default homepage </h1>

  <nav>
    <ul>
      <li>
        <a class="<?php if (pathIsActive('')) e('active') ?>" href="<?php e(getRouteUrl('')) ?>">Homepage </a>
      </li>
      <li>
        <a class="<?php if (pathIsActive('hello')) e('active') ?>" href="<?php e(getRouteUrl('hello')) ?>"> Hello </a>
      </li>
    </ul>
  </nav>

  <?php print $content ?>

</div>

</body>

</html>