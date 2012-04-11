<?php
function FoundBatiment($idType = false, $login = false, $Coordonnees = false) {
	global $lstNonBatiment, $objManager;
	$sql = "SELECT * FROM table_carte WHERE
			login". ($login ? " = '".$login."'" : ' IS NULL')
	. ($idType ? " AND id_type_batiment=$idType" : "")
	. ($Coordonnees ? " AND coordonnee='$Coordonnees'" : "")
	. " AND detruit IS NULL;";

	$requete = mysql_query($sql) or die(mysql_error() . '<br />Function FoundBatiment SQL = ' . $sql);
	if (mysql_num_rows($requete) > 0) {
		$carte = mysql_fetch_array($requete, MYSQL_ASSOC);
			
		$sql2 = "SELECT * FROM table_batiment WHERE id_batiment=" . $carte['id_type_batiment'] . ";";
		$requete2 = mysql_query($sql2) or die(mysql_error() . '<br />' . $sql2);
		
		$batiment = mysql_fetch_array($requete2, MYSQL_ASSOC);
		
		$objBatiment =  new $batiment['batiment_type']($carte, $batiment);
		
		$objManager->UpdateBatiment($objBatiment);
		
		return $objBatiment;
	}
	
	return null;
	
}
function AfficheIcone($type, $HeightIcone = 20) {
	$Name = $type;
	$FileName = $type;

	switch (strtolower($type)) {
		case 'bois':				$FileName = 'ResBoi';										break;
		case 'pierre':				$FileName = 'ResPie';										break;
		case 'nourriture':			$FileName = 'ResNou';										break;
		case 'argent':
		case 'or':					$FileName = 'ResOr';										break;
		case 'marche_cancel':		$Name = 'Annuler transaction';								break;
		case 'marche_accept':		$Name = 'Accepter transaction';								break;
		case 'marche_attention':	$Name = 'Transaction impossible';							break;
		case 'gmedalor':			$Name = 'Médaille d\'or au classement général';				break;
		case 'gmedalargent':		$Name = 'Médaille d\'argent au classement général';			break;
		case 'gmedalbronze':		$Name = 'Médaille de bronze classement général';			break;
		case 'medalcombat':			$Name = 'Médaille du meilleur combattant';					break;
		case 'amedalor':			$Name = 'Médaille d\'or au classement des alliances';		break;
		case 'amedalargent':		$Name = 'Médaille d\'argent au classement des alliances';	break;
		case 'amedalbronze':		$Name = 'Médaille de bronze au classement des alliances';	break;
		case 'trash':				$Name = 'Supprimer';										break;
	}

	switch (substr($type, 0, 5)) {
		case 'ResBo':	$Name = 'Bois';						$FileName = substr($type, 0, 6);	break;
		case 'ResPi':	$Name = 'Pierre';					$FileName = substr($type, 0, 6);	break;
		case 'ResNo':	$Name = 'Nourriture';				$FileName = substr($type, 0, 6);	break;
		case 'ResOr':	$Name = 'Or';						$FileName = substr($type, 0, 5);	break;
		case 'ResDe':	$Name = 'Déplacements';				$FileName = 'deplacement';			break;
		case 'ResVi':	$Name = 'Vie';						$FileName = 'vie';					break;
		case 'ResHy':	$Name = 'Hydromel';					$FileName = 'hydromel';				break;
		case 'ResGu':	$Name = 'Gui';															break;
		case 'ResAb':	$Name = 'Absinthe';														break;
		case 'ResPt':	$Name = 'Petite Centaurée';												break;
		case 'Tissu':	$Name = 'Tissu';					$FileName = 'Tissu';				break;
		case 'Hydro':	$Name = 'Hydromel';					$FileName = 'hydromel';				break;
		case 'PotVi':	$Name = 'Potion de vie';			$FileName = 'PotVie';				break;
		case 'PotDe':	$Name = 'Potion de déplacement';	$FileName = 'PotDep';				break;
	}

	$sql = "SELECT objet_nom FROM table_objets WHERE objet_code='$type';";
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);

	if (mysql_num_rows($requete) > 0) {
		$row = mysql_fetch_array($requete, MYSQL_ASSOC);
		$Name = $row['objet_nom'];
	}

	return '<img src="./img/icones/ic_' . $FileName . '.png" alt="Icone (' . strtolower($Name) . ')" title="' . ucfirst(strtolower($Name)) . '" height="' . $HeightIcone . 'px" />';
}
function CorrectDataInfoBulle($txtInfoBulle) {
	$txt = str_replace('"', '&quot;', $txtInfoBulle);
	$txt = str_replace('<', '&lt;', $txt);
	$txt = str_replace('>', '&gt;', $txt);
	$txt = str_replace("'", "\'", $txt);
	return $txt;
}
function AfficheRecompenses($login = NULL, $alliance = NULL) {
	$sql = "SELECT login, nb_points, niveau, experience, nb_victoire, clan FROM table_joueurs ORDER BY nb_points DESC, niveau DESC, experience DESC;";
	$rqt = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);

	//Le meilleur Score général
	$rang = 1;
	$nbCombatGagne = 0;
	$txtGeneral = NULL;
	$txtMedalHonor = NULL;
	$txtMedalHonor = NULL;
	$arAlliances = NULL;
	$chkRang = true;

	while ($row = mysql_fetch_array($rqt, MYSQL_ASSOC)) {
		if (isset($arAlliances[$row['clan']])) {
			$arAlliances[$row['clan']] += $row['nb_points'];
		} elseif (!in_array($row['clan'], array(NULL, '1'))) {
			$arAlliances[$row['clan']] = $row['nb_points'];
		}
		if (!is_null($login)) {
			if ($row['nb_victoire'] > $nbCombatGagne) {
				if ($login == $row['login']) {
					$txtMedalHonor = AfficheIcone('MedalCombat');
				} else {
					$txtMedalHonor = NULL;
				}
				$nbCombatGagne = $row['nb_victoire'];
			}
			if ($login == $row['login'] AND $chkRang) {
				switch ($rang) {
					case 1: $txtGeneral = AfficheIcone('GMedalOr');
					break;
					case 2: $txtGeneral = AfficheIcone('GMedalArgent');
					break;
					case 3: $txtGeneral = AfficheIcone('GMedalBronze');
					break;
				}
			} else {
				$rang++;
			}
			if ($rang > 3) {
				$chkRang = false;
			}
		}
	}

	$txtMedalAlliance = NULL;
	if (!is_null($alliance)) {
		arsort($arAlliances);
		reset($arAlliances);
		for ($i = 0; $i <= (count($arAlliances) > 3 ? 3 : count($arAlliances)); $i++) {
			if (key($arAlliances) == $alliance) {
				switch ($i) {
					case 0: $txtMedalAlliance = AfficheIcone('AMedalOr');
					break(2);
					case 1: $txtMedalAlliance = AfficheIcone('AMedalArgent');
					break(2);
					case 2: $txtMedalAlliance = AfficheIcone('AMedalBronze');
					break(2);
				}
			}
			next($arAlliances);
		}
	}

	return (!is_null($login) ? $txtGeneral . $txtMedalHonor : '') . (!is_null($alliance) ? $txtMedalAlliance : '');
}
function AfficheListePrix(array $arPrix, array $arRessources = null) {
	$chk = false;
	$txt = null;
	if (isset($arPrix['Nourriture']) AND $arPrix['Nourriture'] > 0) {
		if (
		(isset($arRessources['Nourriture']) and $arRessources['Nourriture'] >= $arPrix['Nourriture'])
		OR
		is_null($arRessources)
		) {
			$ColorPrix = 'black';
		} else {
			$ColorPrix = 'red';
		}
		$txt .= '<span style="color:' . $ColorPrix . ';">' . $arPrix['Nourriture'] . '</span> ' . AfficheIcone('nourriture');
		$chk = true;
	}
	if (isset($arPrix['Bois']) AND $arPrix['Bois'] > 0) {
		if ($chk) {
			$txt .= ', ';
		}
		if (
		(isset($arRessources['Bois']) and $arRessources['Bois'] >= $arPrix['Bois'])
		OR
		is_null($arRessources)
		) {
			$ColorPrix = 'black';
		} else {
			$ColorPrix = 'red';
		}
		$txt .= '<span style="color:' . $ColorPrix . ';">' . $arPrix['Bois'] . '</span> ' . AfficheIcone('bois');
		$chk = true;
	}
	if (isset($arPrix['Pierre']) AND $arPrix['Pierre'] > 0) {
		if ($chk) {
			$txt .= ', ';
		}
		if (
		(isset($arRessources['Pierre']) and $arRessources['Pierre'] >= $arPrix['Pierre'])
		OR
		is_null($arRessources)
		) {
			$ColorPrix = 'black';
		} else {
			$ColorPrix = 'red';
		}
		$txt .= '<span style="color:' . $ColorPrix . ';">' . $arPrix['Pierre'] . '</span> ' . AfficheIcone('pierre');
		$chk = true;
	}
	if (isset($arPrix['Or']) AND $arPrix['Or'] > 0) {
		if ($chk) {
			$txt .= ', ';
		}
		if (
		(isset($arRessources['Or']) and $arRessources['Or'] >= $arPrix['Or'])
		OR
		is_null($arRessources)
		) {
			$ColorPrix = 'black';
		} else {
			$ColorPrix = 'red';
		}
		$txt .= '<span style="color:' . $ColorPrix . ';">' . $arPrix['Or'] . '</span> ' . AfficheIcone('or');
		$chk = true;
	}
	if (isset($arPrix['Hydromel']) AND $arPrix['Hydromel'] > 0) {
		if ($chk) {
			$txt .= ', ';
		}
		if (
		(isset($arRessources['Hydromel']) and $arRessources['Hydromel'] >= $arPrix['Hydromel'])
		OR
		is_null($arRessources)
		) {
			$ColorPrix = 'black';
		} else {
			$ColorPrix = 'red';
		}
		$txt .= '<span style="color:' . $ColorPrix . ';">' . $arPrix['Hydromel'] . '</span> ' . AfficheIcone('Hydromel');
		$chk = true;
	}
	return $txt;
}
function AfficheCompteurTemp($type, $url, $tmp) {
	return '
	<script type="text/JavaScript">
		function CountDown' . $type . '(time' . $type . '){
			if(time' . $type . '>0){
				if(time' . $type . '>=1){
					document.getElementById("TimeToWait' . $type . '").innerHTML = ArrangeDate(time' . $type . ');
					btime' . $type . ' = time' . $type . '-1;
					if (btime' . $type . '==0){check' . $type . '=true;}
				}
				setTimeout("CountDown' . $type . '(btime' . $type . ')", 1000);
			}else if(check' . $type . '){window.location="' . $url . '";}
		}
		var check' . $type . '=false;
		CountDown' . $type . '(' . $tmp . ');
	</script>';
}
function AfficheTempPhrase($arTemp) {
	return ($arTemp['0'] > 0 ? $arTemp['0'] . 'jrs ' : '') . ($arTemp['1'] > 0 ? $arTemp['1'] . 'hrs ' : '') . ($arTemp['2'] > 0 ? $arTemp['2'] . 'min ' : '') . ($arTemp['3'] > 0 ? $arTemp['3'] . 'sec' : '');
}
function DecoupeTemp($intTime) {
	if ($intTime >= 0 && $intTime <= 59) {
		// Seconds
		$nbJours = null;
		$nbHeures = null;
		$nbMinutes = null;
		$nbSecondes = $intTime;
	} elseif ($intTime >= 60 && $intTime <= 3599) {
		// Minutes + Seconds
		$pmin = $intTime / 60;
		$premin = floor($pmin);
		$presec = $pmin - $premin;
		$sec = $presec * 60;
		$nbJours = null;
		$nbHeures = null;
		$nbMinutes = $premin;
		$nbSecondes = round($sec);
	} elseif ($intTime >= 3600 && $intTime <= 86399) {
		// Hours + Minutes 4253
		$phour = $intTime / 3600;
		$prehour = floor($phour);
		$premin = ($phour - $prehour) * 60;
		$min = floor($premin);
		$presec = $premin - $min;
		$sec = $presec * 60;
		$nbJours = null;
		$nbHeures = $prehour;
		$nbMinutes = $min;
		$nbSecondes = round($sec);
	} elseif ($intTime >= 86400) {
		// Days + Hours + Minutes
		$pday = $intTime / 86400;
		$preday = floor($pday);
		$phour = ($pday - $preday) * 24;
		$prehour = floor($phour);
		$premin = ($phour - $prehour) * 60;
		$min = floor($premin);
		$presec = $premin - $min;
		$sec = $presec * 60;
		$nbJours = $preday;
		$nbHeures = $prehour;
		$nbMinutes = $min;
		$nbSecondes = round($sec);
	}
	return array($nbJours, $nbHeures, $nbMinutes, $nbSecondes);
}
function CheckIfAssezRessource(array $arRessource, personnage $Joueur, maison $Maison){
	if(in_array($arRessource['0'], array('ResBoi', 'ResPie', 'ResNou', 'ResOr'))){
		if(	('ResBoi' == $arRessource['0'] AND $Maison->GetRessourceBois() >= $arRessource['1'])
		OR
		('ResPie' == $arRessource['0'] AND $Maison->GetRessourcePierre() >= $arRessource['1'])
		OR
		('ResNou' == $arRessource['0'] AND $Maison->GetRessourceNourriture() >= $arRessource['1'])
		OR
		('ResOr' == $arRessource['0'] AND $Joueur->GetArgent() >= $arRessource['1']))
		{
			return true;
		}
	}else{
		return $Joueur->AssezElementDansBolga($arRessource['0'], $arRessource['1']);
	}
	return false;
}
function AfficheEquipement($type, &$oJoueur) {
	$SizeHeight = 50;

	switch ($type) {
		case '1': $CodeObjet = $oJoueur->GetCasque();	break;
		case '2': $CodeObjet = $oJoueur->GetBouclier();	break;
		case '3': $CodeObjet = $oJoueur->GetJambiere();	break;
		case '4':
			$CodeObjet = $oJoueur->GetCuirasse();
			$SizeHeight = 150;
			break;
		case '5':
			$CodeObjet = $oJoueur->GetArme();
			$SizeHeight = 150;
			break;
		case '6': $CodeObjet = $oJoueur->GetSac();		break;
		case '7': $CodeObjet = $oJoueur->GetLivre();	break;
	}

	if (is_null($CodeObjet)) {
		return '&nbsp;';
	}

	//if ($_SESSION['main']['uri'] == 3
	if ($_GET['page'] == 'inventaire'
	AND count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga()
	AND !in_array($type, array(7))) {
		$chkLink = true;
	} else {
		$chkLink = false;
	}

	if (in_array($type, array(7))) {
		if ($CodeObjet == 'NoBook') {
			if (is_null($oJoueur->GetLstSorts())) {
				return '&nbsp;';
			} else {
				$arSort = explode('=', current($oJoueur->GetLstSorts()));
				$CodeObjet = $arSort[0];
			}
		} else {
			//on affiche le livre
			if(!is_null($oJoueur->GetLstSorts())){
				$InfoBulle = '<table class="equipement">';
				foreach ($oJoueur->GetLstSorts() as $Sort) {
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
			}else{$InfoBulle = '<table class="equipement"><tr><th>Votre livre est vide.</th></tr></table>';
			}
		}

		return ($chkLink ?'
			<a href="./fct/main.php?action=unuse&amp;id=' . $type . '">' : '')
		. '<img src="./img/objets/' . $CodeObjet . '.png" '
		. 'height="' . $SizeHeight . '" '
		. 'alt="Livre de sort" '
		. 'onmouseover="montre(\'' . CorrectDataInfoBulle($InfoBulle) . '\');" '
		. 'onmouseout="cache();" '
		. '/>'
		. ($chkLink ?'</a>' : '');
	} else {
		return ($chkLink ?'
    		<a href="./fct/main.php?action=unuse&amp;id=' . $type . '">' : '')
		.AfficheInfoObjet($CodeObjet, $SizeHeight)
		. ($chkLink ?'</a>' : '');
	}
}
function AfficheInfoObjet($CodeObjet, $intHeightImg = 50) {
	$sql = "SELECT * FROM table_objets WHERE objet_code='" . $CodeObjet . "';";
	$rqt = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);

	$rowObjet = mysql_fetch_array($rqt, MYSQL_ASSOC);

	if (in_array($rowObjet['objet_type'], array('arme', 'bouclier', 'cuirasse', 'jambiere', 'casque'))) {
		$nbInfo = 0;
		$txtInfo = '<tr>';

		if($rowObjet['objet_attaque'] != 0){
			$txtInfo .= '<td>' . AfficheIcone('attaque') . ' = ' . $rowObjet['objet_attaque'] . '</td>';
			$nbInfo++;
		}

		if($rowObjet['objet_distance'] != 0){
			$txtInfo .= '<td>' . AfficheIcone('distance') . ' = ' . $rowObjet['objet_distance'] . '</td>';
			$nbInfo++;
		}

		if($rowObjet['objet_defense'] != 0){
			$txtInfo .= '<td>' . AfficheIcone('defense') . ' = ' . $rowObjet['objet_defense'] . '</td>';
			$nbInfo++;
		}

		$txtInfo .= '</tr>';
	}

	$InfoBulle =
            '<table class="InfoBulle">'
	. '<tr><th' . (isset($txtInfo) ? ' colspan="'.$nbInfo.'"' : '') . '>' . $rowObjet['objet_nom'] . '</th></tr>'
	. ((!is_null($rowObjet['objet_description']))?'<tr><td'.(isset($txtInfo)?' colspan="'.$nbInfo.'"':'').' style="text-align:left;">' . $rowObjet['objet_description'] . '</td></tr>':'')
	. (isset($txtInfo) ? $txtInfo : '')
	. '<tr><td' . (isset($txtInfo) ? ' colspan="'.$nbInfo.'"' : '') . '>' . AfficheIcone('or') . ' = ' . $rowObjet['objet_prix'] . '</td></tr>'
	. '</table>';

	return '<img '
	. 'style="vertical-align:middle;" '
	. 'alt="' . $rowObjet['objet_nom'] . '" '
	. 'src="./img/objets/' . $rowObjet['objet_code'] . '.png" '
	. 'onmouseover="montre(\'' . CorrectDataInfoBulle($InfoBulle) . '\');" '
	. 'onmouseout="cache();" '
	. 'height="'.$intHeightImg.'px"'
	. ' />';
}
function QuelTypeRessource($Code) {
	switch (substr($Code, 0, 5)) {
		case 'ResNo': return 'nourriture';
		case 'ResBo': return 'bois';
		case 'ResPi': return 'pierre';
		case 'ResVi':
		case 'PotVi': return 'vie';
		case 'ResDe':
		case 'PotDe': return 'deplacement';
		case 'ResOr': return 'argent';
		default: return 'divers';
	}
}
function FreeCaseCarte($carte = NULL) {
	$sql = "SELECT coordonnee FROM table_carte WHERE detruit IS NULL;";
	$requete = mysql_query($sql) or die(mysql_error() . $sql);
	while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
		$arCoordonnee = explode(',', $row['coordonnee']);
		if (is_null($carte) OR $arCoordonnee['0'] == $carte) {
			$arBusy[$arCoordonnee['0']][$arCoordonnee['1']][$arCoordonnee['2']] = true;
		}
	}

	global $nbLigneCarte, $nbColonneCarte;
	//ATTENTION la carte M est retirée car c'est la carte du camp romain
	$arCartes = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y');

	if (is_null($carte)) {
		$carte = $arCartes[array_rand($arCartes)];
	}

	for ($i = 0; $i <= $nbLigneCarte; $i++) {
		for ($j = 0; $j <= $nbColonneCarte; $j++) {
			if (!isset($arBusy[$carte][$i][$j])) {
				$arFree[] = implode(',', array($carte, $i, $j));
			}
		}
	}

	return $arFree;
}
function CheckIfClanExiste($nom) {
	$sql = "SELECT id_alliance FROM table_alliance WHERE membre_actif IS NULL AND nom_clan='".htmlspecialchars($nom, ENT_QUOTES)."';";
	$requete = mysql_query($sql) or die(mysql_error());
	if (mysql_num_rows($requete) > 0) {
		return true;
	} else {
		return false;
	}
}
function ListeMembreClan($Clan){
	//on construit une liste des membres du clan
	$lstMembre = null;
	if (!is_null($Clan) AND $Clan != '1'){
		$sql = "SELECT chef_clan, membre_clan, date_inscription FROM table_alliance WHERE nom_clan='".htmlspecialchars($Clan, ENT_QUOTES)."' AND membre_actif IS NULL;";
		$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
		while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if (is_null($row['date_inscription'])){
				$lstMembre[] = $row['membre_clan'];
			}elseif($row['chef_clan'] == $_SESSION['joueur']){
				$_SESSION['message']['alliance'] = '<p>Vous avez une ou des demande(s) d\'adhésion à votre alliance. Allez vite voir sur la page "<a href="index.php?page=alliance">Alliance</a>".</p>';
			}
		}
	}else{
		$lstMembre[] = $_SESSION['joueur'];
	}
	return $lstMembre;
}
function AddHistory($Login, $Carte, $Position, $Type, $Adversaire, $Date, $Info) {
	if(is_null($Date)){
		$Date = strtotime('now');
	}
	$sql = "INSERT INTO `table_history` (`history_id`, `history_login`, `history_position`, `history_type`, `history_adversaire`, `history_date`, `history_info`)
			VALUES (NULL, '$Login', '" . implode(',', array_merge(array($Carte), $Position)) . "', '$Type', '$Adversaire', '".date('Y-m-d H:i:s', $Date)."', '" . htmlentities($Info, ENT_QUOTES) . "');";
	mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
}
function FinishAllCompetenceEnCours(personnage &$oJoueur) {
	global $lstPoints;
	$sqlCmp = "SELECT * FROM table_competence WHERE cmp_login='" . $_SESSION['joueur'] . "' AND cmp_finish IS NULL";
	$rqtCmp = mysql_query($sqlCmp) or die(mysql_error() . '<br />' . $sqlCmp);
	while ($cmp = mysql_fetch_array($rqtCmp, MYSQL_ASSOC)) {
		if ((strtotime('now') - strtotime($cmp['cmp_date'])) >= $cmp['cmp_temp']) {
			$sql = "UPDATE  `table_competence` SET  `cmp_finish` =  TRUE WHERE `table_competence`.`cmp_id` =" . $cmp['cmp_id'] . ";";
			mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
			$oJoueur->UpdatePoints($lstPoints['CmpTerminé'][0]);
			AddHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'Competence', NULL, NULL, 'Compétence terminée : '.$cmp['cmp_nom'].' de niveau '.$cmp['cmp_niveau']);
		}
	}
}
function AfficheNbMessageAlliance($clan, $date){
	$sql = "SELECT id_chat FROM table_chat
			WHERE (clan_chat='".htmlspecialchars($clan, ENT_QUOTES)."' AND date_chat > '".$date."') 
			ORDER BY date_chat DESC;";
	$requete = mysql_query($sql) or die (mysql_error());
	return mysql_num_rows($requete);
}
function GetInfoCarriere($code, $info = null){
	$sql = "SELECT * FROM table_carrieres_lst WHERE carriere_code='".$code."';";
	$rqtMetier = mysql_query($sql) or die ( mysql_error() );

	if(mysql_num_rows($rqtMetier) > 0){
		if(is_null($info)){
			return mysql_fetch_array($rqtMetier, MYSQL_ASSOC);
		}else{
			$temp = mysql_fetch_array($rqtMetier, MYSQL_ASSOC);
			return $temp[$info];
		}
	}else{
		return null;
	}
}

