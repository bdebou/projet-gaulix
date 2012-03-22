<?php
global $arCouleurs, $objManager;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

$arAtt = $oJoueur->GetAttPerso();
$arDef = $oJoueur->GetDefPerso();
?>

<div class="loginstatus">
	<a id="TopPage"></a>
	<table class="loginstatus">
		<tr>
			<td style="background-color: Brown; font-weight:bold; text-transform:uppercase; width:200px;">
				<?php echo $oJoueur->GetLogin();?> (<?php echo $oJoueur->GetNiveau();?>)
				<?php echo AfficheRecompenses($oJoueur->GetLogin(), $oJoueur->GetClan());?>
			</td>
			<td style="width:150px;">
				<img alt="Barre de Vie" src="./fct/fct_image.php?type=vie&amp;value=<?php echo $oJoueur->GetVie();?>&amp;max=<?php echo personnage::VIE_MAX;?>" />
			</td>
			<td style="background-color:<?php echo $arCouleurs['Attaque'];?>; width:120px;">
				<?php echo AfficheIcone('attaque');?> : <?php echo $arAtt['0'];?> (<?php echo $arAtt['1'];?>)
			</td>
			<td style="background-color:<?php echo $arCouleurs['Or'];?>; width:145px;">
				<?php echo AfficheIcone('or');?> : <?php echo $oJoueur->GetArgent();?>
			</td>
			<?php if(!is_null(FoundBatiment(1))){?>
			<td style="background-color:<?php echo $arCouleurs['Nourriture'];?>;">
				<?php echo AfficheRessource('Nourriture', $oJoueur)?>
			</td>
			<?php }else{?>
			<td style="background-color: white;">
				&nbsp;
			</td>
			<?php }?>
		</tr>
		<tr>
			<td>
				<button type="button" onclick="window.location='./index.php?page=unconnect'" style="width:120px;">Se déconnecter</button>
			</td>
			<td style="width:150px;">
				<img alt="Barre d'expérience" src="./fct/fct_image.php?type=experience&amp;value=<?php echo $oJoueur->GetExpPerso();?>&amp;max=<?php echo $oJoueur->GetMaxExperience();?>" />
			</td>
			<td style="background-color:<?php echo $arCouleurs['Defense'];?>;">
				<?php echo AfficheIcone('defense');?> : <?php echo $arDef['0'];?> (<?php echo $arDef['1'];?>)
			</td>
			<?php if(!is_null(FoundBatiment(1))){?>
			<td style="background-color:<?php echo $arCouleurs['Pierre'];?>;">
				<?php echo AfficheRessource('Pierre', $oJoueur)?>
			</td>
			<td style="background-color:<?php echo $arCouleurs['Bois'];?>;">
				<?php echo AfficheRessource('Bois', $oJoueur)?>
			</td>
			<?php }else{?>
			<td colspan="2" style="background-color: white;">
				Pas de maison installée.
			</td>
			<?php }?>
		</tr>
	</table>
</div>

<?php 
$objManager->update($oJoueur);
unset($oJoueur);
?>