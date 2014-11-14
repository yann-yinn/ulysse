<?php
use okc\framework\server;
global $_context;
?>
<!DOCTYPE html>
<!--[if IE 9]>
<html class="lt-ie10" lang="en" >
<![endif]--> <html class="no-js" lang="en" >
<head>
  <base href="<?php e($_context['okc\framework\server']['base_path']) ?>">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> Simulateur </title>
  <link rel="stylesheet" href="assets/css/app.css">
  <script src="assets/bower_components/foundation/js/vendor/modernizr.js"></script>
</head>
<body>



<div class="row">
  <div class="small-12 columns">
    <h1 class="text-center"> <?php print $page_title ?> </h1>
    <?php if (!empty($page_sub_title)) : ?>
      <h2 class="text-center"> <?php print $page_sub_title ?> </h2>
    <?php endif ?>
  </div>
</div>
<br/>

<div class="row">
  <div class="small-12 columns">
    <?php if (!empty($page_header)) : ?>
      <?php print $page_header ?>
    <?php endif ?>
  </div>
</div>


<div class="row">

  <div class="small-12 columns">
    <div class="page-content">

      <?php if (!empty($page_content_top)) : ?>
        <?php print $page_content_top ?>
      <?php endif ?>

      <?php print $page_content ?>

    </div>
  </div>

</div>

<script src="assets/bower_components/foundation/js/vendor/jquery.js"></script>
<script src="assets/bower_components/foundation/js/foundation.min.js"></script>
<script> $(document).foundation(); </script>
</body>



</html>