//+---------------------------------+
//|				ACTIONS				|
//+---------------------------------+
function ActionMettreDansBolga(&$check, $type, personnage &$oJoueur, &$objManager){
	if(isset($_SESSION['LoginStatus'])){
		//On vérifie si le bolga est plein ou pas
		if(count($oJoueur->GetLstInventaire()) >= $oJoueur->QuelCapaciteMonBolga()){
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionMettreDansBolga - Bolga plein';
			return;
		}
		//on vérifie si on a bien la quantitée
		if(!isset($_SESSION['LoginStatus'][$type])){
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionMettreDansBolga - Pas assez de ressource';
			return;
		}
		//Si tout OK, alors on transfert
		$maison = $oJoueur->GetObjSaMaison();
		switch($type){
			case 'Nourriture':
				$maison->MindNourriture($_SESSION['LoginStatus'][$type]);
				break;
			case 'Bois':
				$maison->MindBois($_SESSION['LoginStatus'][$type]);
				break;
			case 'Pierre':
				$maison->MindPierre($_SESSION['LoginStatus'][$type]);
				break;
		}
		$oJoueur->AddInventaire('Res'.ucfirst(substr($type, 0, 3)).$_SESSION['LoginStatus'][$type], strtolower($type), 1, false);

		$objManager->UpdateBatiment($maison);

		unset($_SESSION['LoginStatus']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionMettreDansBolga';
	}
}
function ActionRessource(&$check, personnage &$oJoueur, &$objManager, $id = NULL){
	if(isset($_SESSION['main']['ressource'])){
		if(is_null($_SESSION['main']['ressource']->GetCollecteur())){
			$_SESSION['main']['ressource']->StartCollect($oJoueur, $id);
		}elseif((strtotime('now') - $_SESSION['main']['ressource']->GetDateDebutAction()) >= $_SESSION['main']['ressource']->GetTempRessource()){
			if($oJoueur->GetLogin() == $_SESSION['main']['ressource']->GetCollecteur()){
				$oMaison = $oJoueur->GetObjSaMaison();
				$_SESSION['main']['ressource']->FinishCollect($oJoueur, $oMaison);
			}else{
				$oCollecteur = $objManager->GetPersoLogin($_SESSION['main']['ressource']->GetCollecteur());
				$oMaison = $oCollecteur->GetObjSaMaison();
				$_SESSION['main']['ressource']->FinishCollect($oCollecteur, $oMaison);
				$objManager->update($oCollecteur);
				unset($oCollecteur);
			}
			$objManager->UpdateBatiment($oMaison);
			unset($oMaison);
		}
		$objManager->UpdateRessource($_SESSION['main']['ressource']);
		unset($_SESSION['main']['ressource']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionRessource';
	}
}
function ActionDeplacement(&$check, &$oJoueur){
	if(!is_null($_SESSION['main']['deplacement'])){
		switch($_SESSION['main']['deplacement']){
			case 'new':
				if(personnage::TEMP_DEPLACEMENT_SUP - (strtotime('now')-$oJoueur->GetLastAction()) <= 0){
					$oJoueur->AddDeplacement(personnage::NB_DEPLACEMENT_SUP,'new');
				}
				break;
		}
		$_SESSION['main']['deplacement'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionDeplacement';
	}
}
function ActionUtiliser(&$check, &$arInfoObject, personnage &$oJoueur, &$objManager){
	if(!is_null($arInfoObject['code'])){
		if($arInfoObject['action']){
			$oJoueur->AddInventaire($arInfoObject['code'], $arInfoObject['type']);
		}

		$maison = $oJoueur->GetObjSaMaison();

		switch($arInfoObject['type']){
			case 'vie':			$oJoueur->GagnerVie($arInfoObject['value']);							break;
			case 'deplacement':	$oJoueur->AddDeplacement($arInfoObject['value'],'objet');				break;
			case 'argent':		$oJoueur->AddOr($arInfoObject['value']);								break;
			case 'nourriture':	if(!is_null($maison)){
				$maison->AddNourriture($arInfoObject['value']);
			}	break;
			case 'bois':		if(!is_null($maison)){
				$maison->AddBois($arInfoObject['value']);
			}		break;
			case 'pierre':		if(!is_null($maison)){
				$maison->AddPierre($arInfoObject['value']);
			}		break;
		}

		if(!is_null($maison)){
			$objManager->UpdateBatiment($maison);
			unset($maison);
		}

		$oJoueur->CleanInventaire($arInfoObject['code']);
		$arInfoObject['code'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionUtiliser';
	}
}
function ActionLaisser(&$check, personnage &$oJoueur){
	if(isset($_GET['type'])){
		if($_GET['type'] == 'objet'){
			$oJoueur->SetLastObject(true,null);
		}
		unset($_SESSION['main'][$_GET['type']]);
	}elseif(isset($_GET['id'])){
		$oJoueur->CleanInventaire($_SESSION['main'][$_GET['id']]['code'], true);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionLaisser';
	}
}
function ResetListeQuetes($login) {
	$sql = "SELECT id_quete_en_cours FROM  table_quetes WHERE quete_login='" . $login . "';";
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
		$sqlRemove = "DELETE FROM table_quetes WHERE id_quete_en_cours=" . intval($row['id_quete_en_cours']) . ";";
		mysql_query($sqlRemove) or die(mysql_error() . '<br />' . $sqlRemove);
	}
}
?>