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
        <a class="<?php if (routeIsActive('homepage')) e('active') ?>" href="<?php e(buildUrl('homepage')) ?>">Homepage </a>
      </li>
      <li>
        <a class="<?php if (routeIsActive('helloWorld')) e('active') ?>" href="<?php e(buildUrl('helloWorld')) ?>"> Hello </a>
      </li>
    </ul>
  </nav>

  <?php print $content ?>

</div>

</body>

</html>