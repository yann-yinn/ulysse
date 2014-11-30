<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title> Page title </title>
  <link rel="stylesheet" href="css/styles.css?v=1.0">
  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <link rel="stylesheet" href="<?php echo getBasePath() ?>assets/foundation-5.4.6/css/foundation.css" />
</head>

<body>

<div class="row">
  <h1>Ulysse installation</h1>
  <div class="panel">
   <ul>
     <li>Copy <?php echo USER_DIST_CONFIG_DIRECTORY_PATH ?> directory to <?php echo USER_CONFIG_DIRECTORY_PATH ?></li>
     <li>Make sure <?php echo FRAMEWORK_WRITABLE_DIRECTORY_PATH ?> directory is writable by apache</li>
     <li>Refresh this page !</li>
   </ul>
  </div>

</div>

</body>

</html>