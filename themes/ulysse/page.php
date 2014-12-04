<!doctype html>
<html lang="en">
<head>
  <?php echo fireDomEvent('template.head') ?>
  <meta charset="utf-8">
  <title> Page title </title>
  <link rel="stylesheet" href="css/styles.css?v=1.0">
  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <?php echo fireDomEvent('template.addingJavascripts') ?>
  <link rel="stylesheet" href="<?php echo getBasePath() ?>assets/foundation-5.4.6/css/foundation.css" />
  <?php echo fireDomEvent('template.addingCss') ?>
</head>

<body>

<div class="row">

  <h1>Ulysse default homepage</h1>

  <nav>
    <ul>
      <li> <?php echo l('Homepage', ''); ?> </li>
    </ul>
  </nav>

  <?php print $content ?>

</div>

</body>

</html>