<div class="main">
<h1>Equipement</h1>
<p>Cliquez sur un objet pour le remettre dans votre inventaire.</p>
<?php
global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

if($oJoueur->GetDisPerso() > 0){echo '<p>Grace à votre arme employée, vous pouvez combattre vos ennemis qui sont à une distance de '.$oJoueur->GetDisPerso().'. Vous verrez automatiquement les joueurs qui sont à portée de tire dans la partie "Action".</p>';}
?>
<div class="equipement">
<table style="width:100%;">
	<tr>
	<td style="width:300px; border:1px solid black;">
	<table class="corps">
		<tr><td colspan="2"></td><td class="membre" style="height:30px;"><?php echo AfficheEquipement(1, $oJoueur);?></td><td colspan="2">&nbsp;</td></tr>
		<tr><td class="membre" style="height:80px; width: 15px;"><?php echo AfficheEquipement(5, $oJoueur);?></td><td colspan="3" class="membre" style=""><?php echo AfficheEquipement(4, $oJoueur);?></td><td class="membre" style="height:80px; width: 15px;"><?php echo AfficheEquipement(2, $oJoueur);?></td></tr>
		<tr><td>&nbsp;</td><td class="membre" style="height: 80px;"><?php echo AfficheEquipement(3, $oJoueur);?></td><td>&nbsp;</td><td class="membre" style="height: 80px;"><?php echo AfficheEquipement(3, $oJoueur);?></td><td>&nbsp;</td></tr>
		<tr><td colspan="5">&nbsp;</td></tr>
		<tr><td class="membre"><?php echo AfficheEquipement(7, $oJoueur);?></td><td colspan="3">&nbsp;</td><td class="membre"><?php echo AfficheEquipement(6, $oJoueur);?></td></tr>
	</table>
	</td>
	<td style="border:1px solid black;">
<?php
	echo AfficheDescriptifEquipement($oJoueur);
	
$objManager->update($oJoueur);
unset($oJoueur);
?>
	</td>
	</tr>
</table>
</div>
</div>