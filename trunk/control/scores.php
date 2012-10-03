<?php
include('model/scores.php');

global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

$lignes = AfficheLignesClassement($oJoueur->GetCivilisation());

$objManager->update($oJoueur);

unset($oJoueur);
?>

