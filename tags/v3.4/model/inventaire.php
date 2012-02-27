<?php
function AffichePlaceVide(){
	return '
<table width="100%">
	<tr style="background:lightgrey; line-height:5px;"><td colspan="8">&nbsp;</td></tr>
	<tr>
		<td rowspan="4" style="width:80px;">Vide</td>
		<td colspan="6" class="tdtitre" title="Libre">Libre</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td colspan="2">&nbsp;</td>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="6">&nbsp;</td>
	</tr>
</table>';
}
function AfficheObjetInventaire($CodeObject, $arInfoObject, $id, $nbObjet, &$oJoueur){
	$_SESSION['main'][$id]['action'] = false;

	if(in_array($arInfoObject['objet_type'], array('ressource', 'potion'))){
		$_SESSION['main'][$id]['type'] = QuelTypeRessource($CodeObject);
	}else{
		$_SESSION['main'][$id]['type'] = $arInfoObject['objet_type'];
	}

	if(isset($arInfoObject['objet_attaque'])){
		$_SESSION['main'][$id]['value'] = $arInfoObject['objet_attaque'];
	}elseif($arInfoObject['objet_type'] == 'objet'){
		$_SESSION['main'][$id]['value'] = 1;
	}else{
		$_SESSION['main'][$id]['value'] = $arInfoObject['objet_nb'];
	}

	$_SESSION['main'][$id]['code'] = $CodeObject;
	$_SESSION['main'][$id]['prix'] = $arInfoObject['objet_prix'];

	$txtType = null;
	$IconeName = $arInfoObject['objet_type'];
	$reSizeImg = 100;

	switch($arInfoObject['objet_type']){
		case 'objet':
			$IconeName = $CodeObject;
			break;
		case 'sort':
			$txtType = '<button class="inventaire" type="button" onclick="window.location=\'index.php?page=inventaire&amp;action=sort&amp;id='.$id.'\'">Utiliser</button>';
			break;
		case 'ressource':
		case 'potion':
			if(in_array($_SESSION['main'][$id]['type'], array('nourriture', 'bois', 'pierre'))){
				//Cas si l'objet est de la nourriture, bois ou pierre
				if(is_null($oJoueur->GetMaisonInstalle())){
					$txtType = 'Pas Encore de Maison';
				}else{
					$txtType = '<button class="inventaire" type="button" onclick="window.location=\'index.php?page=inventaire&amp;action=utiliser&amp;id='.$id.'\'">Utiliser</button>';
				}
			}elseif(in_array($_SESSION['main'][$id]['type'], array('argent'))){
				//Cas si l'objet est de l'argent
				$txtType = '<button class="inventaire" type="button" onclick="window.location=\'index.php?page=inventaire&amp;action=utiliser&amp;id='.$id.'\'">Utiliser</button>';
				$_SESSION['main'][$id]['type'] = 'argent';
			}elseif(in_array($_SESSION['main'][$id]['type'], array('vie', 'deplacement'))
			OR in_array(substr($CodeObject, 0, 6), array('PotVie', 'PotDep'))
			){
				//Cas si l'objet est de la vie ou déplacement
				if(	(in_array(substr($CodeObject, 0, 6), array('ResVie', 'PotVie')) AND ($oJoueur->GetVie() + $_SESSION['main'][$id]['value']) <= personnage::VIE_MAX)
				OR (in_array(substr($CodeObject, 0, 6), array('ResDep','PotDep')) AND ($oJoueur->GetDepDispo() + $_SESSION['main'][$id]['value']) <= personnage::DEPLACEMENT_MAX)){
					$txtType = '<button class="inventaire" type="button" onclick="window.location=\'index.php?page=inventaire&amp;action=utiliser&amp;id='.$id.'\'">Utiliser</button>';
				}else{
					$txtType = 'Max atteint';
				}
			}
				
			$IconeName = $CodeObject;
			break;
		case 'casque':
		case 'bouclier':
		case 'cuirasse':
		case 'jambiere':
		case 'arme':
		case 'sac':
			$reSizeImg = 150;
			$txtType = '<button class="inventaire" type="button" onclick="window.location=\'index.php?page=inventaire&amp;action=equiper&amp;id='.$id.'\'">Equiper</button>';
			break;
		default:
	}

	if(	in_array($arInfoObject['objet_type'], array('casque', 'bouclier', 'cuirasse', 'jambiere', 'arme'))){
		$txtInfo = '
		<td colspan="2">'.AfficheIcone('attaque').' : '.$arInfoObject['objet_attaque'].'</td>
		<td colspan="2">'.AfficheIcone('defense').' : '.$arInfoObject['objet_defense'].'</td>
		<td colspan="2">'.AfficheIcone('distance').' : '.$arInfoObject['objet_distance'].'</td>';
		$InfoBulle = '<table class="equipement">'
		.'<tr><th colspan="3">'.$arInfoObject['objet_nom'].'</th></tr>'
		.'<tr>'
		.'<td>'.AfficheIcone('attaque').' : '.$arInfoObject['objet_attaque'].'</td>'
		.'<td>'.AfficheIcone('defense').' : '.$arInfoObject['objet_defense'].'</td>'
		.'<td>'.AfficheIcone('distance').' : '.$arInfoObject['objet_distance'].'</td>'
		.'</tr>'
		.'</table>';
	}elseif(in_array($arInfoObject['objet_type'], array('sort'))){
		$txtInfo = NULL;
		$InfoBulle = '<table class="equipement">'
		.'<tr><th>'.$arInfoObject['objet_nom'].'</th></tr>'
		.'<tr>'
		.'<td>'.$arInfoObject['objet_description'].'</td>'
		.'</tr>'
		.'</table>';
	}else{
		$txtInfo = '
		<td colspan="6">'.AfficheIcone($IconeName).' : '.$_SESSION['main'][$id]['value'].'</td>';
		$InfoBulle = '<table class="equipement">'
		.'<tr><th>'.$arInfoObject['objet_nom'].'</th></tr>'
		.'<tr>'
		.'<td>'.$arInfoObject['objet_description'].'</td>'
		.'</tr>'
		.'</table>';
	}

	//Le check si on est sur un entrepot ou pas
	if(is_null(FoundBatiment(4, NULL, implode(',', array_merge(array($oJoueur->GetCarte()), $oJoueur->GetPosition()))))){
		//on est pas sur l'entrepot
		$checkEntrepot = false;
	}else{
		//on est sur l'entrepot
		$checkEntrepot = true;
	}

	//Les bouttons de ventes
	if($oJoueur->CheckSiObjetPeutEtreGroupe($CodeObject, $arInfoObject['objet_type'])){
		$txtButton = null;
		foreach(array(1, 10, 100) as $StepVente){
			if($nbObjet >= $StepVente){
				if($checkEntrepot){
					$InfoBulleBtV = '<table class="InfoBulle"><tr><td>Entreposer '.$StepVente.'x '.$arInfoObject['objet_nom'].'</td></tr></table>';
				}else{
					$InfoBulleBtV = '<table class="InfoBulle"><tr><td>Vendre '.$StepVente.'x '.$arInfoObject['objet_nom'].' pour le prix de '.($StepVente * $arInfoObject['objet_prix']).' '.AfficheIcone('or', 15).'</td></tr></table>';
				}
				$txtButton .= '
			<button type="button" class="inventaire" '
				.'onclick="window.location=\'index.php?page=inventaire&amp;action='.($checkEntrepot?'entreposer':'vendre').'&amp;id='.$id.'&amp;qte='.$StepVente.'\'" '
				.'onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulleBtV).'\');" '
				.'onmouseout="cache();" '
				.'>'
				.($checkEntrepot?$StepVente.'x Entreposer':$StepVente.'x Vendre')
				.'</button>'
				.'<br />';
			}
		}
	}else{
		if($checkEntrepot){
			$InfoBulleBtV = '<table class="InfoBulle"><tr><td>Entreposer 1x '.$arInfoObject['objet_nom'].'</td></tr></table>';
		}else{
			$InfoBulleBtV = '<table class="InfoBulle"><tr><td>Vendre 1x '.$arInfoObject['objet_nom'].' pour le prix de '.$arInfoObject['objet_prix'].' '.AfficheIcone('or', 15).'</td></tr></table>';
		}
		$txtButton = '
		<button type="button" class="inventaire" '
		.'onclick="window.location=\'index.php?page=inventaire&amp;action='.($checkEntrepot?'entreposer':'vendre').'&amp;id='.$id.'&amp;qte=1\'" '
		.'onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulleBtV).'\');" '
		.'onmouseout="cache();" '
		.'>'
		.($checkEntrepot?'Entreposer':'Vendre')
		.'</button>';
	}

	$InfoBulleBtJ = '<table class="InfoBulle"><tr><td>'.AfficheIcone('attention').'Si vous libérez cet emplacement, il vous sera impossible de récupérer les '.($nbObjet > 1?'<b>'.$nbObjet.'x </b>':'').$arInfoObject['objet_nom'].'</td></tr></table>';

	$txt =  '
<table width="100%">
	<tr style="background:lightgrey; line-height:5px;"><td colspan="8">&nbsp;</td></tr>
	<tr>
		<td rowspan="4" style="width:80px;">'.AfficheInfoObjet($CodeObject, $reSizeImg).'</td>
		<td colspan="6" class="tdtitre">'.($nbObjet > 1?'<b>'.$nbObjet.'x </b>':'').$arInfoObject['objet_nom'].'</td>
	</tr>
	<tr>'
	.$txtInfo
	.'</tr>
	<tr>
		<td colspan="6">'.AfficheIcone('or').' : '.$arInfoObject['objet_prix'].'</td>
	</tr>
	<tr>
		<td colspan="6">'
	.(!is_null($txtType)?
	$txtType.'<br />'
	:'')
	.$txtButton
	.'<button type="button" class="inventaire" '
	.'onclick="window.location=\'index.php?page=inventaire&amp;action=laisser&amp;id='.$id.'\'"'
	.'onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulleBtJ).'\');" '
	.'onmouseout="cache();" '
	.'>'
	.'Libérer'
	.'</button>'
	.'</td>
	</tr>
</table>';
	return $txt;
}

