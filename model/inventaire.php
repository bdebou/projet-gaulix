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
function AfficheRessource($type, personnage &$oJoueur){
	$maison = $oJoueur->GetObjSaMaison();

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

//+---------------------------------+
//|				ACTIONS				|
//+---------------------------------+

function ActionVendre(&$check, $id, personnage &$oJoueur, &$objManager, $qte){
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
		$maison = $oJoueur->GetObjSaMaison();
		
		$maison->MindRessource($id, $qte);
		$oJoueur->AddOr($qte);
		
		$objManager->UpdateBatiment($maison);
		unset($maison, $_SESSION['inventaire']);
		
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
function ActionConvertir(&$check, $id, personnage &$oJoueur, &$objManager, $Qte){
	if(isset($_SESSION['inventaire'][$id]['code'])){

		//on trouve sa maison
		$maison = $oJoueur->GetObjSaMaison();
		
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
						$oJoueur->AddInventaire($arRessource[0], false, $arRessource[1]);
					break;
				}
			}
			
			//on enlève un objet de l'inventaire
			$oJoueur->CleanInventaire($objObjet->GetCode());
		}
		
		if(!is_null($maison)){
			$objManager->UpdateBatiment($maison);
			unset($maison);
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