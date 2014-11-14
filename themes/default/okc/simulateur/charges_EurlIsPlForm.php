
<form class="hide-for-print" action="" method="GET">


  <div class="row">

    <div class="small-12 large-2 columns">

      <label class="<?php if(isset($form->errors['caHt']))  e('error') ?>">
        Chiffre d'affaire HT <br/><br/>
        <input type="text" name="caHt" value="<?php e($form->fields['caHt']) ?>">
      </label>

      <?php if (isset($form->errors['caHt'])) : ?>
        <small class="error"><?php e(implode('<br />', $form->errors['caHt'])) ?> </small>
      <?php endif ?>

    </div>

    <div class="small-12 large-2 columns">

      <label class="<?php if (isset($form->errors['salaire'])) e('error') ?>">
        Rémunération <br/><br/>
        <input type="text" name="salaire" value="<?php e($form->fields['salaire']) ?>">
      </label>
      <?php if (isset($form->errors['salaire'])) : ?>
        <small class="error"><?php e(implode('<br />', $form->errors['salaire'])) ?></small>
      <?php endif ?>

    </div>

    <div class="small-12 large-2 columns">

      <label class="<?php if (isset($form->errors['frais'])) e('error') ?>">
        Frais <br/><br/>
        <input type="text" name="frais" value="<?php e($form->fields['frais']) ?>">
      </label>
      <?php if (isset($form->errors['frais'])) : ?>
        <small class="error"><?php e(implode('<br />', $form->errors['frais'])) ?></small>
      <?php endif ?>

    </div>

    <div class="small-12 large-2 columns">

      <label class="<?php if (isset($form->errors['cfe'])) e('error') ?>">
        CFE (selon votre commune)
        <input type="text" name="cfe" value="<?php e($form->fields['cfe']) ?>">
      </label>
      <?php if (isset($form->errors['cfe'])) : ?>
        <small class="error"><?php e(implode('<br />', $form->errors['cfe'])) ?></small>
      <?php endif ?>

    </div>

    <div class="small-12 large-2 columns">

      <label class="<?php if (isset($form->errors['cotisationsSocialesDejaVerseesEnN'])) e('error') ?>">
        Cotisation sociales déjà versées en N
        <input type="text" name="cotisationsSocialesDejaVerseesEnN" value="<?php e($form->fields['cotisationsSocialesDejaVerseesEnN']) ?>">
      </label>
      <?php if (isset($form->errors['cotisationsSocialesDejaVerseesEnN'])) : ?>
        <small class="error"><?php e(implode('<br />', $form->errors['cotisationsSocialesDejaVerseesEnN'])) ?></small>
      <?php endif ?>

    </div>

    <div class="row">
      <div class="small-12 large-2 columns">
        <label> Première année
          <input type="radio" <?php if($form->fields['cotisationRegime'] == 'annee_1') e("checked=checked") ?> name="cotisationRegime" value="annee_1">
        </label>

        <label>Deuxieme année
          <input type="radio" <?php if($form->fields['cotisationRegime'] == 'annee_2') e("checked=checked") ?> name="cotisationRegime" value="annee_2">
        </label>

        <label>Regime etabli
          <input type="radio" <?php if($form->fields['cotisationRegime'] == 'regime_etabli') e("checked=checked") ?> name="cotisationRegime" value="regime_etabli">
        </label>

        <?php if (isset($form->errors['cotisationRegime'])) : ?>
          <small class="error"><?php e(implode('<br />', $form->errors['cotisationRegime'])) ?></small>
        <?php endif ?>
      </div>
    </div>


    <div class="row">
      <div class="small-12 large-12 columns text-center">
        <br/>
        <input class="button radius primary" type="submit" value="CALCULER" name="submitted">
      </div>
    </div>

  </div><!-- /.row -->

  <!--
      <label>
      <span data-tooltip class="has-tip" title="Indiquez ici les acomptes de TVA déjà versée au titre de l'année N. N'incluez pas les régularisations de l'année N concernant N-1 ou antérieurs.">
           TVA prévisionnelle déjà versée en N
        </span>
        <input type="text" name="tvaDejaVerseePourN" value="<?php //e($tvaDejaVerseePourN) ?>">
      </label>
      -->



</form>