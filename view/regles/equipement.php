<h1>Equipement</h1>
	<p>Vous présente votre équipement. Chaque équipement augmente votre pouvoir d\'attaque ou de défense.</p>
	<p>Si vous cliquez sur un élément, il sera remis dans votre inventaire.</p>
	<p>Voici la liste des équipements possibles avec leurs caractéristiques et valeurs.</p>
<h2 style="clear:both;">Armes</h2>
	<?php echo ReglesAfficheTableauEquipements($oDB, objArmement::TYPE_ARME);?>
<h2 style="clear:both;">Boucliers</h2>
	<?php echo ReglesAfficheTableauEquipements($oDB, objArmement::TYPE_BOUCLIER);?>
<h2 style="clear:both;">Casques</h2>
	<?php echo ReglesAfficheTableauEquipements($oDB, objArmement::TYPE_CASQUE);?>
<h2 style="clear:both;">Jambières</h2>
	<?php echo ReglesAfficheTableauEquipements($oDB, objArmement::TYPE_CUIRASSE);?>
<h2 style="clear:both;">Cuirasses</h2>
	<?php echo ReglesAfficheTableauEquipements($oDB, objArmement::TYPE_JAMBIERE);?>