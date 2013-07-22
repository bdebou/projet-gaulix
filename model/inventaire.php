<?php
function CreateListObjet($Bolga){
	$lst = null;
	foreach($Bolga as $InfoObjet)
	{
		$arInfoObjet = explode('=', $InfoObjet);
		
		$objObjet = FoundObjet($arInfoObjet[0], $arInfoObjet[1]);
		
		if(!is_null($objObjet))
		{
			$lst[get_class($objObjet)][] = $objObjet;
		}
	}
	
	return $lst;
}
function AfficheRessource($type, personnage &$oJoueur, maison &$maison){
	
	$txtBt = NULL;
	
	if(!is_null($maison)){
		if ($maison->GetRessource($type) > 500) {

			switch ($type){
				case maison::TYPE_RES_NOURRITURE:
					$qte = 50;
					break;
				case maison::TYPE_RES_EAU_POTABLE:
					$qte = 25;
					break;
			}
		
			$_SESSION['inventaire'][$type] = $qte;
				
			$InfoBulle = '<table class="equipement"><tr><td>Vendre ' . $qte . 'pts ' . AfficheIcone($type, 15) . ' pour '.$qte.AfficheIcone(personnage::TYPE_RES_MONNAIE, 15).'</td></tr></table>';
			$txtBt = '	<form method="post" action="index.php?page=inventaire" style="display:inline;">
							<input type="hidden" name="action" value="Vendre" />
							<input type="hidden" name="id" value="'.$type.'" />
							<input type="hidden" name="qte" value="'.$qte.'" />'
							.(isset($_GET['page'])?'<input type="hidden" name="retour" value="'.$_GET['page'].'" />':NULL)
							.'<input type="submit" 
								name="submit" 
								value="-'.$qte.'x" 
								onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');"
								onmouseout="cache();" 
								 />
						</form>';
		}
	}

	return AfficheIcone($type) . ' : ' . $maison->GetRessource($type) . $txtBt;
}
/**
 * Retourne la string pour afficher ou non le bouton "Equiper"
 * @param personnage $oJoueur
 * @param objArmement $oObjet
 * @return string
 */
function AfficheBtEquiper(personnage &$oJoueur, objArmement &$oObjet){
	$txtBtEquiper = '<input type="submit" name="action" value="Equiper" />';
	
	if(!is_null($oObjet->GetCompetence()))
	{
		if($oJoueur->CheckIfCompetenceAvailable($oObjet->GetCompetence()))
		{
			if($oJoueur->CheckCompetence($oObjet->GetCompetence()))
			{
				return $txtBtEquiper;
			}else{
				return '<span style="color:red;">Compétence "' . GetInfoCompetence($oObjet->GetCompetence(), 'cmp_lst_nom') . '"</span>';
			}
		}else{
			return 'Vous ne pourrez jamais vous équiper de cet objet.';
		}
	}
	
	return $txtBtEquiper;
}

function AfficheListObjets(personnage &$oJoueur, $lstTypeObjets){
	if(!is_null($oJoueur->GetLstInventaire()))
	{
		$lstObjetParCategory = CreateListObjet($oJoueur->GetLstInventaire());
	}else{
		$lstObjetParCategory = NULL;
	}
	
	$id = 0;
	$txt = NULL;
	
	foreach($lstTypeObjets as $Category)
	{
		$txt .= '<div class="'.$Category.'">
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
					case 'Divers':
					case 'Ressource':
						break;
					case 'Armement':
						$txtInfoArmement = $objObjet->AfficheInfoTd(NULL, true);
						$nbColonne = 3;
						$nbLigne = 5;
						$nbLigne++;
						$txtEquiper = AfficheBtEquiper($oJoueur, $objObjet);
						break;
				}
					
				$txt .= '<form class="inventaire" action="index.php?page=inventaire" formmethod="post" method="post">'
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
			$txt .= 'Aucun objet de cette catégorie.';
		}
		$txt .= '</div>';
	}
	
	return $txt;
}
//+---------------------------------+
//|				ACTIONS				|
//+---------------------------------+

