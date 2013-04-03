<?php
/**
 * Retourne l'objet Batiment si trouvé.
 * @param integer $idType <p>Numéro ID du type de bâtiment (Ex: 1 = Maison, ...). Default = False</p>
 * @param string $login <p>Le Login du propriétaire du bâtiment. Default = False</p>
 * @param string $Coordonnees <p>Les coordonnées du bâtiment structuré comme "A_1_1". Default = False</p>
 * @return NULL|batiment <p>Retourne NULL si bâtiment non trouvé, sinon retourne l'objet bâtiment</p>
 */
function FoundBatiment($idType = NULL, $login = NULL, $Coordonnees = NULL) {
	global $lstNonBatiment, $objManager;
	$sql = "SELECT * FROM table_carte WHERE
			detruit IS NULL"
			. (!is_null($login)?
				" AND login = '".$login."'"
				:NULL)
			. (!is_null($idType)?
				" AND id_type_batiment=$idType"
				:NULL)
			. (!is_null($Coordonnees)?
				" AND coordonnee='$Coordonnees'"
				:NULL)
			. ";";

	$requete = mysql_query($sql) or die(mysql_error() . '<br />Function FoundBatiment SQL = ' . $sql);
	
	if (mysql_num_rows($requete) > 0) {
		$carte = mysql_fetch_array($requete, MYSQL_ASSOC);
		if(!in_array($carte['id_type_batiment'], $lstNonBatiment))
		{	
			$sql2 = "SELECT * FROM table_batiment WHERE id_type=" . $carte['id_type_batiment'] . " AND batiment_niveau=".$carte['niveau_batiment'].";";
			$requete2 = mysql_query($sql2) or die(mysql_error() . '<br />' . $sql2);
			
			$batiment = mysql_fetch_array($requete2, MYSQL_ASSOC);
			
			$objBatiment =  new $batiment['batiment_type']($carte, $batiment);
			
			$objManager->UpdateBatiment($objBatiment);
			
			return $objBatiment;
		}
	}elseif(is_null($login) AND is_null($Coordonnees) AND !is_null($idType)){
		$sql2 = "SELECT * FROM table_batiment 
					WHERE id_type=" . $idType . "
						 AND batiment_niveau=1;";
		
		$requete2 = mysql_query($sql2) or die(mysql_error() . '<br />' . $sql2);
			
		$batiment = mysql_fetch_array($requete2, MYSQL_ASSOC);
			
		$objBatiment =  new $batiment['batiment_type'](NULL, $batiment);
			
		//$objManager->UpdateBatiment($objBatiment);
			
		return $objBatiment;
	}
	
	return null;
	
}
/**
 * Requete SQL pour trouver les infos d'un objet donner par $CodeObject et retourne une class de l'objet
 * @param string $CodeObject <p>Code de l'objet</p>
 * @param integer $nbObjet <p>Nombre d'obet. Default = 1</p>
 * @return NULL|object <p>Class d'objet spécifique</p>
 */
