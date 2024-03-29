<?php
function AfficheMouvements(personnage &$oJoueur) {
	if ($oJoueur->GetDepDispo() < personnage::DEPLACEMENT_MAX) {
		$txtDep = '
		<tr>
			<td colspan="3">
					Encore ' . $oJoueur->GetDepDispo() . ' d�placements
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<div style="display:inline;" id="TimeToWait"></div>
				<script type="text/JavaScript">CountDown(' . abs(personnage::TEMP_DEPLACEMENT_SUP - (strtotime('now') - $oJoueur->GetLastAction())) . ');</script>
			</td>
		</tr>';
	} else {
		$txtDep = '
		<tr>
			<td colspan="3">
				Maximum de ' . personnage::DEPLACEMENT_MAX . ' d�placements atteint
			</td>
		</tr>';
	}
	
	$txt = '
	<table class="mouvements">'
	. $txtDep
	. '<tr>
			<td style="border:none;">&nbsp;</td>
			<td>
				<button 
					type="button" 
					onclick="window.location=\'index.php?page=main&amp;move=up\'" '
	. ($oJoueur->CheckMove('up') ? '' : 'disabled="disabled"') . '
					class="mouvements">'
	. AfficheIcone(($oJoueur->CheckMove('up') ? 'up' : 'croix'))
	. '</button>
			</td>
			<td style="border:none;">&nbsp;</td>
		</tr>
		<tr>
			<td>
				<button 
					type="button" 
					onclick="window.location=\'index.php?page=main&amp;move=left\'" '
	. ($oJoueur->CheckMove('left') ? '' : 'disabled="disabled"') . '
					class="mouvements">'
	. AfficheIcone(($oJoueur->CheckMove('left') ? 'left' : 'croix'))
	. '</button>
			</td>
			<td>&nbsp;</td>
			<td>
				<button 
					type="button" 
					onclick="window.location=\'index.php?page=main&amp;move=right\'" '
	. ($oJoueur->CheckMove('right') ? '' : 'disabled="disabled"') . '
					class="mouvements">'
	. AfficheIcone(($oJoueur->CheckMove('right') ? 'right' : 'croix'))
	. '</button>
			</td>
		</tr>
		<tr>
			<td style="border:none;">&nbsp;</td>
			<td>
				<button 
					type="button" 
					onclick="window.location=\'index.php?page=main&amp;move=down\'" '
	. ($oJoueur->CheckMove('down') ? '' : 'disabled="disabled"') . '
					class="mouvements">'
	. AfficheIcone(($oJoueur->CheckMove('down') ? 'down' : 'croix'))
	. '</button>
			</td>
			<td style="border:none;">&nbsp;</td>
		</tr>
	</table>';
	return $txt;
}
function AfficheActions(personnage &$oJoueur, maison &$oMaison = NULL) {
	global $retour_combat, $arTailleCarte;

	$LstQueteAccessible = null;
	$position = $oJoueur->GetPosition();

	//===  Partie pour afficher les combats possible  ===
	if (isset($_SESSION['message']) AND !is_null($_SESSION['message'])) {
		foreach ($_SESSION['message'] as $Message) {
			echo $Message . '<hr />';
		}
	}

	//===  Partie pour afficher un objet trouv�  ===
	echo AfficheObjetTrouveDansMenuAction($oJoueur);

	//===  Affichage de collecte de ressource
	echo AfficheCollecteRessource($oJoueur, $oMaison);

	//===  Affichage du gibier apport�e de tir  ===
	echo AfficheGibierAChasser($oJoueur, $oMaison);
	
	//=== Affichage de l'action "Vider stock" si plein et sur la case
	echo AfficheActionViderStock($oJoueur);
	
	//=== Affiche la redirection vers son batiment
	echo AfficheRedirectionBatiment($oJoueur);
	
	//===  On cr�e la liste des choses dans les environs  ===
	if ($oJoueur->GetMaisonInstalle()) {
		if (!is_null($oJoueur->GetArme())) {
			//Si on a une arme, on v�rifie si c'est une arme de jet.
			$sql_arme = "SELECT objet_distance FROM table_objets WHERE objet_code='" . strval($oJoueur->GetArme()) . "';";
			$requete = mysql_query($sql_arme) or die(mysql_error() . '<br />' . $sql_arme);
			$result = mysql_fetch_array($requete, MYSQL_ASSOC);
			if ($result['objet_distance'] != 0) {
				
					//La position active
				$arSQLPosition[] = $oJoueur->GetCoordonnee();
				if (isset($_SESSION['QueteEnCours'])) {
					CheckQueteAccessible($LstQueteAccessible, $oJoueur->GetCoordonnee());
				}

				$chkDirection = array('VH' => true, 'VB' => true, 'HG' => true, 'HD' => true, 'OHG' => true, 'OHD' => true, 'OBG' => true, 'OBD' => true);

				for ($i = 1; $i <= $result['objet_distance']; $i++) {
					//la direction verticale haut
					if (($position['1'] - $i) < 0) {
						$chkDirection['VH'] = false;
					}
					if ($chkDirection['VH']) {
						$tmpPosition = implode(',', array($oJoueur->GetCarte(), $position['0'], ($position['1'] - $i)));
						$arSQLPosition[] = $tmpPosition;
						if (isset($_SESSION['QueteEnCours'])) {
							CheckQueteAccessible($LstQueteAccessible, $tmpPosition);
						}
					}
					//la direction verticale bas
					if (($position['1'] + $i) > $arTailleCarte['NbLigne']) {
						$chkDirection['VB'] = false;
					}
					if ($chkDirection['VB']) {
						$tmpPosition = implode(',', array($oJoueur->GetCarte(), $position['0'], ($position['1'] + $i)));
						$arSQLPosition[] = $tmpPosition;
						if (isset($_SESSION['QueteEnCours'])) {
							CheckQueteAccessible($LstQueteAccessible, $tmpPosition);
						}
					}
					//la direction horizontale gauche
					if (($position['0'] - $i) < 0) {
						$chkDirection['HG'] = false;
					}
					if ($chkDirection['HG']) {
						$tmpPosition = implode(',', array($oJoueur->GetCarte(), ($position['0'] - $i), $position['1']));
						$arSQLPosition[] = $tmpPosition;
						if (isset($_SESSION['QueteEnCours'])) {
							CheckQueteAccessible($LstQueteAccessible, $tmpPosition);
						}
					}
					//la direction horizontale droite
					if (($position['0'] + $i) > $arTailleCarte['NbColonne']) {
						$chkDirection['HD'] = false;
					}
					if ($chkDirection['HD']) {
						$tmpPosition = implode(',', array($oJoueur->GetCarte(), ($position['0'] + $i), $position['1']));
						$arSQLPosition[] = $tmpPosition;
						if (isset($_SESSION['QueteEnCours'])) {
							CheckQueteAccessible($LstQueteAccessible, $tmpPosition);
						}
					}
					//la direction oblique HG
					if (($position['0'] - $i) < 0 or ($position['1'] - $i) < 0) {
						$chkDirection['OHG'] = false;
					}
					if ($chkDirection['OHG']) {
						$tmpPosition = implode(',', array($oJoueur->GetCarte(), ($position['0'] - $i), ($position['1'] - $i)));
						$arSQLPosition[] = $tmpPosition;
						if (isset($_SESSION['QueteEnCours'])) {
							CheckQueteAccessible($LstQueteAccessible, $tmpPosition);
						}
					}
					//la direction oblique HD
					if (($position['0'] - $i) < 0 or ($position['1'] + $i) > $arTailleCarte['NbColonne']) {
						$chkDirection['OHD'] = false;
					}
					if ($chkDirection['OHD']) {
						$tmpPosition = implode(',', array($oJoueur->GetCarte(), ($position['0'] - $i), ($position['1'] + $i)));
						$arSQLPosition[] = $tmpPosition;
						if (isset($_SESSION['QueteEnCours'])) {
							CheckQueteAccessible($LstQueteAccessible, $tmpPosition);
						}
					}
					//la direction oblique BG
					if (($position['0'] + $i) > $arTailleCarte['NbLigne'] or ($position['1'] - $i) < 0) {
						$chkDirection['OBG'] = false;
					}
					if ($chkDirection['OBG']) {
						$tmpPosition = implode(',', array($oJoueur->GetCarte(), ($position['0'] + $i), ($position['1'] - $i)));
						$arSQLPosition[] = $tmpPosition;
						if (isset($_SESSION['QueteEnCours'])) {
							CheckQueteAccessible($LstQueteAccessible, $tmpPosition);
						}
					}
					//la direction oblique BD
					if (($position['0'] + $i) > $arTailleCarte['NbLigne'] or ($position['1'] + $i) > $arTailleCarte['NbColonne']) {
						$chkDirection['OBD'] = false;
					}
					if ($chkDirection['OBD']) {
						$tmpPosition = implode(',', array($oJoueur->GetCarte(), ($position['0'] + $i), ($position['1'] + $i)));
						$arSQLPosition[] = $tmpPosition;
						if (isset($_SESSION['QueteEnCours'])) {
							CheckQueteAccessible($LstQueteAccessible, $tmpPosition);
						}
					}
				}
			} else {
				$arSQLPosition[] = $oJoueur->GetCoordonnee();
				if (isset($_SESSION['QueteEnCours'])) {
					CheckQueteAccessible($LstQueteAccessible, $oJoueur->GetCoordonnee());
				}
			}
		} else {
			$arSQLPosition[] = $oJoueur->GetCoordonnee();
			if (isset($_SESSION['QueteEnCours'])) {
				CheckQueteAccessible($LstQueteAccessible, $oJoueur->GetCoordonnee());
			}
		}

		//=== On affiche le choix des ennemis attaquables
		$sql = "SELECT * FROM table_joueurs WHERE 
		login NOT IN ('".implode("', '", array_merge(ListeMembreClan($oJoueur->GetClan()), ListMembreVillage($oJoueur->GetVillage())))."') 
		AND position IN('".implode("', '", $arSQLPosition)."');";
		echo AfficheListeEnnemisAFrapper($oJoueur, $sql);

		//=== On affiche la liste des batiment attaquables
		$SQLCarte = "SELECT * FROM table_carte WHERE detruit IS NULL 
		AND login NOT IN ('".implode("', '", array_merge(ListeMembreClan($oJoueur->GetClan()), ListMembreVillage($oJoueur->GetVillage())))."') 
		AND coordonnee IN ('".implode("', '", $arSQLPosition)."');";
		echo AfficheListeBatimentAttaquable($SQLCarte, $chkConstruction = true);

		//=== On affiche le l�gionnaire
		echo AfficheAttaqueBrigand($oJoueur);
	} else {   //la Maison n'est pas encore install�
		echo '
		<p>Votre Maison n\'est pas encore install�e. Installez la o� vous voulez et ensuite vous pourrez attaquer d\'autre gaulois.</p>
		<hr />';
	}
	//===  Partie pour afficher La possibilit� de construction  ===
	echo AfficheMenuConstruction($oJoueur, $chkConstruction, $oMaison);
	//===  Partie pour afficher les monstres des quetes  ===
	echo AfficheQueteAPorteeDeTire($LstQueteAccessible);
}
function AfficheRedirectionBatiment(personnage &$oJoueur){
	$batiment = FoundBatiment(NULL, $oJoueur->GetLogin(), $oJoueur->GetCoordonnee());
	if(!is_null($batiment) and get_class($batiment) != 'ressource'){
		return '<p>Allez � votre '
					.'<a href="index.php?page=village&amp;anchor='.str_replace(',', '_', $batiment->GetCoordonnee()).'">'
						.strtolower(get_class($batiment))
						.' <img src="img/carte/'.strtolower(get_class($batiment)).'-a.png" alt="'.strtolower(get_class($batiment)).'" title="Votre '.strtolower(get_class($batiment)).'" height="20px" />'
					.'</a>'
				.' pour voir les options possibles</p>';
	}else{
		return null;
	}
}
function AfficheActionViderStock(personnage &$oJoueur){
	$batiment = FoundBatiment(NULL, $oJoueur->GetLogin(), $oJoueur->GetCoordonnee());
	if(	!is_null($batiment)
		AND in_array($batiment->GetIDType(), array(mine::ID_BATIMENT, ferme::ID_BATIMENT, carriere::ID_BATIMENT, potager::ID_BATIMENT/* , scierie::ID_BATIMENT */))
		AND $batiment->GetStockContenu() == $batiment->GetStockMax()){
		
		$_SESSION['main'][get_class($batiment)]['vider'] = $batiment->GetStockContenu();
		
		return '<p>Votre stock de la '.strtolower(get_class($batiment))
				.'<img src="img/carte/'.strtolower(get_class($batiment)).'-a.png" alt="'.strtolower(get_class($batiment)).'" title="Votre '.strtolower(get_class($batiment)).'" height="20px" />'
				.' est plein ('.$batiment->GetStockContenu().'x '.AfficheIcone($batiment->GetCodeRessource($batiment->GetTypeContenu())).'): 
				<a href="index.php?page=main&amp;action=viderstock'.strtolower(get_class($batiment)).'">Vider</a> ou 
				<a href="index.php?page=village&amp;anchor='.str_replace(',', '_', $batiment->GetCoordonnee()).'">Changer de production</a></p>';
	}else{
		return null;
	}
}
function AfficheObjetTrouveDansMenuAction(personnage &$oJoueur) {
	$DebugMode = false;
	$txt = null;
	if (!$oJoueur->GetChkObject()) {
		
		$objObjet = FoundObjet(ObjetTrouve($oJoueur));
		
		if ($DebugMode) {
			var_dump($objObjet);
		}
		
		if (is_null($objObjet))
		{
			$oJoueur->SetLastObject(true, null);
		} else {
			
			$oJoueur->SetLastObject(true, $objObjet->GetCode());
			$txt .= AfficheActionPossible($oJoueur, $objObjet);
			
		}
	} else {
		if (!is_null($oJoueur->GetLastObject()))
		{
			$objObjet = FoundObjet($oJoueur->GetLastObject());
			
			$txt .= AfficheActionPossible($oJoueur, $objObjet);
		} else {
			$oJoueur->SetLastObject(true, null);
		}
	}
	return (is_null($txt) ? $txt : $txt . '<hr />');
}
function AfficheCollecteRessource(personnage &$oJoueur, maison &$oMaison = NULL) {
	//on v�rifie si on a d�ja install� sa maison
	if (is_null($oJoueur->GetMaisonInstalle())) {
		return null;
	}

	//On v�rifie si la case est vide, si oui --> Finis
	if (ChkIfFree($oJoueur->GetCoordonnee())) {
		return null;
	}
	
	$objRessource = FoundBatiment(NULL, NULL, $oJoueur->GetCoordonnee());
	
	if(is_null($objRessource) OR (get_class($objRessource) AND get_class($objRessource) != 'ressource'))
	{
		return NULL;
	}
	//Dans quel etat est la ressource?

	if (is_null($objRessource->GetCollecteur()))
	{
		//Si la ressource est libre
		if (!is_null($objRessource->GetCompetenceRequise())
			AND !$oJoueur->CheckCompetence($objRessource->GetCompetenceRequise()))
		{
			$txt = '<p>Vous devez avoir une comp�tence <u>'. GetInfoCompetence($objRessource->GetCompetenceRequise(), 'cmp_lst_nom').'</u> pour ramasser ' . $objRessource->GetTextRessource() . '</p><hr />';
		} else {
			if(	CheckIfAssezRessource(array($objRessource->GetCodeRessource(ressource::TYPE_NORMAL), 1), $oJoueur, $oMaison)
				OR count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga())
			{
				$txt = '<p style="text-align:center;">'
				. '<a href="index.php?page=main&amp;action=ressource&amp;id='.ressource::TYPE_NORMAL.'">'
				. 'Collecter ' . $objRessource->GetQuantiteCollecte($oJoueur->GetLastCompetenceFinish($oJoueur->GetTypeCompetence($objRessource->GetCompetenceRequise()))) . ' ' . AfficheIcone(strtolower($objRessource->GetCodeRessource()))
				. '</a>'
				. '</p>';
			}else{
				$txt = '<p>Vous n\'avez pas assez de place dans votre bolga pour commencer � r�colter cette ressource. Allez videz un peu votre Bolga et revenez.</p>';
			}
				//Si on est sur une ressource de pierre, on peut collecter aussi de l'or
				if ($objRessource->GetNomType() == ressource::NOM_RESSOURCE_PIERRE
				AND $oJoueur->CheckCompetence(ressource::COMPETENCE_POUR_OR))
				{
					if(	CheckIfAssezRessource(array($objRessource->GetCodeRessource(ressource::TYPE_NORMAL), 1), $oJoueur, $oMaison)
						OR count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga())
					{
						$txt .= '<p style="text-align:center;">'
							. '<a href="index.php?page=main&amp;action=ressource&amp;id='.ressource::TYPE_OR.'">'
							. 'Collecter ' . $objRessource->GetQuantiteCollecte($oJoueur->GetLastCompetenceFinish($oJoueur->GetTypeCompetence($objRessource->GetCompetenceRequise(ressource::TYPE_OR))), ressource::TYPE_OR) . ' ' . AfficheIcone(strtolower($objRessource->GetCodeRessource(ressource::TYPE_OR)))
							. '</a>'
							. '</p>';
					}else{
						$txt = '<p>Vous n\'avez pas assez de place dans votre bolga pour commencer � r�colter cette ressource. Allez videz un peu votre Bolga et revenez.</p>';
					}
				}
				$txt .= '<hr />';
		}
	} else {
		
		if ((strtotime('now') - $objRessource->GetDateDebutAction()) >= $objRessource->GetTempRessource())
		{
			$txt = '<script type="text/javascript">window.location=\'index.php?page=main&action=ressource\';</script>';
		} elseif ($objRessource->GetCollecteur() == $oJoueur->GetLogin())
		{
			$txt = '<p style="display:inline;">Vous �tes en train de collecter ' . $objRessource->GetTextRessource() . ' ' . AfficheIcone(strtolower($objRessource->GetCodeRessource($objRessource->GetTypeContenu()))) . '. Vous en avez encore pour :</p>
				<div style="display:inline;" id="TimeToWaitRessource"></div><p style="display:inline;"> N\'interrompez pas votre collecte sinon ce sera perdu.</p>'
			. AfficheCompteurTemp('Ressource', 'index.php?page=main&action=ressource', ($objRessource->GetTempRessource() - (strtotime('now') - $objRessource->GetDateDebutAction())))
			//. AfficheCompteurTemp('Ressource', 'index.php', ($objRessource->GetTempRessource() - (strtotime('now') - $objRessource->GetDateDebutAction())))
			. '<hr />';
		} else {
			$txt = '<p style="display:inline;">La ressource est en cours d\'utilisation par ' . $objRessource->GetCollecteur() . ' pour encore :</p>
				<div style="display:inline;" id="TimeToWaitRessource"></div>'
			. AfficheCompteurTemp('Ressource', 'index.php?page=main&action=ressource', ($objRessource->GetTempRessource() - (strtotime('now') - $objRessource->GetDateDebutAction())))
			//. AfficheCompteurTemp('Ressource', 'index.php', ($objRessource->GetTempRessource() - (strtotime('now') - $objRessource->GetDateDebutAction())))
			. '<hr />';
		}
	}
	
	/* Global $objManager;
	$objManager->UpdateBatiment($objRessource);
	unset($objRessource); */
	
	return $txt;
}
function ChkIfFree($position) {
	$sql = "SELECT id_case_carte FROM table_carte WHERE coordonnee='".$position."' AND detruit IS NULL;";
	
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	
	if (mysql_num_rows($requete) > 0) {
		return false;
	}

	return true;
}
function AfficheGibierAChasser(personnage &$oJoueur, maison &$oMaison = NULL) {
	$txt = null;
	if(!$oJoueur->GetChkChasse())
	{
		$oGibier = GibierTrouve($oJoueur);
		
		if (!is_null($oGibier))
		{
			//$oGibier = FoundGibier($codeGibier);
			
			if ($oJoueur->CheckCompetence($oGibier->GetCompetence()))
			{
				/* $_SESSION['chasser']['nourriture'] = $Gibier->GetGainNourriture($oJoueur->GetNiveauCompetence(Gibier::TYPE_CMP_NOUR));
				$_SESSION['chasser']['cuir'] = $Gibier->GetGainCuir($oJoueur->GetNiveauCompetence(Gibier::TYPE_CMP_CUIR));
				$_SESSION['chasser']['attaque'] = $Gibier->GetAttaque(); */
				
				$_SESSION['chasser'] = $oGibier->GetCode();
				
				$oJoueur->SetChasse(true);
				
				return '<p>Voulez-vous chasser du ' . $oGibier->GetNom() . '? Gain de ' . AfficheListePrix($oGibier->GetRessource()).(!is_null($oGibier->GetAttaque())?' mais '.$oGibier->GetAttaque().' de force':NULL).'.'
						. '<ul style="display:inline; list-style-type:none; padding:0px; text-align:center;">'
						. ((CheckIfAssezRessource(array($oGibier->GetCode(), 1), $oJoueur, $oMaison)
							OR count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga())?
								'<li style="display:inline; margin-left:20px;"><a href="index.php?page=main&amp;action=chasser">Chasser</a></li>'
								:'<li style="display:inline; margin-left:20px;">Votre Bolga est plein.</li>')
						. '<li style="display:inline; margin-left:20px;"><a href="index.php?page=main&amp;action=laisser&amp;type=chasser">Laisser</a></li>'
						. '</ul>'
						. '</p>'
						. '<hr />';
			}else{
				return 'Domage que vous n\'ayez pas une comp�tence de "'.Gibier::TYPE_CMP_MAIN.'". Vous auriez pu chasser '.$oGibier->GetNom().'.';
			}
		}
	}
	return NULL;
}
function AfficheListeEnnemisAFrapper(personnage &$oJoueur, $sql) {
	global $temp_combat;
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	$chkEnnemis = true;
	$chkEnnemisEnd = false;
	$txt = null;
	if (mysql_num_rows($requete) > 0) {
		$nbEnnemis = 0;
		while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
			if ($row['login'] != $oJoueur->GetLogin()) {
				if (isset($_SESSION['retour_combat'])) {
					for ($i = 0; $i <= count($_SESSION['retour_combat']); $i = $i + 2) {
						if (isset($_SESSION['retour_combat'][$i])) {
							if ($_SESSION['retour_combat'][$i] == $row['login']) {
								$txt .= '
		<p>Combat contre <b>' . $_SESSION['retour_combat'][$i] . '</b> : ' . $_SESSION['retour_combat'][$i + 1]['0'] . '</p>';
							}
						}
					}
				}
				if ((strtotime('now') - strtotime($row['date_last_combat'])) > $temp_combat
				and abs($row['niveau'] - $oJoueur->GetNiveau()) <= 2) {
					if (!isset($_SESSION['retour_combat']) or !in_array($row['login'], $_SESSION['retour_combat'])) {
						if ($chkEnnemis) {
							$txt .= '
		<p>Vous pouvez attaquer les joueurs suivants :
			<ul>';
							$chkEnnemis = false;
						}
						$_SESSION['main']['frapper'][$nbEnnemis] = $row['id'];
						$InfoBulle = '<table class="InfoBulle">'
							.'<tr><th colspan="2">'.$row['login'].' ('.ucfirst(GetInfoCarriere($row['carriere'], 'carriere_nom')).')</th></tr>'
							.'<tr><td colspan="2"><img alt="'.$row['login'].'" src="./fct/fct_image.php?type=VieCarte&amp;value='.$row['vie'].'&amp;max=300" /></td></tr>'
							.'<tr>'
							.'<td><img src="img/icones/ic_attaque.png" alt="Attaque" height="20px" /> : '.$row['val_attaque'].'</td>'
							.'<td><img src="img/icones/ic_defense.png" alt="D�fense" height="20px" /> : '.$row['val_defense'].'</td>'
							.'</tr>'
							.'</table>';
						$txt .= '
				<li><a href="index.php?page=main&amp;action=frapper&amp;id=' . $nbEnnemis . '">Attaquer <img src="img/carte/'.$row['civilisation'].'-b.png" alt="Ennemi" height="20px" onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" onmouseout="cache();" /> '.$row['login'].' ('.ucfirst(GetInfoCarriere($row['carriere'], 'carriere_nom')).')</a></li>';
						$chkEnnemisEnd = true;
						$nbEnnemis++;
					}
				}
			}
		}
		if ($chkEnnemisEnd) {
			$txt .= '
			</ul>
		</p>
		<hr />';
		}
	}
	return $txt;
}
function AfficheListeBatimentAttaquable($sql, &$chkConstruction) {
	global $temp_combat;
	$txt = null;
	$chkBatiment = true;
	$chkBatimentsEnd = false;
	
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	if (isset($_SESSION['retour_attaque'])) {
		$chkConstruction = false;
		for ($i = 0; $i <= count($_SESSION['retour_attaque']); $i = $i + 2) {
			if (isset($_SESSION['retour_attaque'][$i])) {
				$txt .= '
		<p>Attaque du batiment de <b>' . $_SESSION['retour_attaque'][$i] . '</b> : ' . $_SESSION['retour_attaque'][$i + 1]['0'] . '</p>';
			}
		}
		$txt .= '<hr />';
	}
	if (mysql_num_rows($requete) > 0) {
		$chkConstruction = false;   //on ne peut pas construire de batiment
		$nbBatiment = 0;
		while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
			$sqlBatiment = "SELECT * FROM table_batiment WHERE id_type=" . $row['id_type_batiment'] . " AND batiment_niveau=".$row['niveau_batiment'].";";
			$rqtBatiment = mysql_query($sqlBatiment) or die(mysql_error() . '<br />' . $sqlBatiment);
			$rstBatiment = mysql_fetch_array($rqtBatiment, MYSQL_ASSOC);

			if (!in_array($rstBatiment['batiment_type'], array('ressource', 'carte'))) {
				if ((strtotime('now') - strtotime($row['date_last_attaque'])) > $temp_combat) {
					if ($chkBatiment) {
						$txt .= '
		<p>Vous pouvez attaquer les batiment suivants : <i>nom (Etat)</i>
			<ul>';
						$chkBatiment = false;
					}

					$_SESSION['main']['attaquer'][$nbBatiment] = $row['coordonnee'];
					$InfoBulle =
						'<b>'.$rstBatiment['batiment_nom'].' de '.$row['login'].'</b>'
						.'<br />'
						.'<img alt="'.$rstBatiment['batiment_nom'].'" src="./fct/fct_image.php?type=VieCarte&amp;value='.$row['etat_batiment'].'&amp;max='.($rstBatiment['batiment_vie'] + (50 * $row['niveau_batiment'])).'" />';
					$txt .= '
				<li><a href="index.php?page=main&amp;action=attaquer&amp;id=' . $nbBatiment . '">Attaquer <img src="img/carte/'.strtolower($rstBatiment['batiment_nom']).'-b.png" alt="'.$rstBatiment['batiment_nom'].'" height="20px" onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" onmouseout="cache();" /></a></li>';
					$chkBatimentsEnd = true;
					$nbBatiment++;
				}
			}
		}
		if ($chkBatimentsEnd) {
			$txt .= '
				</ul>
			</p>
			<hr />';
		}
	}
	return $txt;
}
Function AfficheAttaqueBrigand(personnage &$oJoueur) {
	$sql = "SELECT * FROM table_objets 
			WHERE objet_type='Ennemi'";
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	$result = mysql_fetch_array($requete, MYSQL_ASSOC);
	
	$num = mt_rand(1, 100);
	if ($num == 50 AND !$oJoueur->GetChkLegionnaire()) {
		$_SESSION['main']['legionnaire'] = new Legionnaire($oJoueur->GetNiveau());
		//$_SESSION['main']['laisser'] = 'legionnaire';
		$oJoueur->SetLegionnaire(true);
		return '<p style="display:inline;">' . AfficheIcone('attention') . ' L�gion romaine en vue!<br />Voulez-vous l\'attaquer ?'
		. '<ul style="display:inline; list-style-type:none; padding:0px; text-align:center;">'
		. '<li style="display:inline;"><a style="margin-left:20px;" href="index.php?page=main&amp;action=legionnaire">Attaquer</a></li>'
		. '<li style="display:inline; margin-left:20px;"><a href="index.php?page=main&amp;action=laisser&amp;type=legionnaire">Laisser</a></li>'
		. '</ul>
			</p>
			<hr />';
	}
}
/* Function AfficheCombatLegionnaire(personnage &$oJoueur) {
	$num = mt_rand(1, 100);
	if ($num == 50 AND !$oJoueur->GetChkLegionnaire()) {
		$_SESSION['main']['legionnaire'] = new Legionnaire($oJoueur->GetNiveau());
		//$_SESSION['main']['laisser'] = 'legionnaire';
		$oJoueur->SetLegionnaire(true);
		return '<p style="display:inline;">' . AfficheIcone('attention') . ' L�gion romaine en vue!<br />Voulez-vous l\'attaquer ?'
		. '<ul style="display:inline; list-style-type:none; padding:0px; text-align:center;">'
		. '<li style="display:inline;"><a style="margin-left:20px;" href="index.php?page=main&amp;action=legionnaire">Attaquer</a></li>'
		. '<li style="display:inline; margin-left:20px;"><a href="index.php?page=main&amp;action=laisser&amp;type=legionnaire">Laisser</a></li>'
		. '</ul>
			</p>
			<hr />';
	}
} */
function AfficheMenuConstruction(personnage &$oJoueur, &$chkConstruction, maison &$maison = NULL) {
	if (is_null($oJoueur->GetMaisonInstalle())) {
		//La Maison n'est pas encore install�e
		
		if (!ChkIfFree($oJoueur->GetCoordonnee())) {
			
			return '
		<p>Ce terrain est d�j� occup�.</p>
		<hr />';
		} else {
			
			$_SESSION['main'][0]['construire'] = 1;
			$_SESSION['main'][0]['prix'] = NULL;
			
			return '
		<p><a href="index.php?page=main&amp;action=construire&amp;id=0">Construire ma Maison ici</a></p>
		<hr />';
		}
	} elseif (ChkIfFree($oJoueur->GetCoordonnee())) {
		
		//La Maison est install�e ET la case est vide.
		if ($chkConstruction AND MonVillageEstProche($oJoueur->GetCarte(), $oJoueur->GetPosition(), $oJoueur->GetLogin())) {
			
			global $lstNonBatiment, $lstBatimentsNonConstructible;
			
			$sqlBatiment = "SELECT * FROM table_batiment 
								WHERE id_type NOT IN (".implode(", ", array_merge($lstNonBatiment, $lstBatimentsNonConstructible)).")
									AND batiment_niveau=1;";
			$rqtBatiment = mysql_query($sqlBatiment) or die(mysql_error() . '<br />' . $sqlBatiment);
			
			$txt = null;
			$chkStart = true;
			$nbBatiment = 0;

			while ($row = mysql_fetch_array($rqtBatiment, MYSQL_ASSOC))
			{
				if (!ChkIfBatimentDejaConstruit($row['id_type']))
				{
					if ($chkStart)
					{
						$txt = '<p>Vous pouvez construire les batiments suivants :
						<ul>';
						$chkStart = false;
					}
					
					$txt .= '<li>"';
					
					if(is_null($row['batiment_prix']))
					{
						$LstPrix = NULL;
					}else{
						$LstPrix = explode(',', $row['batiment_prix']);
					}
					
					if (CheckCout($LstPrix, $oJoueur, $maison))
					{
						$_SESSION['main'][$nbBatiment]['construire'] = $row['id_type'];
						//$_SESSION['main'][$nbBatiment]['prix'] = $row['batiment_prix'];
						$_SESSION['main'][$nbBatiment]['prix'] = $LstPrix;
						$txt .= '<a href="index.php?page=main&amp;action=construire&amp;id=' . $nbBatiment . '">' . $row['batiment_nom'] . '</a>';
					} else {
						$txt .= $row['batiment_nom'];
					}
					
					$txt .= '" au prix de : ' . AfficheListePrix($LstPrix, $oJoueur, $maison) . '</li>';
					
					$nbBatiment++;
				}
			}
			if (!is_null($txt)) {
				return $txt . '</ul>
				</p>
				<hr />';
			} else {
				return '<p style="text-align:center;">Pas assez de ressources pour construire.</p><hr />';
			}
		} else {
			return null;
		}
	}
}
function AfficheQueteAPorteeDeTire(&$lstMonstre) {
	$txt = null;
	if (isset($lstMonstre)) {
		$txt .= '<p>Vous pouvez attaquer :
		<ul>';
		foreach ($lstMonstre as $Monstre) {
			$txt .= '<li><a href="index.php?page=main&amp;action=quete&amp;id=' . $Monstre['id'] . '">Attaquer ' . $Monstre['nom'] . '</a></li>';
		}
		$txt .= '</ul>
		</p>
		<hr />';
	}
	return $txt;
}
/**
 * Retourne le nombre de point perdu suite � une attaque de tour.
 * @param DBManage $db
 * @param personnage $oJoueur
 * @return number
 */