function ActionVendre(&$check, $id, personnage &$oJoueur, maison &$maison, $qte){
	if(isset($_SESSION['inventaire'][$id]['code'])){
		
		$objObjet = FoundObjet($_SESSION['inventaire'][$id]['code']);

		if($oJoueur->GetCombienElementDansBolga($objObjet->GetCode()) <= 0)
		{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionVendre : Pas assez d\'éléments';
			
		}elseif ($qte > $oJoueur->GetCombienElementDansBolga($objObjet->GetCode()))
		{
			$_qte = $oJoueur->GetCombienElementDansBolga($objObjet->GetCode());
		}

		if($check){
			for ($i = 1; $i <= $qte; $i++) {
				$oJoueur->VendreObjet($objObjet->GetCode(), $objObjet->GetPrix());
				//$objMarche->AddMarchandise($_SESSION['inventaire'][$id]['code']);
			}
				
			//$objManager->UpdateMarche($objMarche);
				
		}

		unset($_SESSION['inventaire']);
	}elseif(in_array($id, array(maison::TYPE_RES_NOURRITURE, maison::TYPE_RES_EAU_POTABLE)))
	{
				
		$maison->MindRessource($id, $qte);
		$oJoueur->AddOr($qte);
		
		unset($_SESSION['inventaire']);
		
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
function ActionEquiper(&$check, $id, &$oJoueur){
	if(isset($_SESSION['inventaire'][$id]['code'])){
		
		$objObjet = FoundObjet($_SESSION['inventaire'][$id]['code']);
		
		$oJoueur->EquiperPerso($objObjet->GetCode(), $objObjet->GetType());
		unset($_SESSION['inventaire'][$id]['code']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionEquiper';
	}
}
function ActionSorts(&$check, personnage &$oJoueur){
	switch($_SESSION['main'][$_GET['id']]['code']){
		case 'SrtMaison':
			$oJoueur->UpdatePosition($oJoueur->GetMaisonInstalle());
			$oJoueur->CleanInventaire($_SESSION['main'][$_GET['id']]['code']);
			break;
		case 'SrtDefense10':
		case 'SrtAttaque10':
		case 'SrtDistance1':
			$oJoueur->EquiperPerso($_SESSION['main'][$_GET['id']]['code'], objDivers::TYPE_SORT);
			break;
		case 'LvrDruides':
			$oJoueur->EquiperPerso($_SESSION['main'][$_GET['id']]['code'], objDivers::TYPE_LIVRE);
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
function ActionConvertir(&$check, $id, personnage &$oJoueur, maison &$maison, $Qte){
	if(isset($_SESSION['inventaire'][$id]['code'])){

		//on crée l'objet Objet
		$objObjet = FoundObjet($_SESSION['inventaire'][$id]['code']);
		
		for($i=1; $i <= $Qte; $i++)
		{
			foreach($objObjet->GetRessource() as $Ressource)
			{
				$arRessource = explode('=', $Ressource);
				
				switch(QuelTypeObjet($arRessource[0])){
					case maison::TYPE_RES_EAU_POTABLE:
					case maison::TYPE_RES_NOURRITURE:
						if(!is_null($maison)){
							$maison->AddRessource(QuelTypeObjet($arRessource[0]), $arRessource[1]);
						}
						break;
					default:
						$oJoueur->AddInventaire($arRessource[0], $arRessource[1], false);
					break;
				}
			}
			
			//on enlève un objet de l'inventaire
			$oJoueur->CleanInventaire($objObjet->GetCode());
		}
				
		unset($_SESSION['inventaire'][$id]['code']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionConvertir';
	}
}
function ActionAbandonner(&$check, personnage &$oJoueur, $id, $qte){
	if(isset($_SESSION['inventaire'][$id]['code'])){
		for($i=1; $i <= $qte; $i++)
		{
			$oJoueur->CleanInventaire($_SESSION['inventaire'][$id]['code']);
		}
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionAbandonner';
	}
}

?>