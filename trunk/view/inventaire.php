<div class="main">
<h1>Votre Bolga <dfn>(Sac)</dfn></h1>
<?php
	global $arCouleurs, $objManager;

	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
?>
<div class="Main_Ressources">
<h2>Ressources de base</h2>
	<table class="Main_Ressources">
		<tr>
			<td style="background-color:<?php echo $arCouleurs[personnage::TYPE_RES_MONNAIE];?>; width:20%;">
				<?php echo AfficheIcone(personnage::TYPE_RES_MONNAIE);?> : <?php echo $oJoueur->GetArgent();?>
			</td>
			<?php if(!is_null($oJoueur->GetObjSaMaison())){?>
			<td style="width:20%; background-color:<?php echo $arCouleurs[maison::TYPE_RES_NOURRITURE];?>;">
				<?php echo AfficheRessource(maison::TYPE_RES_NOURRITURE, $oJoueur)?>
			</td>
			<td style="width:20%; background-color:<?php echo $arCouleurs[maison::TYPE_RES_EAU_POTABLE];?>;">
				<?php echo AfficheRessource(maison::TYPE_RES_EAU_POTABLE, $oJoueur)?>
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
<?php
/* global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']); */

$lstObjetParCategory = NULL;
if(!is_null($oJoueur->GetLstInventaire()))
{
	$lstObjetParCategory = CreateListObjet($oJoueur->GetLstInventaire());
}

$id = 0;

foreach($lstTypeObjets as $Category)
{
	echo '<div class="'.$Category.'">
			<h2>'.$Category.'</h2>';
	if(isset($lstObjetParCategory['obj'.$Category]))
	{
		//echo '<table class="objets">';
				
		foreach($lstObjetParCategory['obj'.$Category] as $objObjet)
		{
			$_SESSION['inventaire'][$id]['code'] = $objObjet->GetCode();
			
			$txtUtiliser		= NULL;
			$txtVendre			= '<input type="submit" name="action" value="Vendre" />';
			$txtEntreposer		= NULL;
			$txtInfoArmement	= NULL;
			$txtEquiper			= NULL;
			$txtConvertir		= NULL;
			$txtConvertion		= NULL;
			$txtAbandonner		= '<input style="float:right; width:20px; height:20px;" type="image" src="./img/icones/ic_croix.png" name="action" value="Abandonner" alt="Abandonner" />';
			$nbLigne			= 4;
			$nbColonne			= 2;
			
			//On crée le boutton "entreposer" si on est bien sur un entrepot
			if(CheckIfOnEstSurUnBatiment(entrepot::ID_BATIMENT, $oJoueur->GetCoordonnee()))
			{
				$txtEntreposer = '<input type="submit" name="action" value="Entreposer" />';
				$nbLigne = 5;
			}
			
			if(in_array(QuelTypeObjet($objObjet->GetCode()), array(objDivers::TYPE_RES_DEP, objDivers::TYPE_RES_VIE)))
			{
				$txtValideUtiliser = NULL;
				if(	(QuelTypeObjet($objObjet->GetCode()) == objDivers::TYPE_RES_VIE AND ($oJoueur->GetVie() + $objObjet->GetNb(objDivers::TYPE_RES_VIE)) > personnage::VIE_MAX)
						OR
					(QuelTypeObjet($objObjet->GetCode()) == objDivers::TYPE_RES_DEP AND ($oJoueur->GetDepDispo() + $objObjet->GetNb(objDivers::TYPE_RES_DEP)) > personnage::DEPLACEMENT_MAX)
				   )
				{
					$txtValideUtiliser = ' disabled="disabled"';
				}
				
				$txtUtiliser = '<input '.$txtValideUtiliser.'type="submit" name="action" value="Utiliser" />';
				$nbLigne = 5;
				
			}else{
				if(!is_null($objObjet->GetRessource()))
				{
					$txtConvertion = AfficheListePrix($objObjet->GetRessource());
					$nbLigne++;
				
					$txtValideUtiliser = NULL;
					if(!CheckIfOnEstSurUnBatiment(maison::ID_BATIMENT, $oJoueur->GetCoordonnee()))
					{
						$txtValideUtiliser = ' disabled="disabled"';
					}
					$txtConvertir = '<input '.$txtValideUtiliser.'type="submit" name="action" value="Convertir" />';
				}
			}
			
			
			switch($Category)
			{
				case 'Construction':
					
					break;
				case 'Divers':
					
					break;
				case 'Ressource':
					
					break;
				case 'Armement':
					$txtInfoArmement = '<tr>'
											.'<td>'.AfficheIcone(objArmement::TYPE_ATTAQUE).' : '.$objObjet->GetAttaque().'</td>'
											.'<td>'.AfficheIcone(objArmement::TYPE_DEFENSE).' : '.$objObjet->GetDefense().'</td>'
											.'<td>'.AfficheIcone(objArmement::TYPE_DISTANCE).' : '.$objObjet->GetDistance().'</td>'
										.'</tr>';
					$nbColonne = 3;
					$nbLigne = 5;
					$nbLigne++;
					$txtEquiper = '<input type="submit" name="action" value="Equiper" />';
					break;
			}
			
			echo '<form class="inventaire" action="index.php?page=inventaire" formmethod="post" method="post">'
	.'<table class="objet">
		<tr><td rowspan="'.$nbLigne.'" style="width:105px; margin:auto;">'
					//.'<img src="./img/objets/'.$objObjet->GetCode().'.png" alt="'.$objObjet->GetNom().'" width="100px" onmouseover="montre(\''.CorrectDataInfoBulle($objObjet->GetInfoBulle()).'\');" onmouseout="cache();" style="vertical-align:middle;" />'
					.$objObjet->AfficheInfoObjet(100)
					.'</td></tr>'
					.'<tr><th colspan="'.$nbColonne.'">'.($objObjet->GetQuantite() > 1?'<b>'.$objObjet->GetQuantite().'x</b> ':'').$objObjet->GetNom().$txtAbandonner.'</th></tr>'
					.$txtInfoArmement
					.'<tr><td colspan="'.$nbColonne.'" style="text-align:center;">
						<input style="width:150px;" id="Slider'.$objObjet->GetCode().'" type="range" min="1" max="'.$objObjet->GetQuantite().'" step="1" value="1" onchange="printValue(\'Slider'.$objObjet->GetCode().'\',\'RangeValue'.$objObjet->GetCode().'\');" />
						<input style="width:50px;" name="qte" id="RangeValue'.$objObjet->GetCode().'" onchange="printValue(\'RangeValue'.$objObjet->GetCode().'\',\'Slider'.$objObjet->GetCode().'\');" type="number" min="1" max="'.$objObjet->GetQuantite().'" step="1" value ="1" size="15" />
						<script>printValue(\'Slider'.$objObjet->GetCode().'\',\'RangeValue'.$objObjet->GetCode().'\');</script>
					</td></tr>'
					.'<tr><td class="action_inventaire">'.$txtVendre.'</td><td colspan="'.($nbColonne - 1).'" style="text-align:center;">'.AfficheListePrix(array(personnage::TYPE_RES_MONNAIE.'='.$objObjet->GetPrix())).' par unité</td></tr>'
					.(!is_null($txtConvertion)?'<tr><td class="action_inventaire">'.$txtConvertir.'</td><td style="text-align:center;">'.$txtConvertion.' par unité</td>':'')
					.'<input type="hidden" name="id" value="'.$id.'" />'
					.((!is_null($txtEntreposer) OR !is_null($txtUtiliser) OR !is_null($txtEquiper))?'<tr><td colspan="'.$nbColonne.'" class="action_inventaire">'.$txtEntreposer.$txtUtiliser.$txtEquiper.'</td></tr>':NULL)
	.'</table>'
	.'</form>';
			$id++;
		}
		
		//echo '</table>';
	}else{
		echo 'Aucun objet de cette catégorie.';
	}
	echo '</div>';
}

$objManager->update($oJoueur);
unset($oJoueur);
?>
<script>
	window.onload = function() { 
		var i=0;
		
		for(i = 0; i<=lstCursors.length; i++){
			printValue(lstCursors[i].SliderName, lstCursors[i].RangeValue);
		}
	}
</script>
</div>