function AttaqueTour(DBManage &$db, personnage &$oJoueur){
	global $lstPoints;

	$ptsViePerduTour=0;
	$arDef = $oJoueur->GetDefPerso();
	
	$DefenseJoueur = intval($arDef['0'] + $arDef['1']);
	
	$sql = "SELECT coordonnee, login, niveau_batiment FROM table_carte WHERE login NOT IN('".implode("', '", ListeMembreClan($oJoueur->GetClan()))."') AND detruit IS NULL AND id_type_batiment=3;";
	//$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	$requete = $db->Query($sql);
	
	if(mysql_num_rows($requete) > 0){
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if($row['login'] == 'romain'){
				$distance = ($oJoueur->GetNiveau()>=10?3:2);
			}else{
				$distance = 2 + $row['niveau_batiment'];
			}
			$arTour = ZoneAttaqueTour($oJoueur->GetPosition(), $distance, $oJoueur->GetCarte());
			if(in_array($row['coordonnee'], $arTour)){
				if($row['login'] == 'romain'){
					$ptsDegat = (20 + (10 * ($oJoueur->GetNiveau()>15?15:$oJoueur->GetNiveau()))) - $DefenseJoueur;
				}else{
					$ptsDegat = (20 + (10 * $row['niveau_batiment'])) - $DefenseJoueur;
				}
				if($ptsDegat > 0){
					$oJoueur->PerdreVie($ptsDegat, 'tour');
					$oJoueur->UpdatePoints((abs(tour::POINT_TOUR_ATTAQUE) * -1));
					$ptsViePerduTour += $ptsDegat;
					$db->InsertHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'attaque', $row['login'], NULL, 'La Tour de '.$row['login'].' vous a attaqu� et bless� de '.$ptsDegat.'pts de vie.');
				}
			}
		}
	}
	return $ptsViePerduTour;
}
function ZoneAttaqueTour($position, $distance, $carte){
	global $arTailleCarte;
	$chkDirection = array('VH'=>true, 'VB'=>true, 'HG'=>true, 'HD'=>true, 'OHG'=>true, 'OHD'=>true, 'OBG'=>true, 'OBD'=>true);
	$lstCoordonnee[] = implode(',', array_merge(array($carte),$position));
	for($i=1;$i<=$distance;$i++){
		//la direction verticale haut
		if(($position['1']-$i)<0){$chkDirection['VH'] = false;}
		if($chkDirection['VH']){$lstCoordonnee[] = implode(',', array($carte, $position['0'], ($position['1'] - $i)));}
		//la direction verticale bas
		if(($position['1']+$i)>$arTailleCarte['NbLigne']){$chkDirection['VB'] = false;}
		if($chkDirection['VB']){$lstCoordonnee[] = implode(',', array($carte, $position['0'], ($position['1'] + $i)));}
		//la direction horizontale gauche
		if(($position['0']-$i)<0){$chkDirection['HG'] = false;}
		if($chkDirection['HG']){$lstCoordonnee[] = implode(',', array($carte, ($position['0'] - $i), $position['1']));}
		//la direction horizontale droite
		if(($position['0']+$i)>$arTailleCarte['NbColonne']){$chkDirection['HD'] = false;}
		if($chkDirection['HD']){$lstCoordonnee[] = implode(',', array($carte, ($position['0'] + $i), $position['1']));}
		//la direction oblique HG
		if(($position['0']-$i)<0 or ($position['1']-$i)<0){$chkDirection['OHG'] = false;}
		if($chkDirection['OHG']){$lstCoordonnee[] = implode(',', array($carte, ($position['0'] - $i), ($position['1'] - $i)));}
		//la direction oblique HD
		if(($position['0']-$i)<0 or ($position['1']+$i)>$arTailleCarte['NbColonne']){$chkDirection['OHD'] = false;}
		if($chkDirection['OHD']){$lstCoordonnee[] = implode(',', array($carte, ($position['0'] - $i), ($position['1'] + $i)));}
		//la direction oblique BG
		if(($position['0']+$i)>$arTailleCarte['NbLigne'] or ($position['1']-$i)<0){$chkDirection['OBG'] = false;}
		if($chkDirection['OBG']){$lstCoordonnee[] = implode(',', array($carte, ($position['0'] + $i), ($position['1'] - $i)));}
		//la direction oblique BD
		if(($position['0']+$i)>$arTailleCarte['NbLigne'] or ($position['1']+$i)>$arTailleCarte['NbColonne']){$chkDirection['OBD'] = false;}
		if($chkDirection['OBD']){$lstCoordonnee[] = implode(',', array($carte, ($position['0'] + $i), ($position['1'] + $i)));}
	}
	return $lstCoordonnee;
}
/**
 * S�lectionne les objets possible de trouver.
 * Cr�e une table de probabilit� de les trouv�s gr�ce � la valeur objet_rarete
 * Trouve un nombre entre 0 et exp(NiveauJoueur ou 5)
 * Si le num�ro existe dans la table, on renvoie le CodeObjet
 * @param personnage $oJoueur
 * @return string CodeObjet|NULL
 */
