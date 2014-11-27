<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title> Page title </title>
  <link rel="stylesheet" href="css/styles.css?v=1.0">
  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <?php echo fireDomEvent('ulysse.framework.javascripts') ?>
  <link rel="stylesheet" href="<?php echo getBasePath() ?>assets/foundation-5.4.6/css/foundation.css" />
  <?php echo fireDomEvent('ulysse.framework.stylesheets') ?>
</head>
<style>
  .content-editable-link {
    font-size : 14px;
    float:right;
  }
</style>

<body>

<div class="row">
  <h1><?php echo template('ulysse/content/templates/contentViewSetting.php', getContentByMachineName('site_name')) ?></h1>
  <p><?php echo template('ulysse/content/templates/contentViewSetting.php', getContentByMachineName('site_slogan', CONTENT_STATE_ONLINE)) ?></p>
</div>

<div class="row">

  <nav>
    <ul>
      <li> <?php echo l('homepage', ''); ?> </li>
      <li> <?php echo l('Page not Found', 'azertyuiop789456123') ?></li>
    </ul>
  </nav>
  <?php if (isset($zone_top)) : ?>
    <?php echo $zone_top(); ?>
  <?php endif ?>

  <?php print $content ?>

</div>

</body>

</html>