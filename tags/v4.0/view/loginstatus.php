<?php
global $arCouleurs, $objManager;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

$arAtt = $oJoueur->GetAttPerso();
$arDef = $oJoueur->GetDefPerso();
$arDis = $oJoueur->GetDisPerso();
?>

<div class="loginstatus">
	<a id="TopPage"></a>
	<table class="loginstatus">
		<tr>
			<td style="background-color: Brown; width:200px;">
				<span class="login"><?php echo $oJoueur->GetLogin();?></span><?php echo AfficheRecompenses($oJoueur->GetLogin(), $oJoueur->GetClan());?>
				<br />
				<?php echo ucfirst(GetInfoCarriere($oJoueur->GetCodeCarriere(),'carriere_nom'));?>
			</td>
			<td colspan="3" style="width:150px;">
				<img alt="Barre de Vie" src="./fct/fct_image.php?type=vie&amp;value=<?php echo $oJoueur->GetVie();?>&amp;max=<?php echo personnage::VIE_MAX;?>&amp;taille=270x28" />
			</td>
			<td colspan="3" style="width:150px;">
				<img alt="Barre d'expérience" src="./fct/fct_image.php?type=experience&amp;value=<?php echo $oJoueur->GetExpPerso();?>&amp;max=<?php echo $oJoueur->GetMaxExperience();?>&amp;taille=270x28" />
			</td>
		</tr>
		<tr>
			<td>
				<button type="button" onclick="window.location='./index.php?page=unconnect'" style="width:120px;">Se déconnecter</button>
			</td>
			<td colspan="2" style="background-color:<?php echo $arCouleurs['Attaque'];?>;">
				<?php echo AfficheIcone('attaque');?> : <?php echo $arAtt['0'];?> (<?php echo $arAtt['1'];?>)
			</td>
			<td colspan="2" style="background-color:<?php echo $arCouleurs['Defense'];?>;">
				<?php echo AfficheIcone('defense');?> : <?php echo $arDef['0'];?> (<?php echo $arDef['1'];?>)
			</td>
			<td colspan="2" style="background-color:<?php echo $arCouleurs['Distance'];?>;">
				<?php echo AfficheIcone('distance');?> : <?php echo $arDis;?>
			</td>
		</tr>
	</table>
</div>

<?php 
$objManager->update($oJoueur);
unset($oJoueur);
?>