function ObjetTrouve(personnage &$oJoueur) {
	global $lstTypeObjets;
	$sqlObj = 	"SELECT objet_code, objet_niveau, objet_rarete 
				FROM table_objets 
				WHERE objet_civilisation IN ('".$oJoueur->GetCivilisation()."', 'all')
					AND objet_rarete > 0
					AND objet_type IN ('".implode("', '", $lstTypeObjets)."');";
	
	$requeteObj = mysql_query($sqlObj) or die(mysql_error() . '<br />' . $sqlObj);
	
	//on cr�e une table contenant les infos des objets
	unset($arObjets);
	
	while ($row = mysql_fetch_array($requeteObj, MYSQL_ASSOC))
	{
		for($i=1; $i <= $row['objet_rarete']; $i++)
		{
			//$arObjets[] = array('objet_code' => $row['objet_code'], 'objet_niveau' => $row['objet_niveau']);
			$arObjets[] = $row;
		}
	}
	//on prend un nombre al�atoire
	$num = mt_rand(0, count($arObjets) + intval(exp(($oJoueur->GetNiveau() > 5 ? 5 : $oJoueur->GetNiveau()))));
	
	if (isset($arObjets[$num])/*  AND $arObjets[$num]['objet_niveau'] <= $oJoueur->GetNiveau() */)
	{
		return $arObjets[$num]['objet_code'];
	}
	
	return null;
}
function AfficheActionPossible(personnage &$oJoueur, objMain &$objObjet) {

	$_SESSION['main']['objet']['code'] = $objObjet->GetCode();
	$Type = QuelTypeObjet($objObjet->GetCode());
	//$_SESSION['main']['objet']['value'] = $arData['objet_nb'];
	//$_SESSION['main']['objet']['action'] = true;
	$_SESSION['main']['objet']['laisser'] = 'ObjetTrouv�';

	$txtRamasser	= '<li style="display:inline;"><a href="index.php?page=main&amp;action=stock">Ramasser</a></li>';
	$txtUtiliser	= '<li style="display:inline; margin-left:20px;"><a href="index.php?page=main&amp;action=utiliser">Utiliser</a></li>';
	$txtLaisser		= '<li style="display:inline; margin-left:20px;"><a href="index.php?page=main&amp;action=laisser&amp;type=objet">Laisser</a></li>';
	$txtEquiper		= '<li style="display:inline; margin-left:20px;"><a href="index.php?page=main&amp;action=equiper">Equiper</a></li>';
	$txtPlein		= '<li style="display:inline;">Votre Bolga est plein.</li>';
	
	//$txt = <div;

	//$nbObjetDansSac = count($_SESSION['joueur']->GetLstInventaire());

	$txt = '<p>Vous avez trouv� l\'objet suivant : ' . $objObjet->GetNom().' '.AfficheIcone($objObjet->GetCode())
			.'<ul style="list-style-type:none; padding:0px; text-align:center;">';
	
	//on v�rifie si le bolga n'est pas plein
	if(	CheckIfAssezRessource(array($objObjet->GetCode(), 1), $oJoueur)
		OR count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga())
	{
		$txt .= $txtRamasser;
	}else{
		$txt .= $txtPlein;
	}
	
	//on v�rifie le type d'objet
	if (in_array($Type, array(objDivers::TYPE_RES_VIE, objDivers::TYPE_RES_DEP)))
	{
		if($Type == objDivers::TYPE_RES_VIE AND ($oJoueur->GetVie() + $objObjet->GetNb(objDivers::TYPE_RES_VIE)) > personnage::VIE_MAX)
		{
			$txt .= '<li style="display:inline; margin-left:20px;">Limite de ' . personnage::VIE_MAX . ' vie atteinte</li>';
		}elseif ($Type == objDivers::TYPE_RES_DEP AND ($oJoueur->GetDepDispo() + $objObjet->GetNb(objDivers::TYPE_RES_DEP)) > personnage::DEPLACEMENT_MAX)
		{
			$txt .= '<li style="display:inline; margin-left:20px;">Limite de ' . personnage::DEPLACEMENT_MAX . ' d�placements atteint</li>';
		}else{
			//on v�rifie si on a d�ja une maison ou pas
			$txt .= $txtUtiliser;
		}
		
	}

	//on affiche la possibilit� de laisser l'objet
	$txt .= $txtLaisser.'</ul></p>';

	return $txt;
}
/**
 * S�lectionne un gibier par sa raret�.
 * @param personnage $oJoueur
 * @return Gibier|NULL
 */
