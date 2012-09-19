<?php
function AfficheListeElementBricolage(personnage &$oJoueur, $Onglet = null){
	$txt = '
	<div class="systeme_onglets">';
	$TypePrecedent = null;
	$arOnglets['Contenu'] = '
		<div class="contenu_onglets">';
	$arOnglets['Span'] = '
		<div class="onglets">';
	$nbBricolage = 0;
	
	$maison = $oJoueur->GetObjSaMaison();
	
	$chkFirst = false;

	$sql = "SELECT objet_code 
			FROM table_objets 
			WHERE objet_quete IS NULL 
				AND objet_civilisation IN ('".$oJoueur->GetCivilisation()."', 'all')
				AND objet_cout NOT LIKE 'cmp%' 
			ORDER BY objet_type, objet_nom ASC;";
	
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		
		$oObjet = FoundObjet($row['objet_code']);
		
		//if($row['objet_type'] != $TypePrecedent){
		if(get_class($oObjet) != $TypePrecedent){
			$arOnglets['Span'] .= '
			<span 
				class="onglet_0 onglet" 
				id="onglet_'.get_class($oObjet).'" 
				onclick="javascript:change_onglet(\''.get_class($oObjet).'\');">'.ucfirst(substr(get_class($oObjet), 3)).'
			</span>
			';
			
			if($chkFirst){
				$arOnglets['Contenu'] .= '</div>';
			}else{
				$FirstOnglet = get_class($oObjet);
			}
			
			if(!is_null($Onglet) AND get_class($oObjet) == $Onglet){
				$FirstOnglet = get_class($oObjet);
			}
			
			$arOnglets['Contenu'] .= '<div class="contenu_onglet" id="contenu_onglet_'.get_class($oObjet).'">';
			
			$chkFirst = true;
			
			$TypePrecedent = get_class($oObjet);
			//$nbBricolage = 0;
		}
		
		$arOnglets['Contenu'] .= AfficheInfoObjetBricolage($oJoueur, $oObjet, $nbBricolage, $maison);
		//$arOnglets['Contenu'] .= var_dump($oObjet);
		$nbBricolage++;
	}
	
	$arOnglets['Contenu'] .= '
			</div>
		</div>';
	$arOnglets['Span'] .= '
		</div>';
	$txt .= $arOnglets['Span']
			.$arOnglets['Contenu']
	.'</div>
	<script type="text/javascript">
		//<!--
			var OldName = \''.$FirstOnglet.'\';
			change_onglet(OldName);
		//-->
	</script>';
	return $txt;
}
function AfficheInfoObjetBricolage(personnage &$oJoueur, &$oObjet, &$numObjet, maison &$maison){
	$txt 			= null;
	
	$ChkCompetence	= false;
	$chkDescription = false;
	
	$nbCol			= 2;
	$nbLigne		= 3;
	
	//Si l'objet a une description, on ajoute une ligne
	if(!is_null($oObjet->GetDescription()))
	{
		$chkDescription = true;
		$nbLigne++;
	}
	
	$txt = '<form class="bricolage" action="index.php?page=bricolage" formmethod="post" method="post">
		<table class="bricolage">
			<tr>
				<td rowspan="'.$nbLigne.'" style="width:120px; text-align:center;">'.$oObjet->AfficheInfoObjet(120).'</td>
				<th colspan="'.$nbCol.'"><a name="'.$oObjet->GetCode().'">'
					.$oObjet->GetNom().'</a> '.AfficheIcone($oObjet->GetCode()).' - Revente à '.$oObjet->GetPrix().' '.AfficheIcone(personnage::TYPE_RES_MONNAIE)
				.'</th>
			</tr>'
			.'<tr>
				<td style="width:210px;">Coût fabrication par unité :</td>
				<td colspan="'.($nbCol -1).'">'.AfficheListePrix($oObjet->GetCoutFabrication(), $oJoueur, $maison).'</td>
			</tr>'
			.($chkDescription?'<tr><td colspan="'.$nbCol.'">'.$oObjet->GetDescription().'</td></tr>':NULL)
			.'<tr>
				<td colspan="'.$nbCol.'" class="action_bricolage">
					<input style="width:150px;" id="Slider'.$oObjet->GetCode().'" type="range" min="1" max="100" step="1" value="1" onchange="printValue(\'Slider'.$oObjet->GetCode().'\',\'RangeValue'.$oObjet->GetCode().'\');"'.(CheckCout($oObjet->GetCoutFabrication(), $oJoueur, $maison)?NULL:'disabled="disabled"').' />
					<input style="width:50px;" name="qte" id="RangeValue'.$oObjet->GetCode().'" onchange="printValue(\'RangeValue'.$oObjet->GetCode().'\',\'Slider'.$oObjet->GetCode().'\');" type="number" min="1" max="100" step="1" value ="1" size="15"'.(CheckCout($oObjet->GetCoutFabrication(), $oJoueur, $maison)?NULL:'disabled="disabled"').' />
					<script>printValue(\'Slider'.$oObjet->GetCode().'\',\'RangeValue'.$oObjet->GetCode().'\');</script>
					<input type="submit" name="action" value="Fabriquer" '.(CheckCout($oObjet->GetCoutFabrication(), $oJoueur, $maison)?NULL:'disabled="disabled"').' />
				</td>
			</tr>
		</table>
	</form>';
		
	return $txt;
}
function ActionFabriquer(&$check, $id, personnage &$oJoueur, &$objManager){
	global $lstPoints;
	if(isset($_SESSION['main']['bricolage'][$id])){
		//on trouve la maison
		$maison = $oJoueur->GetObjSaMaison();

		if(!is_null($maison)){
			$LstPrix = explode(',', $_SESSION['main']['bricolage'][$id]['prix']);
				
			foreach($LstPrix as $Prix){
				$arPrix = explode('=', $Prix);
				//on vérifie si on a assez de ressource
				if(!CheckIfAssezRessource(array($arPrix[0], ($arPrix[1] * abs($_GET['qte']))), $oJoueur, $maison)){
					$check = false;
					break;
				}

				if(in_array(QuelTypeRessource($arPrix[0]), array(maison::TYPE_RES_EAU_POTABLE, maison::TYPE_RES_NOURRITURE, personnage::TYPE_RES_MONNAIE))){
					switch(QuelTypeRessource($arPrix[0]))
					{
						case maison::TYPE_RES_NOURRITURE:		$maison->MindRessource(maison::TYPE_RES_NOURRITURE, $arPrix[1] * abs($_GET['qte']));	break;
						case maison::TYPE_RES_EAU_POTABLE:		$maison->MindRessource(maison::TYPE_RES_EAU_POTABLE, $arPrix[1] * abs($_GET['qte']));		break;
						case personnage::TYPE_RES_MONNAIE:		$oJoueur->MindOr($arPrix[1] * abs($_GET['qte']));			break;
					}
						
				}else{
					for($i=1;$i<=($arPrix[1] * abs($_GET['qte']));$i++){
						$oJoueur->CleanInventaire($arPrix[0]);
					}
				}
			}
				
			$objManager->UpdateBatiment($maison);
			unset($maison);
				
			if(!$check){
				echo 'Erreur GLX0004: Fonction ActionFabriquer - Pas assez de ressource';
			}else{
				//on ajoute le nouvel objet dans son bolga
				$oJoueur->AddInventaire($_SESSION['main']['bricolage'][$id]['code'], abs($_GET['qte']), false);
				//on gagne des points
				$oJoueur->UpdatePoints(abs(personnage::POINT_OBJET_FABRIQUE));
			}

				
			unset($_SESSION['main']['bricolage']);
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionFabriquer - Pas de maison trouvée';
		}
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionFabriquer';
	}
}


?>