<?php
function CombienQueteDisponible($login, $niveau){
	$sql = "SELECT * FROM table_quete_lst WHERE niveau<=$niveau;";
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);

	if(mysql_num_rows($requete) > 0){
		$NbQueteDisponible = 0;

		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if(!CheckIfQueteEnCours($row['id_quete']) AND !CheckIfQueteDejaTermine($row['id_quete'], $login)){
				$NbQueteDisponible++;
			}
		}
		
		return $NbQueteDisponible;
	}

	return 0;
}
function CheckIfQueteEnCours($NumQuete){
	foreach($_SESSION['QueteEnCours'] as $quete){
		if($quete->GetIDTypeQuete() == $NumQuete){
			return true;
		}
	}
	return false;
}
function CheckIfQueteDejaTermine($NumQuete, $login){
	$sql = "SELECT id_quete_en_cours FROM table_quetes WHERE quete_login = '$login' AND quete_reussi IS NOT NULL AND quete_id = $NumQuete;";
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	
	if(mysql_num_rows($requete) > 0){
		return true;
	}
	return false;
}
function SelectQuete(personnage &$oJoueur){
	$txt = null;
	$sql = "SELECT * FROM table_quete_lst WHERE niveau<=".$oJoueur->GetNiveau().";";
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	
	if(mysql_num_rows($requete) > 0){
		$nbCol = 0;
		$NbQueteDisponible = 0;
		
		$txt .= '
		<table class="quetes">';
		
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if(!CheckIfQueteEnCours($row['id_quete']) AND !CheckIfQueteDejaTermine($row['id_quete'], $oJoueur->GetLogin())){
				$NbQueteDisponible++;
				if($nbCol == 0){
					$txt .= '
				<tr>';
				}
				
				$txt .= '
					<td>'.AfficheDescriptifQuete($row).'</td>';
					
				$nbCol++;
				
				if($nbCol == 3){
					$txt .= '
				</tr>';
					$nbCol=0;
				}
			}
		}
	
		if($nbCol < 3 AND $nbCol > 0){
			$txt .= '
			</tr>';
		}
		$txt .= '
		</table>';
	
		if($NbQueteDisponible > 0){
			return $txt;
		}else{
			return '<p>Vous êtes inscrit à toutes les quêtes disponibles pour le moment.</p>';
		}
	}else{
		return '<p>Pas de nouvelle quête disponible. Passez au niveau suivant pour avoir de nouvelles quêtes.</p>';
	}
}
function AfficheDescriptifQuete($quete){
	global $CodeCouleurQuete;

	$nbInfo=0;
	if(!is_null($quete['gain_or'])){
		$nbInfo++;
	}
	if(!is_null($quete['gain_experience'])){
		$nbInfo++;
	}
	if(!is_null($quete['gain_points'])){
		$nbInfo++;
	}

	return '
				<table class="fiche_quete">
					<tr style="background:'.$CodeCouleurQuete[$quete['quete_type']].';">
						<th colspan="'.$nbInfo.'">'.$quete['nom'].'</th>
					</tr>
					<tr><td colspan="'.$nbInfo.'">Gains</td></tr>
					<tr>'
	.(!is_null($quete['gain_or'])?'
						<td style="width:'.intval(100 / $nbInfo).'%">
							'.AfficheIcone('or').' : <b>'.$quete['gain_or'].'</b>
						</td>'
	:'')
	.(!is_null($quete['gain_experience'])?'
						<td style="width:'.intval(100 / $nbInfo).'%">
							Exp : <b>'.$quete['gain_experience'].'</b>
						</td>'
	:'')
	.(!is_null($quete['gain_points'])?'
						<td style="width:'.intval(100 / $nbInfo).'%">
							Points : <b>'.$quete['gain_points'].'</b>
						</td>'
	:'')
	.'</tr>'
	.((!is_null($quete['quete_duree']))?
					'<tr>
						<td colspan="'.$nbInfo.'">
							Vous avez : <b>'.AfficheTempPhrase(DecoupeTemp($quete['quete_duree'])).'</b>
						</td>
					</tr>'
	:'')
	.'<tr>
						<td class="description" colspan="'.$nbInfo.'">'
	.$quete['description']
	.(!is_null($quete['id_objet'])?'<p>Si vous remplissez votre quête à temps, vous recevrez ceci :'.AfficheInfoObjet($quete['id_objet']).'</p>':'')
	.'</td>
					</tr>
					<tr>
						<td colspan="'.$nbInfo.'">
							<button 
								type="button" 
								onclick="window.location=\'index.php?page=quete&amp;action=inscription&amp;num_quete='.$quete['id_quete'].'\'"' 
								.(count($_SESSION['QueteEnCours']) >= quete::NB_QUETE_MAX?'disabled=disabled ':'')
								.'class="quete" >
									Accepter
							</button>
						</td>
					</tr>
				</table>';
}
function InscriptionQuete($numQuete){
	$sql = "SELECT * FROM table_quete_lst WHERE id_quete=$numQuete;";
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	$infoQuete = mysql_fetch_array($requete, MYSQL_ASSOC);
	$sqlAdd = "INSERT INTO table_quetes (
		`id_quete_en_cours`, 
		`quete_login`, 
		`quete_id`, 
		`quete_position`, 
		`quete_vie`, 
		`quete_reussi`, 
		`date_start`, 
		`date_end`)
		VALUE(
		NULL, 
		'".$_SESSION['joueur']."', 
		".$infoQuete['id_quete'].", 
		'".SelectionPositionQuete()."', 
		".(is_null($infoQuete['quete_vie'])?"NULL":$infoQuete['quete_vie']).", 
		NULL, 
		'".date('Y-m-d H:i:s')."', 
		NULL
		);";
	$requete = mysql_query($sqlAdd) or die (mysql_error().'<br />'.$sqlAdd);

}
function SelectionPositionQuete(){
	global $objManager;

	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

	$carte = null;
	if($oJoueur->GetNiveau() <= 3){
		if(!is_null($oJoueur->GetMaisonInstalle())){
			$arcarte = $oJoueur->GetMaisonInstalle();
			$carte = $arcarte['0'];
		}else{
			$carte = $oJoueur->GetCarte();
		}
	}
	$free = FreeCaseCarte($carte);

	$objManager->update($oJoueur);
	unset($oJoueur);
	return $free[array_rand($free)];
}
function ClotureQuete($IDQuete) {
	$sqlBis = "UPDATE table_quetes SET quete_reussi = 1, date_end = '" . date('Y-m-d H:i:s') . "' WHERE id_quete_en_cours = " . $IDQuete . ";";
	mysql_query($sqlBis) or die(mysql_error() . '<br />' . $sqlBis);
}
function FoundQueteEnCours() {
	global $objManager;
	unset($_SESSION['QueteEnCours']);
	$QueteEnCours = NULL;
	$sql = "SELECT * FROM table_quetes WHERE quete_login='" . $_SESSION['joueur'] . "' AND quete_reussi IS NULL;";
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	$QuetePrecedente = null;

	if (mysql_num_rows($requete) > 0) {
		while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
			$sqlBis = "SELECT * FROM table_quete_lst WHERE id_quete=" . $row['quete_id'] . ";";
			$requeteBis = mysql_query($sqlBis) or die(mysql_error() . '<br />' . $sqlBis);
			$infoQuete = mysql_fetch_array($requeteBis, MYSQL_ASSOC);
			if (is_null($infoQuete['quete_duree']) OR (strtotime('now') - strtotime($row['date_start'])) < $infoQuete['quete_duree']) {
				if ($QuetePrecedente != $row['quete_id']) {
					$tmpQuete = new quete($row, $infoQuete);
					$objManager->UpdateQuete($tmpQuete);
					$QueteEnCours[] = $tmpQuete;
					$QuetePrecedente = $row['quete_id'];
				} else {
					ClotureQuete($row['id_quete_en_cours']);
				}
			} elseif ((strtotime('now') - strtotime($row['date_start'])) >= $infoQuete['quete_duree']) {
				ClotureQuete($row['id_quete_en_cours']);
			}
		}
		return $QueteEnCours;
	} else {
		return NULL;
	}
}
function AfficheAvancementQuete($QueteEnCours) {
	global $CodeCouleurQuete;

	$nbInfo = 0;
	if (!is_null($QueteEnCours->GetGainOr())) {
		$nbInfo++;
	}
	if (!is_null($QueteEnCours->GetGainExperience())) {
		$nbInfo++;
	}
	if (!is_null($QueteEnCours->GetGainPoints())) {
		$nbInfo++;
	}

	switch ($QueteEnCours->GetTypeQuete()) {
		case 'monstre':
		case 'romains':
			$status = '<tr><td colspan="' . $nbInfo . '"><img alt="Barre de Vie" src="./fct/fct_image.php?type=vie&amp;value=' . $QueteEnCours->GetVie() . '&amp;max=' . $QueteEnCours->GetVieMax() . '" /></td></tr>';
			break;
		case 'recherche':
		case 'objet':
			if (!is_null($QueteEnCours->GetDuree())) {
				$status = '
					<tr>
						<td colspan="' . $nbInfo . '">
							Temps restant : <br /><div style="display:inline;" id="TimeToWaitQuete' . $QueteEnCours->GetIDQuete() . '"></div>'
				. AfficheCompteurTemp('Quete' . $QueteEnCours->GetIDQuete(), 'index.php?page=quete&amp;action=annule&amp;num_quete='.$QueteEnCours->GetIDQuete(), ($QueteEnCours->GetDuree() - (strtotime('now') - $QueteEnCours->GetDateStart())))
				. '</td>
					</tr>';
			}
			break;
	}

	return '
	<table class="fiche_quete">
		<tr style="background:' . $CodeCouleurQuete[$QueteEnCours->GetTypeQuete()] . ';">
			<th colspan="' . $nbInfo . '">' . $QueteEnCours->GetNom() . '</th>
		</tr>
		<tr><td colspan="' . $nbInfo . '">Gains</td></tr>
		<tr>'
	. (!is_null($QueteEnCours->GetGainOr()) ?
                    '<td style="width:' . intval(100 / $nbInfo) . '%">'
	. AfficheIcone('or') . ' : <b>' . $QueteEnCours->GetGainOr() . '</b>
				</td>' : '')
	. (!is_null($QueteEnCours->GetGainExperience()) ?
                    '<td style="width:' . intval(100 / $nbInfo) . '%">
					Expérience : <b>' . $QueteEnCours->GetGainExperience() . '</b>
				</td>' : '')
	. (!is_null($QueteEnCours->GetGainPoints()) ?
                    '<td style="width:' . intval(100 / $nbInfo) . '%">
					Points : <b>' . $QueteEnCours->GetGainPoints() . '</b>
				</td>' : '')
	. '</tr>'
	. (isset($status) ? $status : '')
	. '<tr>
			<td class="description" colspan="' . $nbInfo . '">'
	. $QueteEnCours->GetDescription()
	. (!is_null($QueteEnCours->GetCodeObjet()) ? AfficheInfoObjet($QueteEnCours->GetCodeObjet()) . '<p>Si vous remplissez votre quête à temps, vous recevrez ceci.</p>' : '')
	. '</td>
		</tr>
		<tr>
			<td colspan="' . $nbInfo . '" style="background:#b6ff6c;">
				Toujours en cours
			</td>
		</tr>'
	. ($_GET['page'] == 'quete' ?
                    '<tr>
			<td colspan="' . $nbInfo . '">
				<button 
					type="button" 
					onclick="window.location=\'index.php?page=quete&amp;action=annule&amp;num_quete='.$QueteEnCours->GetIDQuete().'\'" 
					class="quete" >
						Annuler
				</button>
			</td>
		</tr>' : '') .
            '</table>';
}


?>