Function GibierTrouve(personnage &$oJoueur) {

	$sqlGibier = "SELECT objet_code, objet_rarete FROM table_objets WHERE objet_civilisation='gibier';";
	$rqtGibier = mysql_query($sqlGibier) or die(mysql_error() . '<br />' . $sqlGibier);

	//on cr�e une table contenant les infos des objets
	unset($arGibier);
	
	while ($row = mysql_fetch_array($rqtGibier, MYSQL_ASSOC)) {

		for($i=1; $i <= $row['objet_rarete']; $i++)
		{
			$arGibier[] = $row['objet_code'];
		}
	}
	
	//on prend un nombre al�atoire
	$num = mt_rand(0, count($arGibier) * 20);
	
	if (isset($arGibier[$num]))
	{
		$oGibier = FoundGibier($arGibier[$num]);
		
		if($oJoueur->CheckCompetence($oGibier->GetCompetence()))
		{
			return $oGibier;
		}
	}
	return null;
}
function MonVillageEstProche($carte, $position, $login) {
	global $arTailleCarte;
	$sql = "SELECT coordonnee, id_type_batiment FROM table_carte WHERE login='" . $login . "' AND detruit IS NULL;";
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	$lstCoordonnee[] = implode(',', array_merge(array($carte), $position));
	//la direction verticale haut
	if (($position['1'] - 1) >= 0) {
		$lstCoordonnee[] = implode(',', array($carte, $position['0'], ($position['1'] - 1)));
	}
	//la direction verticale bas
	if (($position['1'] + 1) <= $arTailleCarte['NbLigne']) {
		$lstCoordonnee[] = implode(',', array($carte, $position['0'], ($position['1'] + 1)));
	}
	//la direction horizontale gauche
	if (($position['0'] - 1) >= 0) {
		$lstCoordonnee[] = implode(',', array($carte, ($position['0'] - 1), $position['1']));
	}
	//la direction horizontale droite
	if (($position['0'] + 1) <= $arTailleCarte['NbColonne']) {
		$lstCoordonnee[] = implode(',', array($carte, ($position['0'] + 1), $position['1']));
	}
	//la direction oblique HG
	if (($position['0'] - 1) >= 0 or ($position['1'] - 1) >= 0) {
		$lstCoordonnee[] = implode(',', array($carte, ($position['0'] - 1), ($position['1'] - 1)));
	}
	//la direction oblique HD
	if (($position['0'] - 1) >= 0 or ($position['1'] + 1) <= $arTailleCarte['NbColonne']) {
		$lstCoordonnee[] = implode(',', array($carte, ($position['0'] - 1), ($position['1'] + 1)));
	}
	//la direction oblique BG
	if (($position['0'] + 1) <= $arTailleCarte['NbLigne'] or ($position['1'] - 1) >= 0) {
		$lstCoordonnee[] = implode(',', array($carte, ($position['0'] + 1), ($position['1'] - 1)));
	}
	//la direction oblique BD
	if (($position['0'] + 1) <= $arTailleCarte['NbLigne'] or ($position['1'] + 1) <= $arTailleCarte['NbColonne']) {
		$lstCoordonnee[] = implode(',', array($carte, ($position['0'] + 1), ($position['1'] + 1)));
	}

	while ($Coordonnee = mysql_fetch_array($requete, MYSQL_ASSOC)) {
		if (in_array($Coordonnee['coordonnee'], $lstCoordonnee)) {
			return true;
		}
	}
	return false;
}
function ChkIfBatimentDejaConstruit($IDBatiment) {
	global $lstBatimentConstructionUnique;
	if (in_array($IDBatiment, $lstBatimentConstructionUnique)) {
		$sql = "SELECT id_case_carte FROM table_carte WHERE id_type_batiment=$IDBatiment AND detruit IS NULL AND login='" . $_SESSION['joueur'] . "';";
		$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
		if (mysql_num_rows($requete) > 0) {
			return true;
		}
	}
	return false;
}
function AddCaseCarte($position, $login, $IDBatiment, $etat, $niveau){
	$sql="INSERT INTO `table_carte` (
	`id_case_carte`, 
	`coordonnee`, 
	`login`, 
	`id_type_batiment`, 
	`contenu_batiment`, 
	`res_nourriture`, `res_bois`, `res_pierre`, `res_eau`, 
	`date_action_batiment`, 
	`etat_batiment`, 
	`date_last_attaque`, 
	`detruit`, 
	`niveau_batiment`) VALUES (
	NULL, 
	'$position', 
	'$login', 
	$IDBatiment, "
	.(in_array($IDBatiment, array(ferme::ID_BATIMENT, mine::ID_BATIMENT, carriere::ID_BATIMENT, potager::ID_BATIMENT))?"'a1,0'":'NULL').", "
	.($IDBatiment == maison::ID_BATIMENT?"0, 0, 0, 0, ":"NULL, NULL, NULL, NULL, ")
	.(in_array($IDBatiment, array(ferme::ID_BATIMENT, mine::ID_BATIMENT, carriere::ID_BATIMENT, potager::ID_BATIMENT))?"'".date('Y-m-d H:i:s')."'":'NULL').", 
	$etat, 
	NULL, 
	NULL, 
	$niveau);";
	mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);
}
function NotificationMail($To, $type, $nom, $info) {
	$Sujet = 'Gaulix - ';
	switch ($type) {
		case 'attaque':
			$Sujet .= 'Attaque du batiment : ' . $nom;
			break;
		case 'combat':
			$Sujet .= 'Combat contre : ' . $nom;
			break;
	}

	$Message = '
	<html>
		<body>
			<p>Bonjour,</p>
			<p>Vous avez demand� d\'�tre inform� lors ' . ($type == 'attaque' ? 'd\'' : 'de ') . $type . '. Alors voici l\'information :</p>
			<div style="margin-left:50px; margin-right:50px; background:lightgrey;">"'
	. $info
	. '"</div>
			<p>Nous vous conseillons de vous connecter sur <a href="http://www.gaulix.be">www.gaulix.be</a> et de '
	. ($type == 'attaque' ? 'r�parer votre b�timent' : 'soigner votre personnage') . '.</p>'
	. '<p>A bientot,</p>
			<p style="margin-left:30px;">L\'�quipe Gaulix</p>
			<br />
			<p>PS : Si vous ne d�sirez plus recevoir ce genre de message, connectez-vous sur <a href="http://www.gaulix.be">www.gaulix.be</a>, allez dans les options et d�sactivez ce que vous voulez. Et pour supprimer votre joueur, c\'est au m�me endroit.</p>
		</body>
	</html>';

	$headers = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: Admin Gaulix<admin@gaulix.be>' . "\r\n";

	//echo $Message;
	mail($To, $Sujet, $Message, $headers);
}
function CheckQueteAccessible(&$lstMonster, $Position) {
	global $temp_combat;
	$arTmp = null;
	foreach ($_SESSION['QueteEnCours'] as $Quete)
	{
		$arPositionJoueur = explode(',', $Position);
		
		if(	($Quete->CheckQueteMonstre() OR $Quete->CheckQueteEnnemi())
			AND $arPositionJoueur[0] == $Quete->GetCarte())
		{
			$arPositionQuete = $Quete->GetPosition();

			if(	$arPositionQuete[0] == $arPositionJoueur[1]
				AND $arPositionQuete[1] == $arPositionJoueur[2]
				AND (strtotime('now') - $Quete->GetDateCombat()) > $temp_combat)
			{
				$lstMonster[] = array('id' => $Quete->GetIDQuete(), 'nom' => $Quete->GetNomEnnemi());
			}
		}
	}
}
function AfficheHistory(personnage &$oJoueur){
	$sql = "SELECT * FROM table_history WHERE
		history_login='".$oJoueur->GetLogin()."' 
		AND history_type IN ('combat', 'attaque', 'ressucite', 'quete', 'sort', 'voleur') 
		ORDER BY history_date DESC LIMIT 0, 5;";
	$requete = mysql_query($sql) or die (mysql_error());
	if(mysql_num_rows($requete) > 0){
		$txt = '
	<table class="history">
		<tr>
			<th>Date</th>
			<th>Position</th>
			<th>Contre</th>
			<th>Resultat</th>
		</tr>';
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			$txt .= '
		<tr>
			<td style="width:120px;">'.date('j M - G:i:s', strtotime($row['history_date'])).'</td>
			<td style="width:60px; text-align:center;">'.$row['history_position'].'</td>
			<td style="width:100px;">'.$row['history_adversaire'].'</td>
			<td style="width:500px;">'.html_entity_decode($row['history_info'], ENT_QUOTES).'</td>
		</tr>';
		}
		$txt .= '
	</table>';
	}else{$txt = '<p>Pas d\'historique</p>';
	}

	return $txt;
}

