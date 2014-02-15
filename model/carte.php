<?php


function AffichageJoueurSurGrille(DBManage &$db, &$grille, $numCarte){
	//$sql="SELECT vie, position, login, civilisation FROM table_joueurs;";
	$requete = $db->Select('table_joueurs', array('vie', 'position', 'login', 'civilisation'));
	
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		
		$position = explode(',', $row['position']);
		
		if($numCarte == $position[0]){
			if(empty($grille[intval($position[1])][intval($position[2])])){
				
				$grille[intval($position[1])][intval($position[2])]['login'] = $row['login'];
				$grille[intval($position[1])][intval($position[2])]['vie'] = $row['vie'];
				$grille[intval($position[1])][intval($position[2])]['civilisation'] = $row['civilisation'];
				
			}elseif($row['login'] == $_SESSION['joueur']){
				
				$grille[intval($position[1])][intval($position[2])]['login'] = $row['login'];
				$grille[intval($position[1])][intval($position[2])]['vie'] = $row['vie'];
				$grille[intval($position[1])][intval($position[2])]['civilisation'] = $row['civilisation'];
				
			}
		}
	}
}
function AffichageQueteSurGrille(&$grille, $numCarte, $AllCartes){
	foreach($_SESSION['QueteEnCours'] as $Quete){
		
		if($numCarte == $Quete->GetCarte() AND $Quete->GetVisibilite()){
			
			$arPosition = $Quete->GetPosition();
			$InfoBulle = '<b>'.$Quete->GetNom().'</b>';
			$grille[intval($arPosition[0])][intval($arPosition[1])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" onmouseout="cache();" style="background: url(\'./img/quetes/icones/'.($AllCartes?'mini/':'').$Quete->GetImgNom().'.png\') no-repeat center;"';
			
		}
	}
}
function AffichageBatimentSurGrille(DBManage &$db, &$grille, $numCarte, $AllCartes){
	global $lstNonBatiment, $lstBatimentsNonConstructible;
	
	$sql="SELECT login, coordonnee, id_type_batiment FROM table_carte WHERE id_type_batiment NOT IN (".implode(", ", array_merge($lstNonBatiment, $lstBatimentsNonConstructible)).") AND detruit IS NULL;";
	$requete = $db->Query($sql);
	
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		
		$position = explode(',', $row['coordonnee']);
		
		if($numCarte == $position['0']){
			
			$objBatiment = FoundBatiment($row['id_type_batiment'], $row['login'], $row['coordonnee']);

			if(!is_null($objBatiment)){
				
					//on ajoute l'infobulle � l'icone du batiment
				$grille[intval($position[1])][intval($position[2])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($objBatiment->GetInfoBulle($AllCartes)).'\');" onmouseout="cache();"';
				
					//on ajout l'icone du batiment
				$grille[intval($position[1])][intval($position[2])]['batiment'] .= ' style="background: url(\'./img/carte/'.($AllCartes?'mini/':'').$objBatiment->GetImgName().'.png\') no-repeat center;"';
				
			}
		}
	}
}
function AffichageRessourceSurGrille(DBManage &$db, &$grille, $numCarte, $AllCartes){
	global $lstBatimentsNonConstructible;
	
	$sql="SELECT login, coordonnee, id_type_batiment FROM table_carte WHERE id_type_batiment IN (".implode(', ', $lstBatimentsNonConstructible).") AND detruit IS NULL;";
	$requete = $db->Query($sql);
	
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
	
		$position = explode(',', $row['coordonnee']);
	
		if($numCarte == $position['0']){
				
			//$objBatiment = FoundBatiment($row['id_type_batiment'], $row['login'], $row['coordonnee']);
			$objRessource = FoundBatiment(NULL, NULL, $row['coordonnee']);

			if(!is_null($objRessource)){
	
				//on ajoute l'infobulle � l'icone du batiment
				$grille[intval($position[1])][intval($position[2])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($objRessource->GetInfoBulle($AllCartes)).'\');" onmouseout="cache();"';
	
				//on ajout l'icone du batiment
				$grille[intval($position[1])][intval($position[2])]['batiment'] .= ' style="background: url(\'./img/carte/'.($AllCartes?'mini/':'').$objRessource->GetImgName().'.png\') no-repeat center;"';
	
			}
		}
	}
}
?>