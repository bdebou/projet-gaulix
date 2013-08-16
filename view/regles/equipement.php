<h1>Equipement</h1>
	<p>Vous pr�sente votre �quipement. Chaque �quipement augmente votre pouvoir d\'attaque ou de d�fense.</p>
	<p>Si vous cliquez sur un �l�ment, il sera remis dans votre inventaire.</p>
	<p>Voici la liste des �quipements possibles avec leurs caract�ristiques et valeurs.</p>
<h2 style="clear:both;">Armes</h2>
	<?php echo ReglesAfficheTableauEquipements($oDB, objArmement::TYPE_ARME);?>
<h2 style="clear:both;">Boucliers</h2>
	<?php echo ReglesAfficheTableauEquipements($oDB, objArmement::TYPE_BOUCLIER);?>
<h2 style="clear:both;">Casques</h2>
	<?php echo ReglesAfficheTableauEquipements($oDB, objArmement::TYPE_CASQUE);?>
<h2 style="clear:both;">Jambi�res</h2>
	<?php echo ReglesAfficheTableauEquipements($oDB, objArmement::TYPE_CUIRASSE);?>
<h2 style="clear:both;">Cuirasses</h2>
	<?php echo ReglesAfficheTableauEquipements($oDB, objArmement::TYPE_JAMBIERE);?>