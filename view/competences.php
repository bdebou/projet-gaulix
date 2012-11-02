<div class="main">
<h1>Compétences</h1>
<?php
	global $objManager;
	
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	$oMaison = $oJoueur->GetObjSaMaison();
?>
<div class="perfectionnement">
	<?php echo AfficheModulePerfectionnement(objArmement::TYPE_ATTAQUE, $oJoueur, $oMaison);?>
</div>
<div class="perfectionnement">
	<?php echo AfficheModulePerfectionnement(objArmement::TYPE_DEFENSE, $oJoueur, $oMaison);?>
</div>
	<table class="lst_competences">
	<?php
	$temp = AfficheAutreCompetences($oJoueur, $oMaison);
	echo $temp[0];
	?>
	</table>
	<?php echo $temp[1];?>
</div>
<?php 
	$objManager->UpdateBatiment($oMaison);
	$objManager->update($oJoueur);
	unset($oJoueur, $oMaison);
?>
