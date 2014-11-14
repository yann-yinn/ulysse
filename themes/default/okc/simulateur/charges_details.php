
<?php if ($calculs->totalCharges > $calculs->caHt) : ?>

<h2 class="text-center"> Déficit : <strong style="color:red">
  <?php e($calculs->totalCharges - $calculs->caHt, 'euros') ?>
</strong>

<?php else : ?>

  <h2 class="text-center"> Bénéfice : <strong style="color:green">
      <?php e($calculs->caHt - $calculs->totalCharges, 'euros') ?>
    </strong>
  </h2>

<?php endif; ?>

<h3> Récapitulatif de vos charges :
  <div style="float:right">
    <a class="tiny button secondary radius" href="javascript:window.print()">Imprimer</a>
  </div>
</h3>

<table>
  <thead>
  <tr>
    <th>
      Poste
    </th>
    <th>
      Montant
    </th>
  </tr>
  </thead>
  <tbody>

  <tr>
    <td>
      Rémunération
    </td>
    <td>
      <?php e($calculs->salaire, 'euros') ?>
    </td>
  </tr>

  <tr>
    <td>
      Cotisations sociales
    </td>
    <td>
      <?php e($calculs->totalCotisationsSociales, 'euros') ?>
      <ul>
        <?php foreach ($calculs->getTotalCotisationsSocialesParOrganismes() as $organismeId => $organisme_datas) : ?>
          <li>
            <?php e($organisme_datas['label']) ?>   :  <?php e($organisme_datas['total'], 'euros') ?>
          </li>
        <?php endforeach ?>
      </ul>
    </td>
  </tr>

  <tr>
    <td>
      Impot sur la société
    </td>
    <td>
      <?php e($calculs->is['total'], 'euros') ?>
    </td>
  </tr>

  <tr>
    <td>
      Frais
    </td>
    <td>
      <?php e($calculs->frais, 'euros') ?>
    </td>
  </tr>

  <tr>
    <td>
      CFE
    </td>
    <td>
      <?php e($calculs->cfe, 'euros') ?>
    </td>
  </tr>

  <tr>
    <td>
      <strong>Total</strong>
    </td>
    <td>
      <strong> <?php e($calculs->totalCharges, 'euros') ?> </strong>
    </td>
  </tr>

  </tbody>
</table>

<h3> Détails des calculs </h3>
<table>
<thead>
<tr>
  <th>
    Nom
  </th>
  <th>
    Organisme
  <th>
    Base calcul
  </th>
  <th>
    Taux
  </th>
  <th>
    Montant
  </th>

</tr>
</thead>
<tbody>


<tr>
  <td>
    Allocation familiales
  </td>
  <td>
    <?php print $calculs->getOrganisme('allocationsFamiliales')['label'] ?>
  </td>
  <td>
    <?php e($calculs->baseCalculCotisationsSociales, 'euros') ?>
  </td>
  <td>
    <?php e($datas['allocationsFamiliales']['tranches'][0]['taux']) ?>
  </td>
  <td>
    <?php e($calculs->allocationsFamiliales['total'], 'euros') ?>
  </td>
</tr>

<tr>
  <td>
    Formation professionnelle
  </td>
  <td>
    <?php e($calculs->getOrganisme('formationProfessionnelle')['label']) ?>
  </td>
  <td>
    <?php e($calculs->formationProfessionnelle['tranches'][0]['baseCalcul'], 'euros') ?>
  </td>
  <td>
    <?php e($calculs->formationProfessionnelle['tranches'][0]['taux'], 'euros') ?>
  </td>
  <td>
    <?php e($calculs->formationProfessionnelle['total'], 'euros') ?>
  </td>
</tr>

<tr>
  <td>
    Maladie maternité
  </td>
  <td>
    <?php e($calculs->getOrganisme('maladieMaternite')['label']) ?>
  </td>
  <td>
    <?php e($calculs->baseCalculCotisationsSociales, 'euros') ?>
  </td>
  <td>
    <?php e($datas['maladieMaternite']['tranches'][0]['taux']) ?>
  </td>
  <td>
    <?php e($calculs->maladieMaternite['total'], 'euros') ?>
  </td>
</tr>



<?php // si premiere année, pas de tranche mais un forfait ?>
<?php if(isset($calculs->assuranceVieillesseBase['tranches'])) : ?>
  <tr>
    <td>
      Assurance vieillesse base tranche 1
    </td>
    <td>
      <?php e($calculs->getOrganisme('assuranceVieillesseBase')['label']) ?>
    </td>
    <td>
      <?php e($calculs->assuranceVieillesseBase['tranches'][0]['baseCalcul'], 2, 'euros') ?>
    </td>
    <td>
      <?php e($calculs->assuranceVieillesseBase['tranches'][0]['taux'], 'euros') ?>
    </td>
    <td>
      <?php e($calculs->assuranceVieillesseBase['tranches'][0]['cotisation'], 'euros') ?>
    </td>
  </tr>

  <tr>
    <td>
      Assurance vieillesse base tranche 2 - <?php print $calculs->getOrganisme('assuranceVieillesseBase')['label'] ?>
    </td>
    <td>
      <?php print $calculs->getOrganisme('assuranceVieillesseBase')['label'] ?>
    </td>
    <td>
      <?php e($calculs->assuranceVieillesseBase['tranches'][1]['baseCalcul'], 'euros') ?>
    </td>
    <td>
      <?php e($calculs->assuranceVieillesseBase['tranches'][1]['taux'], 'euros') ?>
    </td>
    <td>
      <?php e($calculs->assuranceVieillesseBase['tranches'][1]['cotisation'], 'euros') ?>
    </td>
  </tr>

