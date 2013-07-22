<div class="main">
<h1>Compétences</h1>
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