//+---------------------------------+
//|				ACTIONS				|
//+---------------------------------+
function ActionVendre(&$check, $id, &$oJoueur, &$objManager){
	if(isset($_SESSION['main'][$id]['code'])){

		$sql = "SELECT contenu_vendeur FROM table_marcher WHERE type_vendeur='marchant'";
		$requete = mysql_query($sql) or die (mysql_error());

		$objMarcher = new marchant(mysql_fetch_array($requete, MYSQL_ASSOC));

		if($oJoueur->GetCombienElementDansBolga($_SESSION['main'][$id]['code']) == 0){
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionVendre : Pas assez d\'éléments';
		}elseif (abs($_GET['qte']) > $oJoueur->GetCombienElementDansBolga($_SESSION['main'][$id]['code'])){
			$_GET['qte'] = $oJoueur->GetCombienElementDansBolga($_SESSION['main'][$id]['code']);
		}

		if($check){
			for ($i = 1; $i <= $_GET['qte']; $i++) {
				$oJoueur->VendreObjet($_SESSION['main'][$id]['code'], $_SESSION['main'][$id]['prix']);
				$objMarcher->AddMarchandise($_SESSION['main'][$id]['code']);
			}
				
			$objManager->UpdateMarcher($objMarcher);
				
		}

		unset($_SESSION['main'][$id]['code']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionVendre';
	}
}
function ActionEntreposer(&$check, $objManager, $id, &$oJoueur){
	if(isset($_SESSION['main'][$id]['code'])){

		$entrepot = FoundBatiment(4);

		if(!is_null($entrepot)){
				
			if($oJoueur->GetCombienElementDansBolga($_SESSION['main'][$id]['code']) == 0){
				$check = false;
				echo 'Erreur GLX0004: Fonction ActionEntreposer : Pas assez d\'éléments';
			}elseif (abs($_GET['qte']) > $oJoueur->GetCombienElementDansBolga($_SESSION['main'][$id]['code'])){
				$_GET['qte'] = $oJoueur->GetCombienElementDansBolga($_SESSION['main'][$id]['code']);
			}
				
			if($check){
				for ($i = 1; $i <= $_GET['qte']; $i++) {
						
					$oJoueur->CleanInventaire($_SESSION['main'][$id]['code']);
					$entrepot->AddContenu($_SESSION['main'][$id]['code']);

				}
					
				$objManager->UpdateBatiment($entrepot);
					
			}

		}else{
				
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionEntreposer - Entrepot Introuvable';

		}
		unset($_SESSION['main'][$id]['code']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionEntreposer';
	}
}
function ActionEquiper(&$check, &$arInfoObject, &$oJoueur){
	if(!is_null($arInfoObject['code'])){
		if($arInfoObject['action']){
			$oJoueur->AddInventaire($arInfoObject['code'], $arInfoObject['type']);
		}
		$oJoueur->EquiperPerso($arInfoObject['code'], $arInfoObject['type']);
		$arInfoObject['code'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionEquiper';
	}
}
function ActionSorts(&$check, &$oJoueur){
	switch($_SESSION['main'][$_GET['id']]['code']){
		case 'SrtMaison':
			$oJoueur->UpdatePosition($oJoueur->GetMaisonInstalle());
			$oJoueur->CleanInventaire($_SESSION['main'][$_GET['id']]['code']);
			break;
		case 'SrtDefense10':
		case 'SrtAttaque10':
		case 'SrtDistance1':
			$oJoueur->EquiperPerso($_SESSION['main'][$_GET['id']]['code'], 'sort');
			break;
		case 'LvrDruides':
			$oJoueur->EquiperPerso($_SESSION['main'][$_GET['id']]['code'], 'livre');
			break;
		case 'SrtQuete':
			$sqlQ = "SELECT quete_position FROM table_quetes WHERE  quete_login='".$oJoueur->GetLogin()."' AND quete_reussi IS NULL;";
			$rqtLstQuete = mysql_query($sqlQ) or die (mysql_error().'<br />'.$sqlQ);
			if(mysql_num_rows($rqtLstQuete) == 0){
				$txtMessage = '<p>Vous n\'avez aucune quête en cours.</p>';
			}else{
				$txtMessage = null;
				$numQuete = 1;
				while($Quete = mysql_fetch_array($rqtLstQuete, MYSQL_ASSOC)){
					$arPosQuete = explode(',', $Quete['quete_position']);
					$txtMessage .= 'quête #'.$numQuete.' >>> (Carte '.ucfirst($arPosQuete['0']).', Ligne '.$arPosQuete['1'].', Colonne '.$arPosQuete['2'].')<br />';
					$numQuete++;
				}
			}
			$_SESSION['message'][] = $txtMessage;
			AddHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'Sort', 'Votre Druide', NULL, $txtMessage);
			//on supprime le sort du bolga
			$oJoueur->CleanInventaire($_SESSION['main'][$_GET['id']]['code']);
			break;
		default:
			$check = false;
			echo 'Erreur GLX0003: Pas d\'action correcte <br />';
			print_r($_SESSION['main']);
	}
}
?>