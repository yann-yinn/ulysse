<div class="panel radius">
  <form method="POST" action="<?php echo href("ulysse.content.delete", 'redirection=' . getFormRedirectionFromUrl()) ?>">

    <p class="text-center">Are you <em>sure</em> you want to delete content <strong><?php e($content['title']) ?> </strong> ?</p>
    <!-- ID FIELD -->
    <?php if(!empty($content['machine_name'])) : ?>
      <input type="hidden" name="machine_name" value="<?php echo $content['machine_name'] ?>" />
    <?php endif ?>


    <div class="text-center">
      <a class="button radius success" href="<?php e(href($_GET['redirection'])) ?>">
        Holy shit no ! that's a mistake ! i want to go back, I WANT TO GO BACK NOW !
      </a>


      <input class="button radius alert" value="Yes. Delete It. For Ever. I know what i am doing, you stupid machine." type="submit"/>

    </div>

  </form>
</div>