function FoundObjet($CodeObject, $nbObjet = 1){
	$sql = "SELECT * FROM table_objets WHERE objet_code='".strval($CodeObject)."';";
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	
	if (mysql_num_rows($requete) > 0)
	{
		$arInfoObject = mysql_fetch_array($requete, MYSQL_ASSOC);
	
		global $lstTypeObjets;
	
		if(	in_array($arInfoObject['objet_type'], array_merge($lstTypeObjets, array(qteCombat::TYPE_QUETE_ENNEMI, qteCombat::TYPE_QUETE_MONSTRE))))
		{
			$ObjetNom = 'obj'.$arInfoObject['objet_type'];
				
			return new $ObjetNom($arInfoObject, $nbObjet);
			
		}
	}
	
	return NULL;
}
function FoundGibier($CodeGibier){
	$sqlGibier = "SELECT * FROM table_objets WHERE objet_code='".$CodeGibier."';";
	$rqtGibier = mysql_query($sqlGibier) or die(mysql_error() . '<br />' . $sqlGibier);
	//on crée une table contenant les infos des objets
	
	return new Gibier(mysql_fetch_array($rqtGibier, MYSQL_ASSOC));
}
function FoundQuete($IDTypeQuete, $Login = false) {
	$sql = "SELECT * FROM table_quete_lst WHERE id_quete=".$IDTypeQuete.";";
	
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);

	$infoQuete = mysql_fetch_array($requete, MYSQL_ASSOC);
	$QueteNom = $infoQuete['quete_type'];
	
	if (mysql_num_rows($requete) > 0)
	{
		if($Login)
		{
			$sqlBis = "SELECT * FROM table_quetes WHERE quete_id=$IDTypeQuete AND quete_login='$Login';";
			$requeteBis = mysql_query($sqlBis) or die(mysql_error() . '<br />' . $sqlBis);
				
			if (mysql_num_rows($requeteBis) > 0)
			{
				$row = mysql_fetch_array($requeteBis, MYSQL_ASSOC);
			}else{
				$row = array();
			}
			
		}else{
			$row = array();
		}
		
		$objQuete = new $QueteNom($row, $infoQuete);
		
		global $objManager;
		$objManager->UpdateQuete($objQuete);
			
		return $objQuete;
	}
	
	return NULL;
}
function ListQueteEnCours() {
	global $objManager;
	
	unset($_SESSION['QueteEnCours']);
	
	$lstQueteEnCours = NULL;
	
	$sql = "SELECT * FROM table_quetes WHERE quete_login='" . $_SESSION['joueur'] . "' AND quete_reussi IS NULL;";
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	
	$QuetePrecedente = null;

	if (mysql_num_rows($requete) > 0)
	{
		while ($row = mysql_fetch_array($requete, MYSQL_ASSOC))
		{
			$sqlBis = "SELECT * FROM table_quete_lst WHERE id_quete=" . $row['quete_id'] . ";";
			$requeteBis = mysql_query($sqlBis) or die(mysql_error() . '<br />' . $sqlBis);
			
			$infoQuete = mysql_fetch_array($requeteBis, MYSQL_ASSOC);
			
			$QueteNom = $infoQuete['quete_type'];
			
			$tmpQuete = new $QueteNom($row, $infoQuete);
			
			if($QuetePrecedente != $tmpQuete->GetIDTypeQuete())
			{
				$lstQueteEnCours[] = $tmpQuete;
			}else{
				$tmpQuete->FinishQuete();
			}
			$objManager->UpdateQuete($tmpQuete);
			
			$QuetePrecedente = $tmpQuete->GetIDTypeQuete();
		}
		return $lstQueteEnCours;
	}
	
	return NULL;
}

