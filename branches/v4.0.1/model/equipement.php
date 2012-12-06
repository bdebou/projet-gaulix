<?php
function AfficheEquipement($type, personnage &$oJoueur) {
	$SizeHeight = 50;

	$CodeObjet = GetCodeEquipement($type, $oJoueur);
	
	if(in_array($type, array(objArmement::TYPE_CUIRASSE, objArmement::TYPE_ARME)))
	{
		$SizeHeight = 150;
	}

	if (is_null($CodeObjet))
	{
		return '&nbsp;';
	}elseif(in_array($type, array(objDivers::TYPE_LIVRE)) AND $CodeObjet === 'NoBook')
	{
		if(is_null($oJoueur->GetLstSorts()))
		{
			return '&nbsp;';
		}else{
			$arSort = explode('=', current($oJoueur->GetLstSorts()));
			$oObjet = FoundObjet($arSort[0]);
		}
	}else{
		$oObjet = FoundObjet($CodeObjet);
	}

	//on vérifie si on a assez de place dans son bolga pour reprendre l'objet
	if(	count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga()
		AND !in_array($type, array(objDivers::TYPE_LIVRE)))
	{
		$chkLink = true;
	}else{
		$chkLink = false;
	}

	//Dans le cas du livre de sorts
	if(in_array($type, array(objDivers::TYPE_LIVRE)))
	{
		if($CodeObjet == 'NoBook')
		{
			if(is_null($oJoueur->GetLstSorts()))
			{
				return '&nbsp;';
			}else{
				$arSort = explode('=', current($oJoueur->GetLstSorts()));
				$CodeObjet = $arSort[0];
			}
		}else{
			//on affiche le livre
			if(!is_null($oJoueur->GetLstSorts()))
			{
				$InfoBulle = '<table class="equipement">';
				foreach ($oJoueur->GetLstSorts() as $Sort)
				{
					$arSort = explode('=', $Sort);
					$sql = "SELECT * FROM table_objets WHERE objet_code='" . strval($arSort[0]) . "';";
					$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
					$result = mysql_fetch_array($requete, MYSQL_ASSOC);

					$InfoBulle .= '<tr><th colspan="2">' . $result['objet_nom'] . '</th></tr>'
					. '<tr>'
					. '<td><img src="./img/objets/' . $arSort['0'] . '.png" alt="' . $result['objet_nom'] . '" height="50px" /></td>'
					. '<td>' . $result['objet_description'] . '</td>'
					. '</tr>';
				}
				$InfoBulle .= '</table>';
			}else{
				$InfoBulle = '<table class="equipement"><tr><th>Votre livre est vide.</th></tr></table>';
			}
		}

		return ($chkLink ?'
			<a href="./fct/main.php?action=unuse&amp;id=' . $type . '">' : NULL)
		. '<img src="./img/objets/' . $CodeObjet . '.png" '
		. 'height="' . $SizeHeight . '" '
		. 'alt="Livre de sort" '
		. 'onmouseover="montre(\'' . CorrectDataInfoBulle($InfoBulle) . '\');" '
		. 'onmouseout="cache();" '
		. '/>'
		. ($chkLink ?'</a>' : NULL);
	}else{
		return ($chkLink ?'
    		<a href="./fct/main.php?action=unuse&amp;id=' . $type . '">' : NULL)
		.$oObjet->AfficheInfoObjet($SizeHeight)
		. ($chkLink ?'</a>' : NULL);
	}
}

/**
 * Retourn le code de l'objet corrspondant
 * @param string $type 
 * @param personnage $oJoueur
 * @return NULL|string
 */
function GetCodeEquipement($type, personnage &$oJoueur){
	switch ($type) {
		case objArmement::TYPE_CASQUE:		return $oJoueur->GetCasque();
		case objArmement::TYPE_BOUCLIER:	return $oJoueur->GetBouclier();
		case objArmement::TYPE_JAMBIERE:	return $oJoueur->GetJambiere();
		case objArmement::TYPE_CUIRASSE:	return $oJoueur->GetCuirasse();
		case objArmement::TYPE_ARME:		return $oJoueur->GetArme();
		case objDivers::TYPE_SAC:			return $oJoueur->GetSac();
		case objDivers::TYPE_LIVRE:			return $oJoueur->GetLivre();
	}
	return NULL;
}
function AfficheDescriptifEquipement(personnage &$oJoueur){
	$txt = '
	<table class="equipement">
		<tr style="background:lightgrey;">
			<td colspan="8">&nbsp;</td>
		</tr>';
	$lstType = array(objArmement::TYPE_CASQUE, objArmement::TYPE_ARME, objArmement::TYPE_CUIRASSE, objArmement::TYPE_BOUCLIER, objArmement::TYPE_JAMBIERE);
	$id = 0;
	foreach($lstType as $type)
	{
		$CodeObjet = GetCodeEquipement($type, $oJoueur);
		
		if(!is_null($CodeObjet))
		{
			$oObjet = FoundObjet($CodeObjet);

			$_SESSION['Equipement'][$id] = $CodeObjet;
			
			$txt .= '
				<tr>
					<td rowspan="3" style="width:80px;">
						<a href="index.php?page=equipement&amp;action=unuse&amp;id='.$id.'">'.$oObjet->AfficheInfoObjet(100).'</a>
					</td>
					<td>'.$oObjet->GetNom().'</td>'
					.$oObjet->AfficheInfoTd(2)
					.'
				</tr>
				<tr>
					<td rowspan="2">'.$oObjet->GetDescription().'</td>
					<td colspan="3">Niv = '.$oObjet->GetNiveau().'</td>
					<td colspan="3">'.AfficheIcone(personnage::TYPE_RES_MONNAIE).' : '.$oObjet->GetPrix().'</td>
				</tr>
				<tr>
					<td colspan="6">
						<form class="equipement" action="index.php?page=equipement" formmethod="post" method="post">
							<input type="hidden" name="id" value="'.$id.'" />
							<input type="hidden" name="action" value="unUse" />
							<input type="submit" name="submit" value="Remettre dans mon Bolga"'.((CheckIfAssezRessource(array($oObjet->GetCode(), 1), $oJoueur, $oJoueur->GetObjSaMaison()) OR count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga())?NULL:' disabled="disabled"').' />
						</form>
					</td>
				</tr>
				<tr style="background:lightgrey;">
					<td colspan="8">&nbsp;</td>
				</tr>';
			$id++;
		}else{
			$txt .= '
		<tr>
			<td rowspan="3" style="width:100px;">'.$type.'</td>
			<td>Nom</td>
			<td colspan="2">Attaque</td>
			<td colspan="2">Defense</td>
			<td colspan="2">Distance</td>
		</tr>
		<tr>
			<td rowspan="2">Description</td>
			<td colspan="3">Niveau</td>
			<td colspan="3">Prix</td>
		</tr>
		<tr>
			<td colspan="6">Actions</td>
		</tr>
		<tr style="background:lightgrey;">
			<td colspan="8">&nbsp;</td>
		</tr>';
		}
		
	}
	$txt .= '
	</table>';
	return $txt;
}
//+---------------------------------+
//|				ACTIONS				|
//+---------------------------------+
function ActionUnuse(&$check, $id, personnage &$oJoueur){
	if(!is_null($id)){
		
		$oJoueur->DesequiperPerso($_SESSION['Equipement'][$id]);
		
		unset($_SESSION['Equipement'][$id]);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionUnuse';
	}
}

?>