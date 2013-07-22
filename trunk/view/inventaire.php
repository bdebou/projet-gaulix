<div class="main">
<h1>Votre Bolga <dfn>(Sac)</dfn></h1>
<div class="Main_Ressources">
<h2>Ressources de base</h2>
	<table class="Main_Ressources">
		<tr>
			<td style="background-color:<?php echo $arCouleurs[personnage::TYPE_RES_MONNAIE];?>; width:20%;">
				<?php echo AfficheIcone(personnage::TYPE_RES_MONNAIE);?> : <?php echo $oJoueur->GetArgent();?>
			</td>
			<?php if(!is_null($oMaison)){?>
			<td style="width:20%; background-color:<?php echo $arCouleurs[maison::TYPE_RES_NOURRITURE];?>;">
				<?php echo AfficheRessource(maison::TYPE_RES_NOURRITURE, $oJoueur, $oMaison)?>
			</td>
			<td style="width:20%; background-color:<?php echo $arCouleurs[maison::TYPE_RES_EAU_POTABLE];?>;">
				<?php echo AfficheRessource(maison::TYPE_RES_EAU_POTABLE, $oJoueur, $oMaison)?>
			</td>
			<?php }else{?>
			<td colspan="2" style="background-color:white; width:40%;">
				Pas de maison installée.
			</td>
			<?php }?>
			<td style="width:40%;">
				Capacité du Bolga : <?php echo count($oJoueur->GetLstInventaire())?> / <?php echo $oJoueur->QuelCapaciteMonBolga()?>
			</td>
		</tr>
	</table>
</div>
<?php echo AfficheListObjets($oJoueur, $lstTypeObjets);?>
<script>
	window.onload = function() { 
		var i=0;
		
		for(i = 0; i<=lstCursors.length; i++){
			printValue(lstCursors[i].SliderName, lstCursors[i].RangeValue);
		}
	}
</script>
</div>