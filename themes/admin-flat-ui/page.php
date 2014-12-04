<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> Page title </title>
  <link rel="stylesheet" href="css/styles.css?v=1.0">
  <!-- Loading Bootstrap -->
  <link href="<?php echo getBasePath() ?>libraries/designmodo/flat-ui/dist/css/vendor/bootstrap.min.css" rel="stylesheet">

  <?php echo fireDomEvent('template.addingJavascripts') ?>
  <!-- Loading Flat UI -->
  <link href="<?php echo getBasePath() ?>libraries/designmodo/flat-ui/dist/css/flat-ui.min.css" rel="stylesheet">

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
  <!--[if lt IE 9]>
  <script src="<?php echo getBasePath() ?>libraries/designmodo/flat-ui/dist/js/vendor/html5shiv.js"></script>
  <script src="<?php echo getBasePath() ?>libraries/designmodo/flat-ui/dist/js/vendor/respond.min.js"></script>
  <![endif]-->

</head>
<style>

</style>

<body>

<div class="container">

  <div class="row">
    <div class="col-xs-12">
      <nav class="navbar navbar-lg navbar-default navbar-embossed navbar-inverse" role="navigation">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-01">
            <span class="sr-only">Toggle navigation</span>
          </button>
          <a class="navbar-brand" href="#">ULYSSE</a>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse-01">
          <ul class="nav navbar-nav navbar-left">
            <li class="<?php if(getCurrentPath() == 'admin/content/create') echo 'active' ?>"><a href="<?php echo href('ulysse.content.create', 'redirection=ulysse.content.list') ?>"> Add content </a></li>
            <li class="<?php if(currentRouteIsParentOf('ulysse.content.list')) echo 'active' ?>"><a  href="<?php echo href('ulysse.content.list.online') ?>"> Content </a></li>
          </ul>
          <form class="navbar-form navbar-right" action="#" role="search">
            <div class="form-group">
              <div class="input-group">
                <input class="form-control" id="navbarInput-01" type="search" placeholder="Search">
                    <span class="input-group-btn">
                      <button type="submit" class="btn"><span class="fui-search"></span></button>
                    </span>
              </div>
            </div>
          </form>
        </div><!-- /.navbar-collapse -->
      </nav><!-- /navbar -->
    </div>
  </div>



  <?php print $content ?>


</div>


<!-- jQuery (necessary for Flat UI's JavaScript plugins) -->
<script src="<?php echo getBasePath() ?>libraries/designmodo/flat-ui/dist/js/vendor/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo getBasePath() ?>libraries/designmodo/flat-ui/dist/js/vendor/video.js"></script>
<script src="<?php echo getBasePath() ?>libraries/designmodo/flat-ui/dist/js/flat-ui.min.js"></script>
<script>
  $("select").select2({dropdownCssClass: 'dropdown-inverse'});
</script>
</body>
</html>