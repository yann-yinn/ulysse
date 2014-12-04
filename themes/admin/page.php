<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title> Page title </title>
  <link rel="stylesheet" href="css/styles.css?v=1.0">
  <!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  <?php echo fireDomEvent('template.addingJavascripts') ?>
  <link rel="stylesheet" href="<?php echo getBasePath() ?>libraries/foundation/foundation-5.4.6/css/foundation.css" />
  <?php if (isset($head)) : ?>
    <?php echo $head(); ?>
  <?php endif ?>
</head>
<style>
  table {
    width: 100%;
  }
  a.active {
    font-weight: bold;
  }
</style>

<body>

<nav class="top-bar" data-topbar role="navigation">
  <ul class="title-area">
    <li class="name">
      <h1><a href="<?php echo href('ulysse.content.list') ?>"> Administration </a></h1>
    </li>
    <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
    <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
  </ul>

  <section class="top-bar-section">
    <!-- Right Nav Section -->
    <ul class="right">
      <li><a class="<?php if(getCurrentPath() == '') echo 'active' ?>" href="<?php echo href('ulysse.framework.homepage') ?>"> Homepage </a></li>
    </ul>


    <!-- Left Nav Section -->
    <ul class="left">

      <li class="<?php if(currentRouteIsParentOf('ulysse.content.list') || isCurrentRoute('ulysse.content.list')) echo 'active' ?>">
        <a href="<?php echo href('ulysse.content.list') ?>"> Content </a>
      </li>

      <li class="<?php if(getCurrentRouteId() == 'ulysse.content.create') echo 'active' ?>">
        <a href="<?php echo href('ulysse.content.create', buildAutoRedirectionQueryString()) ?>"> Add content </a>
      </li>

    </ul>
  </section>
</nav>




<div class="row">

<nav>
  <ul>


  </ul>
</nav>

<?php print $content ?>

</div>

<script src="<?php echo getBasePath() ?>libraries/foundation/foundation-5.4.6/js/vendor/jquery.js"></script>
<script src="<?php echo getBasePath() ?>libraries/foundation/foundation-5.4.6/js/foundation.min.js"></script>
<script>
  $(document).foundation();
</script>

</body>
</html>