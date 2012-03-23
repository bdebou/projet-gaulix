<?php
function AfficheCarte($numCarte, $AllCartes = false){
	global $nbLigneCarte, $nbColonneCarte;

	$txt = null;

	$sql="SELECT vie, position, login FROM table_joueurs;";
	$requete = mysql_query($sql) or die (mysql_error());
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		$position = explode(',', $row['position']);
		if($numCarte == $position[0]){
			if(empty($grille[intval($position[1])][intval($position[2])])){
				$grille[intval($position[1])][intval($position[2])]['login'] = $row['login'];
				$grille[intval($position[1])][intval($position[2])]['vie'] = $row['vie'];
			}elseif($row['login'] == $_SESSION['joueur']){
				$grille[intval($position[1])][intval($position[2])]['login'] = $row['login'];
				$grille[intval($position[1])][intval($position[2])]['vie'] = $row['vie'];
			}
		}
	}
	//On ajoute les quetes sur la carte.
	if(isset($_SESSION['QueteEnCours'])){
		foreach($_SESSION['QueteEnCours'] as $Quete){
			if($numCarte == $Quete->GetCarte() AND in_array($Quete->GetTypeQuete(), array('romains'))){
				$arPosition = $Quete->GetPosition();
				$InfoBulle = '<b>'.$Quete->GetNom().'</b>';
				$grille[intval($arPosition[0])][intval($arPosition[1])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" onmouseout="cache();" style="background: url(\'./img/'.($AllCartes?'mini/':'').$Quete->GetTypeQuete().'.png\') no-repeat center;"';
			}
		}
	}
	//on cache les quetes par les batiments
	//on ajoute les infos des batiments
	$sql="SELECT * FROM table_carte WHERE detruit IS NULL;";
	$requete = mysql_query($sql) or die (mysql_error().$sql);
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		$position = explode(',', $row['coordonnee']);
		if($numCarte == $position['0']){
			$sqlb = "SELECT batiment_type, batiment_nom, batiment_description, batiment_vie FROM table_batiment WHERE id_batiment=".$row['id_type_batiment'].";";
			$requeteb = mysql_query($sqlb) or die (mysql_error().$sqlb);
			$batiment = mysql_fetch_array($requeteb, MYSQL_ASSOC);

			if(!in_array($batiment['batiment_type'], array('ressource', 'carte'))){
				$InfoBulle =
					'<b>'.$batiment['batiment_nom'].' de '.$row['login'].'</b>'
				.'<br />'
				.'<img alt="'.$batiment['batiment_nom'].'" src="./fct/fct_image.php?type=etatcarte&amp;value='.$row['etat_batiment'].'&amp;max='.($batiment['batiment_vie'] + (50 * $row['niveau_batiment'])).'" />'
				.(($row['login'] == 'romain' AND in_array($row['id_type_batiment'], array(4, 5)))?
						'<br />Contenu : '
				.($row['id_type_batiment'] == 5?
				$row['contenu_batiment'].'x '.AfficheIcone('or')
				:'<b>Plusieurs objets</b>')
				:'');
				$grille[intval($position[1])][intval($position[2])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" onmouseout="cache();" style="background: url(\'./img/'.($AllCartes?'mini/':'').$batiment['batiment_type'].'-';
				if($row['login']==$_SESSION['joueur']){
					$grille[intval($position[1])][intval($position[2])]['batiment'] .= 'a';
				}else{
					$grille[intval($position[1])][intval($position[2])]['batiment'] .= 'b';
				}
			}else{
				switch($batiment['batiment_type']){
					case 'ressource':
                        $InfoBulle = '<b>' . $batiment['batiment_description'] . '</b><br /><img alt="Etat Ressource" src="fct/fct_image.php?type=etatcarte&amp;value=' . $row[strval('res_' . $batiment['batiment_nom'])] . '&amp;max=5000" />';
                        break;
                    case 'carte':
                        $InfoBulle = $batiment['batiment_description'];
                        break;
				}
				$grille[intval($position[1])][intval($position[2])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" onmouseout="cache();" style="background: url(\'./img/'.($AllCartes?'mini/':'').$batiment['batiment_nom'].'';
			}
			$grille[intval($position[1])][intval($position[2])]['batiment'] .= '.png\') no-repeat center;"';
		}
	}

	if($AllCartes){
		$txt .= '<table class="carte_petite" onmouseover="montre(\''.CorrectDataInfoBulle('<b>Carte '.strtoupper($numCarte).'</b>').'\');" onmouseout="cache();">';
		$size = 8;
	}else{
		$txt .= '<table class="carte">';
		$size = 30;
	}
	
	for($i=0;$i<=$nbLigneCarte;$i++){
		$txt .= '
				<tr>
					';
		for($j=0;$j<=$nbColonneCarte;$j++){
			$txt .= '<td'.(isset($grille[$i][$j]['batiment'])?$grille[$i][$j]['batiment']:'').'>';
			if(isset($grille[$i][$j]['login'])){
				$txt .= '<img alt="Perso '.$grille[$i][$j]['login'].'" src="./img/homme-'.($_SESSION['joueur']==$grille[$i][$j]['login']?'green':'grey').'.png" height="'.$size.'px" width="'.$size.'px" onmouseover="montre(\''.CorrectDataInfoBulle('<b>'.$grille[$i][$j]['login'].'</b><br /><img alt="Barre de Vie" src="./fct/fct_image.php?type=VieCarte&amp;value='.$grille[$i][$j]['vie'].'&amp;max='.personnage::VIE_MAX.'" />').'\');" onmouseout="cache();" />';
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
?>