function AfficheIcone($type, $HeightIcone = 20) {
	$Name = NULL;
	$FileName = $type;

	switch (strtolower($type))
	{
		case strtolower(personnage::TYPE_EXPERIENCE):
		case strtolower(objArmement::TYPE_ATTAQUE):
		case strtolower(objArmement::TYPE_DEFENSE):
		case strtolower(objArmement::TYPE_DISTANCE):
			$Name = $type;
			break;
		//case 'pierre':				$FileName = 'ResPie';										break;
		case strtolower(maison::TYPE_RES_NOURRITURE):
			$FileName	= maison::TYPE_RES_NOURRITURE;
			$Name		= maison::TYPE_RES_NOURRITURE;
			break;
		case strtolower(maison::TYPE_RES_EAU_POTABLE):
			$FileName	= maison::TYPE_RES_EAU_POTABLE;
			$Name		= maison::TYPE_RES_EAU_POTABLE;
			break;
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
		case 'monnaie':
		case strtolower(personnage::TYPE_RES_MONNAIE):
			$FileName	= personnage::TYPE_RES_MONNAIE;
			$Name		= personnage::TYPE_RES_MONNAIE;
			break;
	}

	/* switch (substr($type, 0, 5)) {
		case 'ResBo':	$Name = 'Bois';						$FileName = substr($type, 0, 6);	break;
		case 'ResPi':	$Name = 'Pierre';					$FileName = substr($type, 0, 6);	break;
		case 'ResNo':	$Name = 'Nourriture';				$FileName = substr($type, 0, 6);	break;
		case 'ResOr':	$Name = 'Or';						$FileName = substr($type, 0, 5);	break;
		case 'ResDe':
		case 'Depla':	$Name = 'Déplacements';				$FileName = 'deplacement';			break;
		case 'ResVi':
		case 'Vie':		$Name = 'Vie';						$FileName = 'vie';					break;
		case 'ResHy':	$Name = 'Hydromel';					$FileName = 'hydromel';				break;
		case 'ResGu':	$Name = 'Gui';															break;
		case 'ResAb':	$Name = 'Absinthe';														break;
		case 'ResPt':	$Name = 'Petite Centaurée';												break;
		case 'Tissu':	$Name = 'Tissu';					$FileName = 'Tissu';				break;
		case 'Hydro':	$Name = 'Hydromel';					$FileName = 'hydromel';				break;
		case 'PotVi':	$Name = 'Potion de vie';			$FileName = 'PotVie';				break;
		case 'PotDe':	$Name = 'Potion de déplacement';	$FileName = 'PotDep';				break;
	} */

	if(is_null($Name))
	{
		$sql = "SELECT objet_nom FROM table_objets WHERE objet_code='$type';";
		$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
		
		if (mysql_num_rows($requete) > 0) {
			$row = mysql_fetch_array($requete, MYSQL_ASSOC);
			$Name = $row['objet_nom'];
		}else{
			$Name = 'NoName';
		}
	}
	

	return '<img src="./img/icones/ic_' . $FileName . '.png" alt="Icone (' . strtolower($Name) . ')" title="' . ucfirst(strtolower($Name)) . '" height="' . $HeightIcone . '" />';
}
function CorrectDataInfoBulle($txtInfoBulle) {
	$txt = str_replace(array('"', '&#34;'), '&quot;', $txtInfoBulle);
	$txt = str_replace('<', '&lt;', $txt);
	$txt = str_replace('>', '&gt;', $txt);
	$txt = str_replace(array("'", "&#39;"), "\'", $txt);
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
function AfficheListePrix($lstPrix, personnage &$oJoueur = NULL, maison &$maison = NULL) {
	$chk = false;
	$txt = NULL;
	
	if(!is_null($lstPrix))
	{
		foreach($lstPrix as $arPrix)
		{
			$Prix = explode('=', $arPrix);
			
			if(	$Prix[0] != quete::TYPE_QUETE)
			{
				if($chk)
				{
					$txt .= ', ';
				}
				
				if($Prix[1] > 0 OR QuelTypeObjet($Prix[0]) == personnage::TYPE_COMPETENCE)
				{
					$ColorPrix = 'black';
				
					if ((!is_null($oJoueur) OR !is_null($maison)) AND !CheckIfAssezRessource($Prix, $oJoueur, $maison))
					{
						$ColorPrix = 'red';
					}
					
					switch(QuelTypeObjet($Prix[0]))
					{
						case personnage::TYPE_COMPETENCE:
							$txt .= '<span style="color:' . $ColorPrix . ';">Compétence "' . GetInfoCompetence($Prix[0], 'cmp_lst_nom') . '"</span>';
							break;
						default:
							/* if(substr($Prix[0], 0, 4) == 'LING')
							{
								$Prix[0] = strtolower(substr($oJoueur->GetCivilisation(), 0, 1)).$Prix[0];
							} */
							$txt .= '<span style="color:' . $ColorPrix . ';">' . $Prix[1] . '</span> ' . AfficheIcone($Prix[0]);
							break;
					}
					
					
				}
				$chk = true;
			}else{
				
				if($chk)
				{
					$txt .= ', ';
				}
				
				$oQuete = FoundQuete($Prix[1], $oJoueur->GetLogin());
				
				$ColorPrix = 'red';
				
				if(	!is_null($oQuete))
				{
					if($oQuete->CheckIfDejaTermine($oJoueur->GetLogin()))
					{
						$ColorPrix = 'black';
					}
				}else{
					$oQuete = FoundQuete($Prix[1]);
				}
				
				if(!is_null($oQuete))
				{
					$txt .= '<span style="color:' . $ColorPrix . ';">Quête "' . $oQuete->GetNom() . '"</span>';
					
				}else{
					$txt .= '<span style="color:red;">Quête innexistante. Contactez Admin.</span>';
				}
				
				$chk = true;
				
			}
			
		}
	}else{
		$txt = 'Gratuit';
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
/**
 * Déduis chaque ressource du nombre donné
 * @param array $arRessource <p>array(code, nombre)</p>
 * @param personnage $Joueur
 * @param maison $Maison
 */
function UtilisationRessource(array $arRessource, personnage &$Joueur, maison &$Maison){
	switch(QuelTypeObjet($arRessource[0]))
	{
		case maison::TYPE_RES_EAU_POTABLE:
		case maison::TYPE_RES_NOURRITURE:
			$Maison->MindRessource(QuelTypeRessource($arRessource[0]), $arRessource[1]);
			break;
		case personnage::TYPE_RES_MONNAIE:
			$Joueur->MindOr($arRessource[1]);
			break;
		case personnage::TYPE_COMPETENCE:
		case quete::TYPE_QUETE:
			break;
		default:
			$Joueur->CleanInventaire($arRessource[0], false, $arRessource[1]);
			break;
	}
}
function CheckIfAssezRessource(array $arRessource, personnage &$Joueur, maison &$Maison = NULL){
	switch(QuelTypeObjet($arRessource[0])){
		case maison::TYPE_RES_NOURRITURE:
		case maison::TYPE_RES_EAU_POTABLE:
			if(!is_null($Maison))
			{
				return ($Maison->GetRessource(QuelTypeRessource($arRessource[0])) >= $arRessource[1]);
			}
			//return $Joueur->AssezElementDansBolga($arRessource[0], $arRessource[1]);
			break;
		case personnage::TYPE_RES_MONNAIE:
			return ($Joueur->GetArgent() >= $arRessource[1]);
			break;
		case personnage::TYPE_COMPETENCE:
			return $Joueur->CheckCompetence($arRessource[0]);
			break;
		case personnage::TYPE_EXPERIENCE:
			return ($Joueur->GetExpPerso() >= $arRessource[1]);
			break;
		case quete::TYPE_QUETE:
			return $Joueur->CheckIfQueteTerminee($arRessource[1]);
		default:
			return $Joueur->AssezElementDansBolga($arRessource[0], $arRessource[1]);
			break;
	}
	return false;
}

/**
 * Vérifie si on se trouve bien sur un bâtiment donnée par $NumBatiment
 * @param integer $NumBatiment <p>ID_type du batiment</p>
 * @param string $position <p>Coordonnée complète de la position à vérifer</p>
 * @return boolean
 */
function CheckIfOnEstSurUnBatiment($NumBatiment, $position){
	//pour vérifier si on est sur un batiment X ou non
	if(is_null(FoundBatiment($NumBatiment, NULL, $position))){
		return false;
	}

	return true;
}

/**
 * Retourne le type d'objet correspondant au $Code
 * @param string $Code
 * @return string
 */
function QuelTypeObjet(&$Code){
	/* $Ressource = QuelTypeRessource($Code);
	if(!is_null($Ressource))
	{
		return $Ressource;
	} */
	/* if($Ressource != $Code)
	{
		return $Ressource;
	} */

	//On transforme le lingot généric par le lingot romain ou gaulois
	if(substr($Code, 0, 4) === 'LING')
	{
		$sql = "SELECT civilisation FROM table_joueurs WHERE `login`='".$_SESSION['joueur']."'";
		$requete = mysql_query($sql) or die(mysql_error() . $sql);
		if (mysql_num_rows($requete) > 0)
		{
			$row = mysql_fetch_array($requete, MYSQL_ASSOC);
			$Code = strtolower(substr($row['civilisation'], 0, 1)).$Code;
		}
	}
	
	//on vérifie si c'est une ressource
	Global $lstRessources;
	if(in_array($Code, $lstRessources))
	{
		return $Code;
	}
	
	//on vérifie les type de quêtes
	switch ($Code)
	{
		case quete::TYPE_QUETE:
		case qteBatiment::TYPE_QUETE_BATIMENT:
		case qteCombat::TYPE_QUETE_MONSTRE:
		case personnage::TYPE_EXPERIENCE:
		case quete::TYPE_QUETE:
			return $Code;
	}
	
	switch (substr($Code, 0, 5))
	{
		case 'ResVi':
		case 'PotVi': return objDivers::TYPE_RES_VIE;
		case 'ResDe':
		case 'PotDe': return objDivers::TYPE_RES_DEP;
	}
	
	if(substr($Code, 0, 3) == 'cmp')
	{
		return personnage::TYPE_COMPETENCE;
	}

	return 'Divers';
}
function QuelTypeRessource(&$Code) {
	switch (strtolower($Code))
	{
		case 'nourriture':									return maison::TYPE_RES_NOURRITURE;
		case 'eau':											return maison::TYPE_RES_EAU_POTABLE;
		case 'monnaie':										return personnage::TYPE_RES_MONNAIE;
		case strtolower(personnage::TYPE_RES_MONNAIE):		return personnage::TYPE_RES_MONNAIE;
	}
	return NULL;
}
/**
 * Retourne la liste des cases libres d'une carte X ($carte = X) ou toutes les cartes ($carte = NULL)
 * @param string $carte <p>Default value : NULL</p>
 * @return array
 */
function FreeCaseCarte($carte = NULL) {
	$sql = "SELECT coordonnee FROM table_carte WHERE detruit IS NULL AND id_type_batiment NOT IN (11);";
	$requete = mysql_query($sql) or die(mysql_error() . $sql);
	while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
		$arCoordonnee = explode(',', $row['coordonnee']);
		if (is_null($carte) OR $arCoordonnee['0'] == $carte) {
			$arBusy[$arCoordonnee['0']][$arCoordonnee['1']][$arCoordonnee['2']] = true;
		}
	}

	global $nbLigneCarte, $nbColonneCarte;
	
	$arCartes = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y');

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
/**
 * Retourne NULL ou la liste des membres du $Village
 * @param string $Village
 * @return array|NULL
 */
function ListMembreVillage($Village){
	//$lstMembre = null;
	if (!is_null($Village))
	{
		$sql = "SELECT * FROM table_villages WHERE villages_nom='".htmlspecialchars($Village, ENT_QUOTES)."';";
		$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);

		if(mysql_num_rows($requete) > 0)
		{
			while ($row = mysql_fetch_array($requete, MYSQL_ASSOC))
			{
				$lstMembre[] = $row['villages_citoyen'];
			}
		}
		
	}else{
		$lstMembre[] = $_SESSION['joueur'];
	}
	if(isset($lstMembre))
	{
		return $lstMembre;
	}
	
	return NULL;
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
	$sqlCmp = "SELECT * FROM table_competence WHERE cmp_login='" . $oJoueur->GetLogin() . "' AND cmp_finish IS NULL";
	$rqtCmp = mysql_query($sqlCmp) or die(mysql_error() . '<br />' . $sqlCmp);
	
	while ($cmp = mysql_fetch_array($rqtCmp, MYSQL_ASSOC))
	{
		if ((strtotime('now') - strtotime($cmp['cmp_date'])) >= $cmp['cmp_temp'])
		{
			$sql = "UPDATE  `table_competence` SET  `cmp_finish` =  TRUE WHERE `table_competence`.`cmp_id` =" . $cmp['cmp_id'] . ";";
			mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
			
			$oJoueur->UpdatePoints(personnage::POINT_COMPETENCE_TERMINE);
			
			AddHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'Competence', NULL, NULL, 'Compétence terminée');
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
/**
 * Requete SQL sur table+competence+lst.
 * Retourne 1 ou toutes les infos d'une compétence donnée par son code.
 * @param string $code <p>Code la compétence</p>
 * @param string $info <p>Si on spécifie le nom du champ de la table, uniquement cette info sera retournée. Si non, une array avec toutes les infos. Default = NULL.</p>
 * @return multitype:|Ambigous <>|NULL
 */
function GetInfoCompetence($code, $info = NULL){
	$sql = "SELECT * FROM table_competence_lst WHERE cmp_lst_code='".$code."';";
	$rqtCmp = mysql_query($sql) or die ( mysql_error() );
	
	if(mysql_num_rows($rqtCmp) > 0){
		if(is_null($info)){
			return mysql_fetch_array($rqtCmp, MYSQL_ASSOC);
		}else{
			$temp = mysql_fetch_array($rqtCmp, MYSQL_ASSOC);
			return $temp[$info];
		}
	}
	
	return null;
}
/**
 * Retourne des info sur la carrière donnée par son code
 * @param string $code <p>Le code de la carrière voulue</p>
 * @param string $info [optional]<p>Nom du champ spécifique voulu.</p>
 * @return <p>Retourne la valeur du champs spécifié par $info ou <array> avec toutes les infos si $info n'est pas spécifié. Mais retourne NULL si la carrière n'est pas trouvée.</p>
 */
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
	}
	return null;
}
/**
 * Vérifie si pour une liste d'objets, cout et autre, il y a assez pour utiliser.
 * @param array $lstPrix <p>array(string (code=nb), string(code=nb))</p>
 * @param personnage $oJoueur
 * @param maison $maison
 * @return boolean
 */
function CheckCout($lstPrix, personnage &$oJoueur, maison &$maison = NULL){
	if(!is_null($lstPrix))
	{
		foreach($lstPrix as $Prix)
		{
			if(!CheckIfAssezRessource(explode('=', $Prix), $oJoueur, $maison))
			{
				return false;
			}
		}
	}
	
	
	return true;
}

//+---------------------------------+
//|				ACTIONS				|
//+---------------------------------+
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
function ActionUtiliser(&$check, &$code = null, personnage &$oJoueur, &$objManager, $Qte = 1){
	if(!is_null($code)){
		if(!isset($_GET['page']) OR $_GET['page'] == 'main'){
			$oJoueur->AddInventaire($code);
		}

		$objObjet = FoundObjet($code);
		
		//$maison = $oJoueur->GetObjSaMaison();
	
		for($i=1; $i <= $Qte; $i++)
		{
			foreach($objObjet->GetRessource() as $Ressource)
			{
				$arTmp = explode('=', $Ressource);
				
				switch(QuelTypeObjet($arTmp[0])){
					case objDivers::TYPE_RES_VIE:
						if(($oJoueur->GetVie() + $arTmp[1]) <= personnage::VIE_MAX)
						{
							$oJoueur->GagnerVie($arTmp[1]);
						}else{
							break(3);
						}
						break;
					case objDivers::TYPE_RES_DEP:
						if(($oJoueur->GetDepDispo() + $arTmp[1]) <= personnage::DEPLACEMENT_MAX)
						{
							$oJoueur->AddDeplacement($arTmp[1],'objet');
						}else{
							break(3);
						}
						break;
				}
			}
			
			$oJoueur->CleanInventaire($code);
		}
		

		/* if(!is_null($maison)){
			$objManager->UpdateBatiment($maison);
			unset($maison);
		} */

		unset($code);
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