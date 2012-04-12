<?php
function AfficheCarte($numCarte, $AllCartes = false){
	global $nbLigneCarte, $nbColonneCarte;

	$txt = null;

	//On ajoute les joueurs
	AffichageJoueurSurGrille($grille, $numCarte);
	
	//On ajoute les quetes sur la carte.
	if(isset($_SESSION['QueteEnCours'])){
		AffichageQueteSurGrille($grille, $numCarte, $AllCartes);
	}
	
	//on cache les quetes par les batiments
	//on ajoute les infos des batiments
	AffichageBatimentSurGrille($grille, $numCarte, $AllCartes);

	//on crée la table carte pour son affichage
	if($AllCartes){
		$txt .= '<table class="carte_petite" onmouseover="montre(\''.CorrectDataInfoBulle('<b>Zone '.strtoupper($numCarte).'</b>').'\');" onmouseout="cache();">';
		$size = 8;
	}else{
		$txt .= '<table class="carte" style="background-image: url(\'img/carte/gaule-'.$numCarte.'.jpg\');">';
		$size = 30;
	}
	
	for($i=0;$i<=$nbLigneCarte;$i++){
		$txt .= '
				<tr>
					';
		for($j=0;$j<=$nbColonneCarte;$j++){
			$txt .= '<td'.(isset($grille[$i][$j]['batiment'])?$grille[$i][$j]['batiment']:'').'>';
			if(isset($grille[$i][$j]['login'])){
				$txt .= '<img alt="Perso '.$grille[$i][$j]['login'].'" 
							src="./img/carte/'.$grille[$i][$j]['civilisation'].'-'.($_SESSION['joueur']==$grille[$i][$j]['login']?'a':'b').'.png" 
							height="'.$size.'px" 
							width="'.$size.'px" 
							onmouseover="montre(\''.
								CorrectDataInfoBulle(
									'<table><tr>'
									.($AllCartes?
										'<td rowspan="2">'
										.'<img alt="Perso '.$grille[$i][$j]['login'].'" src="./img/carte/'.$grille[$i][$j]['civilisation'].'-'.($_SESSION['joueur']==$grille[$i][$j]['login']?'a':'b').'.png" height="30px" width="30px" />'
										.'</td>'
										:'')
									.'<th>'.$grille[$i][$j]['login'].'</th></tr>'
									.'<tr><td><img alt="Barre de Vie" src="./fct/fct_image.php?type=VieCarte&amp;value='.$grille[$i][$j]['vie'].'&amp;max='.personnage::VIE_MAX.'" /></td></tr>'
									.'</table>'
									)
							.'\');" 
							onmouseout="cache();" />';
			}
			$txt .= '</td>';
		}
		$txt .= '
				</tr>';
	}
	$txt .= '
			</table>';

	return $txt;
}

function AffichageJoueurSurGrille(&$grille, $numCarte){
	$sql="SELECT vie, position, login, civilisation FROM table_joueurs;";
	$requete = mysql_query($sql) or die (mysql_error());
	
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
		
		if($numCarte == $Quete->GetCarte() AND in_array($Quete->GetTypeQuete(), array('romains'))){
			
			$arPosition = $Quete->GetPosition();
			$InfoBulle = '<b>'.$Quete->GetNom().'</b>';
			$grille[intval($arPosition[0])][intval($arPosition[1])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" onmouseout="cache();" style="background: url(\'./img/quetes/icones/'.($AllCartes?'mini/':'').$Quete->GetTypeQuete().'.png\') no-repeat center;"';
			
		}
	}
}
function AffichageBatimentSurGrille(&$grille, $numCarte, $AllCartes){
	global $lstNonBatiment;
	$sql="SELECT login, coordonnee, id_type_batiment FROM table_carte WHERE detruit IS NULL;";
	//$sql="SELECT * FROM table_carte WHERE detruit IS NULL;";
	$requete = mysql_query($sql) or die (mysql_error().$sql);
	
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		
		$position = explode(',', $row['coordonnee']);
		
		if($numCarte == $position['0']){
			
			//$sqlb = "SELECT batiment_type, batiment_nom, batiment_description, batiment_vie FROM table_batiment WHERE id_batiment=".$row['id_type_batiment'].";";
			//$requeteb = mysql_query($sqlb) or die (mysql_error().$sqlb);
			//$batiment = mysql_fetch_array($requeteb, MYSQL_ASSOC);
			
			$objBatiment = FoundBatiment($row['id_type_batiment'], $row['login'], $row['coordonnee']);
			
			//if(!in_array($row['id_type_batiment'], $lstNonBatiment)){
			//if(!in_array(get_class($objBatiment), array('ressource', 'carte'))){
			if(!is_null($objBatiment)){
				
				
					//on crée les infos de l'infoBulle
				/* $InfoBulle = '<table><tr>'
								.($AllCartes?
									'<td rowspan="2">'
										.'<img src="./img/carte/'.$objBatiment->GetImgName().'.png" alt="'.$objBatiment->GetNom().'" title="'.$objBatiment->GetNom().'" />'
									.'</td>'
									:'')
								.'<th>'.$objBatiment->GetNom().(!is_null($objBatiment->GetLogin())?' de '.$objBatiment->GetLogin():'').'</th></tr>'
								.'<tr><td><img alt="'.$objBatiment->GetNom().'" src="./fct/fct_image.php?type=etatcarte&amp;value='.$objBatiment->GetEtat().'&amp;max='.$objBatiment->GetEtatMax().'" /></td></tr>'
							.'</table>'; */
					//on ajoute l'infobulle à l'icone du batiment
				$grille[intval($position[1])][intval($position[2])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($objBatiment->GetInfoBulle($AllCartes)).'\');" onmouseout="cache();"';
				
					//on ajout l'icone du batiment
				$grille[intval($position[1])][intval($position[2])]['batiment'] .= ' style="background: url(\'./img/carte/'.($AllCartes?'mini/':'').$objBatiment->GetImgName().'.png\') no-repeat center;"';
				
			}else{
				
			}
		}
	}
}
function AffichageRessourceSurGrille(){
	
}
?>