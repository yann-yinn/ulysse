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
  <?php if (isset($head)) : ?>
    <?php echo $head(); ?>
  <?php endif ?>
</head>
<style>
  a.active {
    font-weight: bold;
  }
</style>

<body>

<nav class="top-bar" data-topbar role="navigation">
  <ul class="title-area">
    <li class="name">
      <h1><a href="#"> Administration </a></h1>
    </li>
    <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
    <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
  </ul>

  <section class="top-bar-section">
    <!-- Right Nav Section -->
    <ul class="right">
      <li><a class="<?php if(getCurrentPath() == '') echo 'active' ?>" href="<?php echo url('') ?>"> Homepage </a></li>
    </ul>


    <!-- Left Nav Section -->
    <ul class="left">
      <li><a class="<?php if(getCurrentPath() == 'admin/content/form') echo 'active' ?>" href="<?php echo url('admin/content/form') ?>"> Add content </a></li>
      <li><a class="<?php if(getCurrentPath() == 'admin') echo 'active' ?>" href="<?php echo url('admin') ?>"> Content list </a></li>
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

</body>
</html>