<?php endif ?>

<tr>
  <td>
    Assurance vieillesse base Total
  </td>
  <td>
    <?php print $calculs->getOrganisme('assuranceVieillesseBase')['label'] ?>
  </td>
  <td></td>
  <td></td>

  <td>
    <?php e($calculs->assuranceVieillesseBase['total'], 'euros') ?>
  </td>
</tr>

<tr>
  <td>
    Assurance vieillesse Complémentaire
  </td>
  <td>
    <?php print $calculs->getOrganisme('assuranceVieillesseComplementaire')['label'] ?>
  </td>
  <td>
    <?php e($calculs->baseCalculCotisationsSociales, 'euros') ?>
  </td>
  <td>
    tranche <?php e($calculs->assuranceVieillesseComplementaire['trancheActive']['nom']) ?>
  </td>
  <td>
    <?php e($calculs->assuranceVieillesseComplementaire['total'], 'euros') ?>

  </td>
</tr>

<tr>
  <td>
    Invalidité décés
  </td>
  <td>
    <?php print $calculs->getOrganisme('invaliditeDeces')['label'] ?>
  </td>
  <td>
    <?php e($calculs->baseCalculCotisationsSociales, 'euros') ?>
  </td>
  <td>
    classe <?php e(strtoupper($calculs->classeInvaliditeDeces)) ?>
  </td>
  <td>
    <?php e($calculs->invaliditeDeces['total'], 'euros') ?>

  </td>
</tr>

<!--
<tr>
  <td>
    <strong>Total cotisations hors CGS / CRDS</strong>
  </td>
  <td>

  </td>
  <td>
  </td>
  <td></td>
  <td>
    <strong><?php //e($calculs->totalCotisationsSocialesHorsCsg, 'euros') ?></strong>
  </td>
</tr>
-->



<tr>
  <td>
    CSG Déductible
  </td>
  <td>
    <?php print $calculs->getOrganisme('csgDeductible')['label'] ?>
  </td>
  <td>
    <?php e($calculs->baseCalculCsgCrds, 'euros') ?>
  </td>
  <td>
    <?php e($calculs->csgDeductible['tranches'][0]['taux'], 'euros') ?>
  </td>
  <td>
    <?php e($calculs->csgDeductible['total'], 'euros') ?>

  </td>
</tr>

<tr>
  <td>
    CSG - Non déductible
  </td>
  <td>
    <?php print $calculs->getOrganisme('csgNonDeductible')['label'] ?>
  </td>
  <td>
    <?php e($calculs->baseCalculCsgCrds, 'euros') ?>
  </td>
  <td>
    <?php e($calculs->csgNonDeductible['tranches'][0]['taux'], 'euros') ?>
  </td>
  <td>
    <?php e($calculs->csgNonDeductible['total'], 'euros') ?>

  </td>
</tr>

<tr>
  <td>
    CRDS - Non déductible
  </td>
  <td>
    <?php print $calculs->getOrganisme('crds')['label'] ?>
  </td>
  <td>
    <?php e($calculs->baseCalculCsgCrds, 'euros') ?>
  </td>
  <td>
    <?php e($calculs->crds['tranches'][0]['taux'], 'euros') ?>
  </td>
  <td>
    <?php e($calculs->crds['total'], 'euros') ?>
  </td>
</tr>

<tr>
  <td>
     CGS / CRDS <?php print $calculs->getOrganisme('csg')['label'] ?>
  </td>
  <td>
    <?php print $calculs->getOrganisme('csgDeductible')['label'] ?>
  </td>
  <td>

  </td>
  <td></td>
  <td>
    <?php e($calculs->csgCrds, 'euros') ?>
  </td>
</tr>

<tr>
  <td>
    <strong>TOTAL COTISATIONS SOCIALES (avec CSG / CRDS) </strong>
  </td>
  <td></td>
  <td></td>
  <td></td>
  <td>
    <strong><?php e($calculs->totalCotisationsSociales, 'euros') ?></strong>
  </td>
</tr>

<!--
<tr>
  <td>
    Pourcentage cotisations par rapport à la rémunération :
  </td>
  <td></td>
  <td></td>
  <td></td>
  <td>
    <?php if ($calculs->salaire > 0) : ?>
      <?php //e(($calculs->totalCotisationsSociales * 100) / $calculs->salaire, 'euros') ?> %
    <?php endif ?>
  </td>
</tr>

<tr>
-->

  <td>
    IMPOT SUR LA SOCIETE Tranche 1
  </td>
  <td></td>
  <td>
    <?php e($calculs->is['tranches'][0]['baseCalcul'], 'euros') ?>
  </td>
  <td>
    <?php e($calculs->is['tranches'][0]['taux'], 'euros') ?>
  </td>
  <td>
    <?php e($calculs->is['tranches'][0]['cotisation'], 'euros'); ?>
  </td>
</tr>

<tr>

  <td>
    IMPOT SUR LA SOCIETE Tranche 2
  </td>
  <td></td>
  <td>
    <?php e($calculs->is['tranches'][1]['baseCalcul'], 'euros') ?>
  </td>
  <td>
    <?php e($calculs->is['tranches'][1]['taux'], 'euros') ?>
  </td>
  <td>
    <?php e($calculs->is['tranches'][1]['cotisation'], 'euros'); ?>
  </td>
</tr>

<tr>

  <td>
    <strong> TOTAL IS  </strong>
  </td>
  <td></td>
  <td>
    <?php e($calculs->baseCalculIs, 'euros') ?>
  </td>
  <td>

  </td>
  <td>
    <strong><?php e($calculs->is['total'], 'euros'); ?></strong>
  </td>
</tr>

</tbody>

</table>
