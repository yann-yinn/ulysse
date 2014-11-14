<?php
namespace okc\framework;

/**
 * Cette classe aide à encapsuler la logique liée au fonctionnement d'un formulaire ,
 * afin de favoriser sa réutilisation d'un controller à un autre.
 * Cela peut comprendre notamment :
 * - la validation des données passées via le constructeur, et la remontée
 *   d'erreur en cas d'échec de validation des données.
 * - les valeurs par défaut des champs
 * - d'une manière ou d'une autre toutes les méta-données liées à l'interaction
 *   d'un formulaire avec l'utilisateur.
 *
 * Elle ne génère aucun code html et l'objet form_controller doit ensuite être utilisé
 * dans un template html pour créer manuellement un formulaire à l'ancienne.
 *
 * @package okc\simulateur\forms
 */
abstract class form_controller {

  // contient la liste des champs du formulaire et leurs valeurs par défaut sous la forme
  // d'un tableau associatif clef => valeur.
  public $fields = [];

  // les erreurs remontés par le formulaire par la méthode "validation".
  // Tableau associatif clef => valeur, où clef doit correspondant à une clef
  // de la propriété "fields", afin que le template sache à quel champ
  // associer quelle erreur.
  public $errors = [];

  // indique si le formulaire a été soumis ou pas.
  // par défaut, le constructeur de base considère que le formulaire a été soumis
  // si le parametre $value contient au moins une des clefs du tableau $fields.
  public $submitted = FALSE;

  /**
   * @param array $values
   *   Donné pour remplir les champs du formulaire. On peut y passer
   * $_GET ou $_POST à partir d'un controller
   */
  function __construct($values = []) {
    // récupération / initialisation des variables via données du formulaire.
    foreach ($this->fields as $key => $value) {
      if (isset($values[$key])) {
        $this->submitted = TRUE;
        $this->fields[$key] = $values[$key];
      }
    }

    // if form as been submitted, validate datas.
    // it's up to the model to do anything with datas.
    if ($this->is_submitted()) {
      $this->validation();
    }
  }

  /**
   * @param $values : values passed to the form.
   * @return TRUE if form as been submitted, FALSE otherwise
   */
  abstract function is_submitted();

  /**
   * @return mixed
   * Dans cette fonction, les données des champs sont validées à la soumission
   * du formulaire. La méthode validation a pour but de remplir la
   * variable $this->errors.
   * Le controller utilise ensuite $this->errors pour savoir si il peut continuer
   * à dérouler ses opérations normalement ou pas.
   */
  abstract function validation();

}