//+---------------------------------+
//|				ACTIONS				|
//+---------------------------------+
function ActionStock(&$check, personnage &$oJoueur){
	if(isset($_SESSION['main']['objet']['code'])){
		$oJoueur->AddInventaire($_SESSION['main']['objet']['code']);
		unset($_SESSION['main']['objet']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionStock';
	}
}
function ActionMove(DBManage &$db, &$check, personnage &$oJoueur, &$objManager){
	//on reset la variable MESSAGE
	unset($_SESSION['message']);

	//on lib�re la ressource avant de bouger
	$objRessource = FoundBatiment(NULL, NULL, $oJoueur->GetCoordonnee());
	
	if(!is_null($objRessource) AND get_class($objRessource) == 'ressource')
	{
		if($objRessource->GetCollecteur() == $oJoueur->GetLogin())
		{
			$objRessource->FreeRessource($oJoueur);
		}
		$objManager->UpdateBatiment($objRessource);
		unset($objRessource);
	}

	//on d�place le joueur
	if(!is_null($_GET['move']) AND $oJoueur->CheckMove(strtolower($_GET['move']))){
		$oJoueur->deplacer($_GET['move']);
		$_GET['move'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionMove';
	}

	//on v�rifie si on a trouv� une quete
	
	if(isset($_SESSION['QueteEnCours'])){
		foreach($_SESSION['QueteEnCours'] as $Quete){
			$Quete->ActionSurQuete($oJoueur);
			$objManager->UpdateQuete($Quete);
		}
	}
	

	//on se fait voler par un voleur
	$num = mt_rand(1, 1000);
	if($num == 100){
		$oJoueur->ArgentVole();
		$msg = '<p>Un voleur vous a d�rob� tout votre argent.</p>';
		$_SESSION['message'][] = $msg;
		$db->InsertHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'voleur', 'Voleur', NULL, $msg);
	}

	//on se fait attaquer par une tour
	if(!$oJoueur->GetAttaqueTour()){
		$tmp = AttaqueTour($db, $oJoueur);
		if($tmp > 0){
			$_SESSION['message'][] = '<p>Vous avez �t� attaqu� par une ou des tours. Vous �tes bless� de '.$tmp.'pts de vie.</p>';
 		}
	}

	if(isset($_SESSION['QueteEnCours'])){
		reset($_SESSION['QueteEnCours']);
	}

	unset($_SESSION['retour_combat']);
	unset($_SESSION['retour_attaque']);
	unset($_GET['move']);
}
function ActionChasser(&$check, personnage &$oJoueur){
	if(isset($_SESSION['chasser'])){
		
		$oGibier = FoundGibier($_SESSION['chasser']);
		
		$oJoueur->AddInventaire($oGibier->GetCode(), 1, false);
		
		if(!is_null($oGibier->GetAttaque()))
		{
			$oJoueur->PerdreVie($oGibier->GetAttaque(), 'chasse');
		}

		unset($_SESSION['chasser']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionChasser';
	}
}
function ActionFrapper(DBManage &$db, &$check, $id, personnage &$oJoueur, &$objManager){
	if(isset($_SESSION['main']['frapper'][$id])){
		$PersoAFrapper = $objManager->get(intval($_SESSION['main']['frapper'][$id]));
		$_SESSION['retour_combat'][] = $PersoAFrapper->GetLogin();
		$_SESSION['retour_combat'][] = $oJoueur->frapper($PersoAFrapper);
		$objManager->update($PersoAFrapper);
		$info = end($_SESSION['retour_combat']);
		//on envoie un mail
		if($PersoAFrapper->GetNotifCombat()){
			NotificationMail($PersoAFrapper->GetMail(), 'combat', $oJoueur->GetLogin(), $info['1']);
		}
		//on ajoute un historique
		$db->InsertHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'combat', $PersoAFrapper->GetLogin(), NULL, $info['0']);
		$db->InsertHistory($PersoAFrapper->GetLogin(), $PersoAFrapper->GetCarte(), $PersoAFrapper->GetPosition(), 'combat', $oJoueur->GetLogin(), NULL, $info['1']);
		unset($_SESSION['main']['frapper'][$id]);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionFrapper';
	}
}
function ActionAttaquer(&$check, $id, personnage &$oJoueur, &$objManager){
	if(isset($_SESSION['main']['attaquer'][$id])){
		$BatimentAAttaquer = $objManager->GetBatiment(strval($_SESSION['main']['attaquer'][$id]));
		//$BatimentAAttaquer = FoundBatiment(NULL, NULL, strval($_SESSION['main']['attaquer'][$id]));

		if($BatimentAAttaquer->GetLogin() == 'romain'){
			$PersoAttaque = new personnage(array('login'=>'romain', 'not_attaque'=>NULL, 'nb_points'=>0));
		}else{
			$PersoAttaque = $objManager->GetPersoLogin($BatimentAAttaquer->GetLogin());
		}

		$_SESSION['retour_attaque'][] = $BatimentAAttaquer->GetLogin();
		$_SESSION['retour_attaque'][] = $BatimentAAttaquer->AttaquerBatiment($PersoAttaque, $oJoueur);

		$objManager->update($PersoAttaque);
		$objManager->UpdateBatiment($BatimentAAttaquer);

		$info = end($_SESSION['retour_attaque']);

		unset($BatimentAAttaquer);
		unset($PersoAttaque);
		unset($_SESSION['main']['attaquer'][$id]);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionAttaquer';
	}
}
function ActionLegionnaire(&$check, personnage &$oJoueur){
	if(isset($_SESSION['main']['legionnaire'])){
		$_SESSION['message'][] = $_SESSION['main']['legionnaire']->CombatLegionnaire($oJoueur);
		unset($_SESSION['main']['legionnaire']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionLegionnaire';
	}
}
function ActionConstruire(DBManage &$db, &$check, $id, personnage &$oJoueur, maison &$maison = NULL){
	if(ChkIfBatimentDejaConstruit($_SESSION['main'][$id]['construire']))
	{
		$check = false;
		echo 'Erreur GLX0003: Fonction ActionConstruire - B�timent d�ja construit';
		return NULL;
	}

	if($_SESSION['main'][$id]['construire'] == maison::ID_BATIMENT)
	{
		//On construit sa maison
		$oJoueur->SetMaisonInstalle($oJoueur->GetCoordonnee());
		// on recup�re les info du batiment
		$sql = "SELECT * FROM table_batiment WHERE id_batiment=".maison::ID_BATIMENT." AND batiment_niveau=1;";
		//$requete = mysql_query($sql) or die ( mysql_error() );
		$requete = $db->Query($sql);
		$batiment = mysql_fetch_array($requete, MYSQL_ASSOC);

		// on ajoute le batiment � la carte
		AddCaseCarte($oJoueur->GetCoordonnee(), $oJoueur->GetLogin(), maison::ID_BATIMENT, $batiment['batiment_vie'], $batiment['batiment_niveau']);
		
	}else{
				
		if(!is_null($maison))
		{
			
			//on paie le batiment
			$chkPaye = true;
		
			if(!is_null($_SESSION['main'][$id]['prix']) AND CheckCout($_SESSION['main'][$id]['prix'], $oJoueur, $maison))
			{
					
				foreach($_SESSION['main'][$id]['prix'] as $Prix)
				{
					UtilisationRessource(explode('=', $Prix), $oJoueur, $maison);
				}
								
				// on recup�re les info du batiment
				$sql = "SELECT * FROM table_batiment WHERE id_type=".$_SESSION['main'][$id]['construire']." AND batiment_niveau=1;";
				//$requete = mysql_query($sql) or die ( mysql_error() );
				$requete = $db->Query($sql);
				$batiment = mysql_fetch_array($requete, MYSQL_ASSOC);
				
				// on ajoute le batiment � la carte
				AddCaseCarte($oJoueur->GetCoordonnee(), $oJoueur->GetLogin(), $_SESSION['main'][$id]['construire'], $batiment['batiment_vie'], $batiment['batiment_niveau']);
				
				//on gagne des points
				$oJoueur->UpdatePoints($batiment['batiment_points']);
				
				//on ajoute un historique que le batiment est construit
				$db->InsertHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'Construction', NULL, NULL, 'Batiment construit. ID du batiment = '.$_SESSION['main'][$id]['construire']);
			}else{
				$check = false;
				echo 'Erreur GLX0003: Fonction ActionConstruire - Pas assez de ressource pour payer';
				return NULL;
			}
						
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionConstruire - Maison introuvable';
			return NULL;
		}
			unset($_SESSION['main'][$id]);
	}
}
function ActionQuete(&$check, $id, personnage &$oJoueur, &$objManager){
	reset($_SESSION['QueteEnCours']);

	foreach($_SESSION['QueteEnCours'] as $key=>$Quete){
		if($Quete->GetIDQuete() == $id)
		{
			$_SESSION['message'][] = $Quete->ActionSurQueteCombat($oJoueur);
			$objManager->UpdateQuete($Quete);
			
			if($Quete->GetVie() <= 0)
			{
				unset($_SESSION['QueteEnCours'][$key]);
			}
			break;
		}
	}
}
function ActionRessource(&$check, personnage &$oJoueur, &$objManager, $id = NULL){
	
	$objRessource = FoundBatiment(NULL, NULL, $oJoueur->GetCoordonnee());
		//On v�rifie si la ressource est bien libre
	if(is_null($objRessource->GetCollecteur())){
			//On d�marre la collecte
		$objRessource->StartCollect($oJoueur, $id);
		
		//On verifie bien si le temp de collecte est bien termin�
	}elseif((strtotime('now') - $objRessource->GetDateDebutAction()) >= $objRessource->GetTempRessource()){
			//On v�rifie si le joueur est bien le collecteur
		if($oJoueur->GetLogin() == $objRessource->GetCollecteur()){
				//on finit la collecte
			$objRessource->FinishCollect($oJoueur/* , $oMaison */);

		}else{
				//Alors on r�cup�re l'objet du collecteur
			$oCollecteur = $objManager->GetPersoLogin($objRessource->GetCollecteur());
				//on finit la collecte
			$objRessource->FinishCollect($oCollecteur/* , $oMaison */);
				//on enregistre les data du collecteur
			$objManager->update($oCollecteur);
			unset($oCollecteur);

		}
			//on enregistre les changement de la maison du collecteur que ce soit le joueur ou non.
		/* $objManager->UpdateBatiment($oMaison);
		unset($oMaison); */
	}
		//on enregistre les modifs de la ressource
	$objManager->UpdateBatiment($objRessource);
	
	unset($objRessource);
}

?>