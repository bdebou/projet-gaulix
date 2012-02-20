<?php
function ReglesAfficheTableauBatiment(){
	global $arCouleurs;
	$txt = '
<table class="village">';
	$sql="SELECT * FROM table_batiment;";
	$requete = mysql_query($sql) or die (mysql_error());
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		if(in_array($row['batiment_type'], array('maison', 'bank', 'entrepot', 'tour', 'mur', 'ferme', 'marcher', 'mine'))){
			$txt .= '
	<tr>
	
		<td rowspan="6">
		<img src="./img/batiments/'.$row['batiment_type'].'-0.png" alt="Bâtiment ('.$row['batiment_type'].')" width="300px" />
		</td>
		<th colspan="4">'.$row['batiment_nom'].' <img src="./img/'.$row['batiment_type'].'-a.png" alt="Icone bâtiment ('.$row['batiment_type'].')" /></th>
	</tr>
	<tr>
		<th colspan="4">Prix</th>
	</tr>
	<tr>
		<td style="background-color:#'.$arCouleurs['Or'].';">'.AfficheIcone('or').' : '.$row['prix_or'].'</td>
		<td style="background-color:#'.$arCouleurs['Bois'].';">'.AfficheIcone('bois').' : '.$row['prix_bois'].'</td>
		<td style="background-color:#'.$arCouleurs['Pierre'].';">'.AfficheIcone('pierre').' : '.$row['prix_pierre'].'</td>
		<td style="background-color:#'.$arCouleurs['Nourriture'].';">'.AfficheIcone('nourriture').' : '.$row['prix_nourriture'].'</td>
	</tr>
	<tr>
		<th colspan="4">Combat</th>
	</tr>
	<tr>
		<td style="background-color:#'.$arCouleurs['Attaque'].';">'.AfficheIcone('attaque').': '.(is_null($row['batiment_attaque'])?'0':$row['batiment_attaque']).'</td>
		<td style="background-color:#'.$arCouleurs['Attaque'].';">'.AfficheIcone('distance').': '.(is_null($row['batiment_distance'])?'0':$row['batiment_distance']).'</td>
		<td style="background-color:#'.$arCouleurs['Defense'].';">'.AfficheIcone('defense').': '.(is_null($row['batiment_defense'])?'0':$row['batiment_defense']).'</td>
		<td style="background-color:#'.$arCouleurs['Vie'].';">'.AfficheIcone('vie').': '.$row['batiment_vie'].'</td>
	</tr>
	<tr>
		<td colspan="6" style="text-align:left;">'.$row['batiment_description'].'</td>
	</tr>
	<tr style="background:lightgrey;">
		<td colspan="5">&nbsp;</td>
	</tr>';
		}
	}
	$txt .= '
</table>
';
	return $txt;
}
function ReglesAfficheTableauEquipements($equipement){
	$bckColor='#c8c8c8';
	$txt=null;

	$sql="SELECT *
			FROM table_objets 
			WHERE objet_type='".$equipement."'"
	.($equipement != 'ressource'?' AND objet_quete IS NULL':'').";";

	$requete = mysql_query($sql) or die (mysql_error());
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		if($bckColor=='#c8c8c8'){
			$bckColor='white';
		}else{$bckColor='#c8c8c8';
		}
		if($equipement != 'ressource'){
			
			$InfoBulle = '<table class="equipement">'
			.'<tr>'
			.'<td>'.AfficheIcone('attaque').' : '.$row['objet_attaque'].'</td>'
			.($equipement=='arme'?'<td>'.AfficheIcone('distance').' : '.($row['objet_distance']!=0?$row['objet_distance']:'0'):'').'</td>'
			.'</tr>'
			.'<tr><td'.($equipement=='arme'?' colspan="2"':'').'>'.AfficheIcone('defense').' : '.$row['objet_defense'].'</td></tr>'
			.'<tr><td'.($equipement=='arme'?' colspan="2"':'').'>'.AfficheIcone('or').' : '.$row['objet_prix'].'</td></tr>'
			.'<tr><td'.($equipement=='arme'?' colspan="2"':'').'>Niveau requis : '.$row['objet_niveau'].'</td></tr>'
			.'</table>';
		}else{
			$InfoBulle = null;
		}

		$txt .= '
<table class="equipements">
	<tr style="background:'.$bckColor.';">
		<td >
			<img src="./img/objets/'.$row['objet_code'].'.png" 
				height="100px" '
		.'alt="'.$row['objet_nom'].'" '
		.(!is_null($InfoBulle)?'onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" onmouseout="cache();" ':'')
		.'/>
		</td>
	</tr>
	<tr style="background:'.$bckColor.';">
		<th style="text-align:left;">'.$row['objet_nom'].'</th>
	</tr>
</table>';
	}

	return $txt;
}
function AfficheTableauGainScores(){
	global $lstPoints;

	$txt = '<table class="points">
		<tr style="background:grey;"><th style="width:40px;">Pts</th><th style="width:300px;">Action</th></tr>';
	foreach($lstPoints as $arPts){
		$txt .= '<tr><td style="background:'.($arPts[0] > 0?'LightGreen':'LightSalmon').';">'.($arPts[0] > 0?'+'.$arPts[0]:$arPts[0]).'</td><td>'.$arPts[1].'</td></tr>';
	}
	$txt .= '</table>';

	return $txt;
}
function AfficheListeCompetences(){
	$NomPrecedent = null;
	$nbComp = 0;
	$NumCol = 0;
	$CheckA = false;
	$CheckB = false;
	$txt = '
	<table>';
	
	$sqlLstCmp = "SELECT * FROM table_competence_lst WHERE cmp_lst_type='competence' ORDER BY cmp_lst_nom, cmp_lst_niveau ASC;";
	$rqtLstCmp = mysql_query($sqlLstCmp) or die (mysql_error().'<br />'.$sqlLstCmp);
	
	while($cmp = mysql_fetch_array($rqtLstCmp, MYSQL_ASSOC)){
		if ($NumCol == 0){
			$txt .= '
		<tr style="vertical-align:top;">';
		}
		if ($cmp['cmp_lst_nom'] != $NomPrecedent){
			
			if($CheckA){
				$txt .= '
				</table>
			</td>';
				$CheckA = false;
				if($NumCol == 2){
					$txt .= '
		</tr>
		<tr style="vertical-align:top;">';
					$NumCol = 0;
				}
			}
			
			$txt .= '
			<td>
				<table class="regles_competences">
					<tr><th colspan="2">'.ucfirst($cmp['cmp_lst_nom']).'</th></tr>
					<tr>
						<td class="regles_competences_niveau">Niveau '.$cmp['cmp_lst_niveau'].'</td>
						<td>'.$cmp['cmp_lst_description'].'</td>
					</tr>';
			$CheckA = true;
			$NomPrecedent = $cmp['cmp_lst_nom'];
			$NumCol++;
			
		}else{
			$txt .= '
					<tr>
						<td>Niveau '.$cmp['cmp_lst_niveau'].'</td>
						<td>'.$cmp['cmp_lst_description'].'</td>
					</tr>';
		}
		
	}
	if($CheckA){
		$txt .= '
				</table>
			</td>
		</tr>';
	}
	
	$txt .= '	
	</table>';
	
	
	return $txt;
}
function NombreDeGaulois(){
	$sql = "SELECT id FROM table_joueurs;";
	$rqt = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	
	return mysql_num_rows($rqt);
}

?>