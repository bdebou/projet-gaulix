<?php
//Affichage Standart
function affiche_LoginStatus() {
    global $VieMaximum, $arCouleurs, $objManager;

    $oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

    $arAtt = $oJoueur->GetAttPerso();
    $arDef = $oJoueur->GetDefPerso();
    //on trouve la maison
    $maison = FoundBatiment(1);
    if (!is_null($maison)) {
        foreach (array('Nourriture', 'Bois', 'Pierre') as $element) {
            switch ($element) {
                case 'Nourriture': $nb = $maison->GetRessourceNourriture();
                    $qte = 50;
                    break;
                case 'Bois': $nb = $maison->GetRessourceBois();
                    $qte = 25;
                    break;
                case 'Pierre': $nb = $maison->GetRessourcePierre();
                    $qte = 25;
                    break;
            }
            if ($nb > 500) {
                $_SESSION['main']['LoginStatus'][$element] = $qte;
                $InfoBulle = '<table class="equipement"><tr><td>Mettre ' . $qte . 'pts ' . AfficheIcone($element, 15) . ' dans votre Bolga</td></tr></table>';
                $txtBt[$element] = '
				<button '
                        . 'type="button" '
                        . (count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga() ? '' : 'disabled="disabled" ')
                        . 'class="LoginStatus" '
                        . 'onmouseover="montre(\'' . CorrectDataInfoBulle($InfoBulle) . '\');" '
                        . 'onmouseout="cache();" '
                        . 'onclick="window.location=\'./fct/main.php?action=MettreBolga&amp;type=' . $element . '\'" '
                        . 'alt="Mettre ' . $qte . 'pts de ' . $element . ' dans votre Bolga">'
                        . '-' . $qte . 'x'
                        . '</button>';
            } else {
                $txtBt[$element] = NULL;
            }
        }
    }

    if (!is_null($maison)) {
        $ligneRessources = '
		<tr>
			<td style="background-color: #' . $arCouleurs['Or'] . ';">'
                . AfficheIcone('or') . ' : ' . $oJoueur->GetArgent()
                . '</td>
			<td style="background-color: #' . $arCouleurs['Nourriture'] . ';">'
                . AfficheIcone('nourriture') . ' : ' . $maison->GetRessourceNourriture()
                . $txtBt['Nourriture']
                . '</td>
		</tr>
		<tr>
			<td style="background-color: #' . $arCouleurs['Bois'] . ';">'
                . AfficheIcone('bois') . ' : ' . $maison->GetRessourceBois()
                . $txtBt['Bois']
                . '</td>
			<td style="background-color: #' . $arCouleurs['Pierre'] . ';">'
                . AfficheIcone('pierre') . ' : ' . $maison->GetRessourcePierre()
                . $txtBt['Pierre']
                . '</td>
			
		</tr>';
    } else {
        $ligneRessources = '
		<tr>
			<td colspan="2" style="background-color: white;">Votre stock de ressources est vide.</td>
		</tr>';
    }

    $txt = '
	<table class="loginstatus">
		<tr>
			<td colspan="2" style="background-color: Brown; font-weight:bold; text-transform:uppercase; text-align:center;">' . $oJoueur->GetLogin() . ' (' . $oJoueur->GetNiveau() . ') ' . AfficheRecompenses($oJoueur->GetLogin(), $oJoueur->GetClan()) . '</td>
		</tr>
		<tr>'
            . '<td><img alt="Barre de Vie" src="./fct/fct_image.php?type=vie&amp;value=' . $oJoueur->GetVie() . '&amp;max=' . $VieMaximum . '" /></td>'
            . '<td><img alt="Barre d\'expérience" src="./fct/fct_image.php?type=experience&amp;value=' . $oJoueur->GetExpPerso() . '&amp;max=' . $oJoueur->GetMaxExperience() . '" /></td>'
            . '</tr>
		<tr>
			<td style="background-color: #' . $arCouleurs['Attaque'] . ';">' . AfficheIcone('attaque') . ' : ' . $arAtt['0'] . ' (' . $arAtt['1'] . ')</td>
			<td style="background-color: #' . $arCouleurs['Defense'] . ';">' . AfficheIcone('defense') . ' : ' . $arDef['0'] . ' (' . $arDef['1'] . ')</td>
		</tr>
		' . $ligneRessources . '
		<tr>
			<td colspan="2" style="text-align:center;">'
            //.AfficheModuleSocial('google')
            . '<button type="button" onclick="window.location=\'./unconnect.php\'" style="width:120px;">Se déconnecter</button>'
            //.AfficheModuleSocial('facebook')
            . '</td>
		</tr>
	</table>';

    $objManager->update($oJoueur);
    unset($oJoueur);

    return $txt;
}
function affiche_carte() {
    global $nbLigneCarte, $nbColonneCarte, $objManager, $VieMaximum;

    $oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

    //on ajoute les joueurs sur la carte
    $sql = "SELECT vie, position, login FROM table_joueurs;";
    $requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
    while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
        $position = explode(',', $row['position']);
        if ($oJoueur->GetCarte() == $position[0]) {
            if (empty($grille[intval($position[1])][intval($position[2])])) {
                $grille[intval($position[1])][intval($position[2])]['login'] = $row['login'];
                $grille[intval($position[1])][intval($position[2])]['vie'] = $row['vie'];
            } elseif ($row['login'] == $oJoueur->GetLogin()) {
                $grille[intval($position[1])][intval($position[2])]['login'] = $row['login'];
                $grille[intval($position[1])][intval($position[2])]['vie'] = $row['vie'];
            }
        }
    }
    //On ajoute les quetes sur la carte.
    if (isset($_SESSION['QueteEnCours'])) {
        foreach ($_SESSION['QueteEnCours'] as $Quete) {
            if ($oJoueur->GetCarte() == $Quete->GetCarte() AND in_array($Quete->GetTypeQuete(), array('romains'))) {
                $arPosition = $Quete->GetPosition();
                $InfoBulle = '<b>' . $Quete->GetNom() . '</b>';
                $grille[intval($arPosition[0])][intval($arPosition[1])]['batiment'] = ' onmouseover="montre(\'' . CorrectDataInfoBulle($InfoBulle) . '\');" onmouseout="cache();" style="background: url(\'./img/' . $Quete->GetTypeQuete() . '.png\') no-repeat center;"';
            }
        }
    }
    //on cache les quetes par les batiments
    //on ajoute les batiments sur la carte
    $sql = "SELECT * FROM table_carte WHERE detruit IS NULL;";
    $requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
    while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
        $position = explode(',', $row['coordonnee']);
        if ($oJoueur->GetCarte() == $position['0']) {
            $sqlb = "SELECT batiment_type, batiment_nom, batiment_description, batiment_vie FROM table_batiment WHERE id_batiment=" . $row['id_type_batiment'] . ";";
            $requeteb = mysql_query($sqlb) or die(mysql_error() . '<br />' . $sqlb);
            $batiment = mysql_fetch_array($requeteb, MYSQL_ASSOC);

            if ($batiment['batiment_type'] != 'ressource' AND $batiment['batiment_type'] != 'carte') {
                $InfoBulle =
                        '<b>' . $batiment['batiment_nom'] . ' de ' . $row['login'] . '</b>'
                        . '<br />'
                        . '<img alt="' . $batiment['batiment_nom'] . '" src="./fct/fct_image.php?type=etatcarte&amp;value=' . $row['etat_batiment'] . '&amp;max=' . ($batiment['batiment_vie'] + (50 * $row['niveau_batiment'])) . '" />'
                        . (($row['login'] == 'romain' AND in_array($row['id_type_batiment'], array(4, 5))) ?
                                '<br />Contenu : '
                                . ($row['id_type_batiment'] == 5 ?
                                        $row['contenu_batiment'] . 'x ' . AfficheIcone('or') : '<b>Plusieurs objets</b>') : '');
                $grille[intval($position[1])][intval($position[2])]['batiment'] = ' onmouseover="montre(\'' . CorrectDataInfoBulle($InfoBulle) . '\');" onmouseout="cache();" style="background: url(\'./img/' . $batiment['batiment_type'] . '-';
                if ($row['login'] == $oJoueur->GetLogin()) {
                    $grille[intval($position[1])][intval($position[2])]['batiment'] .= 'a';
                } else {
                    $grille[intval($position[1])][intval($position[2])]['batiment'] .= 'b';
                }
            } else {
                switch ($batiment['batiment_type']) {
                    case 'ressource':
                        $InfoBulle = '<b>' . $batiment['batiment_description'] . '</b><br /><img alt="Etat Ressource" src="./fct/fct_image.php?type=etatcarte&amp;value=' . $row[strval('res_' . $batiment['batiment_nom'])] . '&amp;max=5000" />';
                        break;
                    case 'carte':
                        $InfoBulle = $batiment['batiment_description'];
                        break;
                }
                $grille[intval($position[1])][intval($position[2])]['batiment'] = ' onmouseover="montre(\'' . CorrectDataInfoBulle($InfoBulle) . '\');" onmouseout="cache();" style="background: url(\'./img/' . $batiment['batiment_nom'] . '';
            }
            $grille[intval($position[1])][intval($position[2])]['batiment'] .= '.png\') no-repeat center;"';
        }
    }

    //on affiche la carte avec les infos
    $txt = '
	<table class="carte">';
    for ($i = 0; $i <= $nbLigneCarte; $i++) {
        $txt .= '
		<tr>';
        for ($j = 0; $j <= $nbColonneCarte; $j++) {
            $txt .= '<td' . (isset($grille[$i][$j]['batiment']) ? $grille[$i][$j]['batiment'] : '') . '>';
            if (isset($grille[$i][$j]['login'])) {
                $txt .= '<img alt="Perso ' . $grille[$i][$j]['login'] . '" src="./img/homme-' . ($oJoueur->GetLogin() == $grille[$i][$j]['login'] ? 'green' : 'grey') . '.png" height="30px" onmouseover="montre(\'' . CorrectDataInfoBulle('<b>' . $grille[$i][$j]['login'] . '</b><br /><img alt="Barre de Vie" src="./fct/fct_image.php?type=VieCarte&amp;value=' . $grille[$i][$j]['vie'] . '&amp;max=' . $VieMaximum . '" />') . '\');" onmouseout="cache();" />';
            }
            $txt .= '
			</td>';
        }
        $txt .= '
		</tr>';
    }
    $txt .= '
	</table>';

    $objManager->update($oJoueur);
    unset($oJoueur);
    return $txt;
}
function affiche_actions() {
    global $retour_combat, $nbLigneCarte, $nbColonneCarte, $objManager;

    $oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

    $position = $oJoueur->GetPosition();

    //===  Partie pour afficher les combats possible  ===
    if (isset($_SESSION['message']) AND !is_null($_SESSION['message'])) {
        foreach ($_SESSION['message'] as $Message) {
            echo $Message . '<hr />';
        }
    }

    //===  Partie pour afficher un objet trouvé  ===
    echo AfficheObjetTrouveDansMenuAction($oJoueur);

    //===  Affichage de collecte de ressource
    echo AfficheCollecteRessource($oJoueur);

    //===  Affichage du gibier apportée de tir  ===
    echo AfficheGibierAChasser($oJoueur);

    //===  On crée la liste des choses dans les environs  ===
    if ($oJoueur->GetMaisonInstalle()) {
        if (!is_null($oJoueur->GetArme())) {
            //Si on a une arme, on vérifie si c'est une arme de jet.
            $sql_arme = "SELECT objet_distance FROM table_objets WHERE objet_code='" . strval($oJoueur->GetArme()) . "';";
            $requete = mysql_query($sql_arme) or die(mysql_error() . '<br />' . $sql_arme);
            $result = mysql_fetch_array($requete, MYSQL_ASSOC);
            if ($result['objet_distance'] != 0) {
                //$strSQLPosition = "(position='".implode(',', array($oJoueur->GetCarte(), $position['0'], $position['1']))."' ";
                
            	$arSQLPosition[] = implode(',', array($oJoueur->GetCarte(), $position['0'], $position['1']));
                
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
                    if (($position['1'] + $i) > $nbLigneCarte) {
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
                    if (($position['0'] + $i) > $nbColonneCarte) {
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
                    if (($position['0'] - $i) < 0 or ($position['1'] + $i) > $nbColonneCarte) {
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
                    if (($position['0'] + $i) > $nbLigneCarte or ($position['1'] - $i) < 0) {
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
                    if (($position['0'] + $i) > $nbLigneCarte or ($position['1'] + $i) > $nbColonneCarte) {
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
            	$arSQLPosition[] = implode(',', array_merge(array($oJoueur->GetCarte()), $position));
                if (isset($_SESSION['QueteEnCours'])) {
                    CheckQueteAccessible($LstQueteAccessible, implode(',', array_merge(array($oJoueur->GetCarte()), $position)));
                }
            }
        } else {
        	$arSQLPosition[] = implode(',', array_merge(array($oJoueur->GetCarte()), $position));
            if (isset($_SESSION['QueteEnCours'])) {
                CheckQueteAccessible($LstQueteAccessible, implode(',', array_merge(array($oJoueur->GetCarte()), $position)));
            }
        }

        //=== On affiche le choix des ennemis attaquables
        $sql = "SELECT 
		id, 
		login, 
		val_attaque, val_defense, 
		niveau, 
		date_last_combat 
		FROM table_joueurs 
		WHERE 
		login NOT IN ('".implode("', '", ListeMembreClan($oJoueur->GetClan()))."') 
		AND position IN('".implode("', '", $arSQLPosition)."');";
        echo AfficheListeEnnemisAFrapper($oJoueur, $sql);
        
        //=== On affiche la liste des batiment attaquables
        $SQLCarte = "SELECT *
		FROM table_carte 
		WHERE 
		detruit IS NULL 
		AND login NOT IN ('".implode("', '", ListeMembreClan($oJoueur->GetClan()))."') 
		AND coordonnee IN ('".implode("', '", $arSQLPosition)."');";
        echo AfficheListeBatimentAttaquable($SQLCarte, $chkConstruction = true);
        
        //=== On affiche le légionnaire
        echo AfficheCombatLegionnaire($oJoueur);
    } else {   //la Maison n'est pas encore installé
        echo '
		<p>Votre Maison n\'est pas encore installée. Installez le où vous voulez et ensuite vous pourrez attaquer d\'autre joueur.</p>
		<hr />';
    }
    //===  Partie pour afficher La possibilité de construction  ===
    echo AfficheMenuConstruction($oJoueur, $position, $chkConstruction, $oJoueur->GetCarte());
    //===  Partie pour afficher les monstres des quetes  ===
    echo AfficheQueteAPorteeDeTire($LstQueteAccessible);

    $objManager->update($oJoueur);
    unset($oJoueur);
}
function affiche_menu() {
    $txt = null;
    $arBtMenu = array(  array('name' => 'Principale', 'link' => './index.php'),
                        array('name' => 'Bolga', 'link' => './inventaire.php'),
                        array('name' => 'Equipements', 'link' => './equipement.php'),
                        array('name' => 'Compétences', 'link' => './competences.php'),
                        array('name' => 'Bricolage', 'link' => './bricolage.php'),
                        array('name' => 'Quêtes', 'link' => './quete.php'),
                        array('name' => 'Scores', 'link' => './scores.php'),
                        array('name' => 'Oppidum', 'link' => './village.php'),
                        array('name' => 'Alliances', 'link' => './alliance.php'),
                        array('name' => 'Carte', 'link' => './cartes.php'),
                        array('name' => 'Règles', 'link' => './regle.php'),
                        array('name' => AfficheIcone('options', 15) . ' Options', 'link' => './options.php')
    );

    foreach ($arBtMenu as $bt) {
        $txt .= '
	<button type="button" class="bt-menu" 
		onclick="window.location=\'' . $bt['link'] . '\';">'
                . $bt['name']
                . '</button>';
    }

    return $txt;
}
function affiche_mouvements() {
    global $temp_attente, $DeplacementMax, $objManager;

    $oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
    if ($oJoueur->GetDepDispo() < $DeplacementMax) {
        $txtDep = '
		<tr>
			<td colspan="3">
					Encore ' . $oJoueur->GetDepDispo() . ' déplacements
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<div style="display:inline;" id="TimeToWait"></div>
				<script type="text/JavaScript">CountDown(' . abs($temp_attente - (strtotime('now') - $oJoueur->GetLastAction())) . ');</script>
			</td>
		</tr>';
    } else {
        $txtDep = '
		<tr>
			<td colspan="3">
				Maximum de ' . $DeplacementMax . ' déplacements atteint
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
					onclick="window.location=\'./fct/main.php?move=up\'" '
            //. ($oJoueur->moveUp() ? '' : 'disabled="disabled"') . ' 
			. ($oJoueur->CheckMove('up') ? '' : 'disabled="disabled"') . ' 
					class="mouvements">'
            //. AfficheIcone(($oJoueur->moveUp() ? 'up' : 'croix'))
			. AfficheIcone(($oJoueur->CheckMove('up') ? 'up' : 'croix'))
            . '</button>
			</td>
			<td style="border:none;">&nbsp;</td>
		</tr>
		<tr>
			<td>
				<button 
					type="button" 
					onclick="window.location=\'./fct/main.php?move=left\'" '
            //. ($oJoueur->moveLeft() ? '' : 'disabled="disabled"') . ' 
			. ($oJoueur->CheckMove('left') ? '' : 'disabled="disabled"') . ' 
					class="mouvements">'
            //. AfficheIcone(($oJoueur->moveLeft() ? 'left' : 'croix'))
			. AfficheIcone(($oJoueur->CheckMove('left') ? 'left' : 'croix'))
            . '</button>
			</td>
			<td>&nbsp;</td>
			<td>
				<button 
					type="button" 
					onclick="window.location=\'./fct/main.php?move=right\'" '
            //. ($oJoueur->moveRight() ? '' : 'disabled="disabled"') . ' 
			. ($oJoueur->CheckMove('right') ? '' : 'disabled="disabled"') . ' 
					class="mouvements">'
            //. AfficheIcone(($oJoueur->moveRight() ? 'right' : 'croix'))
			. AfficheIcone(($oJoueur->CheckMove('right') ? 'right' : 'croix'))
            . '</button>
			</td>
		</tr>
		<tr>
			<td style="border:none;">&nbsp;</td>
			<td>
				<button 
					type="button" 
					onclick="window.location=\'./fct/main.php?move=down\'" '
            //. ($oJoueur->moveDown() ? '' : 'disabled="disabled"') . ' 
			. ($oJoueur->CheckMove('down') ? '' : 'disabled="disabled"') . ' 
					class="mouvements">'
            //. AfficheIcone(($oJoueur->moveDown() ? 'down' : 'croix'))
			. AfficheIcone(($oJoueur->CheckMove('down') ? 'down' : 'croix'))
            . '</button>
			</td>
			<td style="border:none;">&nbsp;</td>
		</tr>
	</table>';

    $objManager->update($oJoueur);
    unset($oJoueur);
    return $txt;
}
function AfficheFooter($bXHTML, $bCSS = true) {
    global $NumVersion;
    return '
	<table style="width:100%">
		<tr>
			<td style="text-align:left;">
				<p>
					Version : ' . $NumVersion . '<br />
					<a rel="author" style="font-size:10px;" target="_blank" href="https://plus.google.com/u/0/107937906218732922871" style="text-decoration:none;">
						<img src="http://www.google.com/images/icons/ui/gprofile_button-16.png" width="10" height="10" alt="Google+ profile" title="Google+ profile" style="border:0;">
						+Bruno Deboubers
					</a>
				</p>
			</td>
			<td style="text-align:right;">
				<p>'
            . ($bXHTML ?
                    '<a href="http://validator.w3.org/check?uri=referer" style="text-decoration:none;" target="_blank">
						<img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" height="31" width="88" />
					</a>' : '')
            . ($bCSS ?
                    '<a href="http://jigsaw.w3.org/css-validator/check/referer" style="text-decoration:none;" target="_blank">
						<img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss" alt="CSS Valide !" />
					</a>' : '')
            . '</p>
			</td>
		</tr>
	</table>';
}
function AfficheModuleSocial() {
    return '<table class="module_social">'
            . '<tr>'
            . '<td>'
            . '<div class="fb-like" data-href="https://www.facebook.com/pages/Gaulix/215647241841733" data-send="false" data-layout="box_count" data-width="55"></div>'
            . '</td>'
            . '<td>'
            . '<div class="g-plusone" data-size="tall" data-href="http://www.gaulix.be"></div>'
            . '</td>'
            . '</tr>'
            . '</table>';
}
function AfficheHead() {
    return '<script type="text/javascript" src="./fct/js_main.js"></script>'
            . '<script type="text/javascript" src="./fct/js_infobulle.js"></script>'
            //.'<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: \'fr\'}</script>'
    		.'<link rel="icon" href="./img/icone.png" type="image/x-icon" />'
    		.'<link rel="shortcut icon" href="./img/icone.png" type="image/x-icon" />'
            . '<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />'
            . '<meta name="keywords" content="gaulix" />'
            . '<meta name="description" content="Gaulix est un Web Game totalement gratuit. Vous incarnez un personnage gaulois. Il doit construire sa maison et peut construire aussi d\'autre batiment. Mais pour cela il lui faut des ressources que vous devrez collecter, apprendre des compétences pour fabriquer des armes, ... Bonne Chance!!!" />'
            . '<link rel="stylesheet" href="./css/styles.css" type="text/css" />'
            . '<link href="https://plus.google.com/u/0/b/116898576928846446900/116898576928846446900/posts" rel="publisher" />'
            . '<link href="http://fonts.googleapis.com/css?family=MedievalSharp" rel="stylesheet" type="text/css">'
            . '<meta property="og:title" content="Gaulix" />'
            . '<meta property="og:type" content="game" />'
            . '<meta property="og:url" content="http://www.gaulix.be" />'
            . '<meta property="og:image" content="http://www.gaulix.be/img/logo.png" />'
            . '<meta property="og:site_name" content="Gaulix" />'
            . '<meta property="fb:admins" content="100002431126216" />';
}

//on fait la rencontre d'un légionnaire Romain
Function AfficheCombatLegionnaire(&$oJoueur) {
    $num = mt_rand(1, 100);
    if ($num == 50 AND !$oJoueur->GetChkLegionnaire()) {
        $_SESSION['main']['legionnaire'] = new Legionnaire($oJoueur->GetNiveau());
        //$_SESSION['main']['laisser'] = 'legionnaire';
        $oJoueur->SetLegionnaire(true);
        return '<p style="display:inline;">' . AfficheIcone('attention') . ' Légion romaine en vue!<br />Voulez-vous l\'attaquer ?'
                . '<ul style="display:inline; list-style-type:none; padding:0px; text-align:center;">'
                . '<li style="display:inline;"><a style="margin-left:20px;" href="./fct/main.php?action=legionnaire">Attaquer</a></li>'
                . '<li style="display:inline; margin-left:20px;"><a href="./fct/main.php?action=laisser&amp;type=legionnaire">Laisser</a></li>'
                . '</ul>
			</p>
			<hr />';
    }
}

//La partie Chasse avec Gibier
function AfficheGibierAChasser(&$oJoueur) {
    $txt = null;
    if (!is_null($oJoueur->GetNiveauCompetence('Chasseur')) AND !$oJoueur->GetChkChasse()) {
        $Gibier = GibierTrouve($oJoueur->GetNiveauCompetence('Chasseur'));

        if (!is_null($Gibier)) {
            $_SESSION['main']['chasser']['nourriture'] = $Gibier->GetGainNourriture($oJoueur->GetNiveauCompetence('Boucher'));
            $_SESSION['main']['chasser']['cuir'] = $Gibier->GetGainCuir($oJoueur->GetNiveauCompetence('Tanneur'));
            $_SESSION['main']['chasser']['attaque'] = $Gibier->GetAttaque();
            //$_SESSION['main']['laisser'] = 'chasse';

            $txt = '
			<p>Voulez-vous chasser : ' . $Gibier->GetNom() . ' (' . $Gibier->GetAfficheGainChasse($oJoueur) . ') ?'
                    . '<ul style="display:inline; list-style-type:none; padding:0px; text-align:center;">'
                    . '<li style="display:inline;"><a style="margin-left:20px;" href="./fct/main.php?action=chasser">Chasser</a></li>'
                    . '<li style="display:inline; margin-left:20px;"><a href="./fct/main.php?action=laisser&amp;type=chasser">Laisser</a></li>'
                    . '</ul>
			</p>
			<hr />';
        }
    }

    $oJoueur->SetChasse(true);

    return $txt;
}
Function GibierTrouve($NiveauChasseur) {
    //$sqlGibier = "SELECT * FROM table_gibier;";
    $sqlGibier = "SELECT * FROM table_objets WHERE objet_type='gibier';";
    $rqtGibier = mysql_query($sqlGibier) or die(mysql_error() . '<br />' . $sqlGibier);
    //on crée une table contenant les infos des objets
    $arGibier = null;
    while ($row = mysql_fetch_array($rqtGibier, MYSQL_ASSOC)) {
        $arGibier[] = new Gibier($row);
    }
    //on prend un nombre aléatoire
    $num = mt_rand(1, mysql_num_rows($rqtGibier) * 20);

    if (isset($arGibier[$num])) {
        switch ($NiveauChasseur) {
            case 3: if ($arGibier[$num]->GetNiveau() == 'grand') {
                    return $arGibier[$num];
                }
            case 2: if ($arGibier[$num]->GetNiveau() == 'moyen') {
                    return $arGibier[$num];
                }
            case 1: if ($arGibier[$num]->GetNiveau() == 'petit') {
                    return $arGibier[$num];
                }
        }
    }
    return null;
}

//La partie Objet Trouvé (Ressource)
function AfficheObjetTrouveDansMenuAction(&$oJoueur) {
    $DebugMode = false;
    $txt = null;
    if (!$oJoueur->GetChkObject()) {
        $CodeObjetTrouve = ObjetTrouve($oJoueur);
        if ($DebugMode) {
            echo $CodeObjetTrouve;
        }
        if (is_null($CodeObjetTrouve)) {
            $oJoueur->SetLastObject(true, null);
        } else {
            //$sql = "SELECT * FROM table_objets_trouves WHERE objet_id=$intObj;";
            $sql = "SELECT * FROM table_objets WHERE objet_code='$CodeObjetTrouve';";
            $requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
            $result = mysql_fetch_array($requete, MYSQL_ASSOC);
            if ($result['objet_niveau'] <= $oJoueur->GetNiveau()) {
                $oJoueur->SetLastObject(true, $CodeObjetTrouve);
                $txt .= AfficheActionPossible($oJoueur, $result);
            } else {
                $oJoueur->SetLastObject(true, null);
            }
        }
    } else {
        if (!is_null($oJoueur->GetLastObject())) {
            //$sql = "SELECT * FROM table_objets_trouves WHERE objet_id=".strval($oJoueur->GetLastObject()).";";
            $sql = "SELECT * FROM table_objets WHERE objet_code='" . strval($oJoueur->GetLastObject()) . "';";
            $requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
            $result = mysql_fetch_array($requete, MYSQL_ASSOC);
            $txt .= AfficheActionPossible($oJoueur, $result);
        } else {
            $oJoueur->SetLastObject(true, null);
        }
    }


    //global $db;
    //$objManager = new PersonnagesManager($db);
    //$objManager->update($_SESSION['joueur']);
    //unset($objManager);
    return (is_null($txt) ? $txt : $txt . '<hr />');
}
function ObjetTrouve(&$oJoueur) {
    //$sqlObj = "SELECT objet_id, objet_niveau FROM table_objets_trouves;";
    $sqlObj = "SELECT objet_code, objet_niveau FROM table_objets WHERE objet_type='ressource';";
    $requeteObj = mysql_query($sqlObj) or die(mysql_error() . '<br />' . $sqlObj);
    //on crée une table contenant les infos des objets
    $arObjets = null;
    while ($row = mysql_fetch_array($requeteObj, MYSQL_ASSOC)) {
        $arObjets[] = array('objet_code' => $row['objet_code'], 'objet_niveau' => $row['objet_niveau']);
    }
    //on prend un nombre aléatoire
    $num = mt_rand(1, mysql_num_rows($requeteObj) + intval(exp(($oJoueur->GetNiveau() > 5 ? 5 : $oJoueur->GetNiveau()))));

    if (isset($arObjets[$num]) AND $arObjets[$num]['objet_niveau'] <= $oJoueur->GetNiveau()) {
        return $arObjets[$num]['objet_code'];
    }
    return null;
}
function AfficheActionPossible(&$oJoueur, $arData) {
    global $VieMaximum, $DeplacementMax;

    $_SESSION['main']['objet']['code'] = $arData['objet_code'];
    $_SESSION['main']['objet']['type'] = QuelTypeRessource($arData['objet_code']);
    $_SESSION['main']['objet']['value'] = $arData['objet_nb'];
    $_SESSION['main']['objet']['action'] = true;
    $_SESSION['main']['objet']['laisser'] = 'ObjetTrouvé';

    $txt = null;

    //$nbObjetDansSac = count($_SESSION['joueur']->GetLstInventaire());

    $txt .= '
		<p>Vous avez trouvé l\'objet suivant : ' . $arData['objet_nom']
            . (substr($arData['objet_code'], 0, 3) == 'Res' ?
                    ' (' . $arData['objet_nb'] . 'x ' . AfficheIcone($arData['objet_code']) . ')' : '')
            . '<ul style="list-style-type:none; padding:0px; text-align:center;">';
    //on vérifie si le bolga n'est pas plein
    if (count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga()) {
        $txt .= '<li style="display:inline;"><a href="./fct/main.php?action=stock">Ramasser</a></li>';
    } else {
        $txt .= '<li style="display:inline;">Votre Bolga est plein.</li>';
    }
    //on vérifie le type d'objet
    if (in_array($_SESSION['main']['objet']['type'], array('deplacement', 'argent', 'nourriture', 'bois', 'pierre', 'vie', 'divers'))) {
        if ($_SESSION['main']['objet']['type'] == 'vie' and ($oJoueur->GetVie() + $arData['objet_nb']) > $VieMaximum) {
            $txt .= '<li style="display:inline; margin-left:20px;">Limite de ' . $VieMaximum . ' vie atteinte</li>';
        } elseif ($_SESSION['main']['objet']['type'] == 'deplacement' and ($oJoueur->GetDepDispo() + $arData['objet_nb']) > $DeplacementMax) {
            $txt .= '<li style="display:inline; margin-left:20px;">Limite de ' . $DeplacementMax . ' déplacements atteint</li>';
        } else {
            //on vérifie si on a déja une maison ou pas
            if (in_array($_SESSION['main']['objet']['type'], array('nourriture', 'bois', 'pierre'))
                    AND is_null($oJoueur->GetMaisonInstalle())) {
                $txt .= '<li style="display:inline; margin-left:20px;">Pas Encore de Maison</li>';
            } elseif (!in_array($_SESSION['main']['objet']['type'], array('divers'))) {
                $txt .= '<li style="display:inline; margin-left:20px;"><a href="./fct/main.php?action=utiliser">Utiliser</a></li>';
            }
        }
        //Si le bolga n'est pas plein, on peut s'équiper.
    } elseif (count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga()) {
        $txt .= '<li style="display:inline; margin-left:20px;"><a href="./fct/main.php?action=equiper">Equiper</a></li>';
    }
    //on affiche la possibilité de laisser l'objet
    $txt .= '
			<li style="display:inline; margin-left:20px;"><a href="./fct/main.php?action=laisser&amp;type=objet">Laisser</a></li>
		</ul></p>';

    return $txt;
}

//La partie Combats
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
            $sqlBatiment = "SELECT batiment_nom, batiment_type FROM table_batiment WHERE id_batiment=" . $row['id_type_batiment'] . ";";
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
                    $txt .= '
				<li><a href="./fct/main.php?action=attaquer&amp;id=' . $nbBatiment . '">Attaquer ' . $rstBatiment['batiment_nom'] . ' de ' . $row['login'] . ' (' . $row['etat_batiment'] . ')</a></li>';
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
function AfficheListeEnnemisAFrapper(&$oJoueur, $sql) {
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
		<p>Vous pouvez attaquer les joueurs suivants : <i>nom(niveau) Att-Def</i>
			<ul>';
                            $chkEnnemis = false;
                        }
                        $_SESSION['main']['frapper'][] = $row['id'];
                        $txt .= '
				<li><a href="./fct/main.php?action=frapper&amp;id=' . $nbEnnemis . '">Attaquer ' . $row['login'] . '(' . $row['niveau'] . ')' . $row['val_attaque'] . '-' . $row['val_defense'] . '</a></li>';
                        $chkEnnemisEnd = true;
                        $nbEnnemis++;
                    }
                }
            }
        }
        if ($chkEnnemisEnd) {
            $txt .= '
			</ul>
		</p>';
        }
        $txt .= '<hr />';
    }
    return $txt;
}
function AfficheQueteAPorteeDeTire(&$lstMonstre) {
    $txt = null;
    if (isset($lstMonstre)) {
        $txt .= '
		<p>Vous pouvez attaquer :
			<ul>';
        foreach ($lstMonstre as $Monstre) {
            $txt .= '
			<li><a href="./fct/main.php?action=quete&amp;id=' . $Monstre['id'] . '">Attaquer ' . $Monstre['nom'] . '</a></li>';
        }
        $txt .= '</ul>
		</p>
		<hr />';
    }
    return $txt;
}
function CheckQueteAccessible(&$lstMonster, $Position) {
    global $temp_combat;
    $arTmp = null;
    foreach ($_SESSION['QueteEnCours'] as $Quete) {
		$arPositionJoueur = explode(',', $Position);
        if (in_array($Quete->GetTypeQuete(), array('monstre', 'romains')) AND $arPositionJoueur[0] == $Quete->GetCarte()) {
			$arPositionQuete = $Quete->GetPosition();
            if ($arPositionQuete[0] == $arPositionJoueur[1]
                    AND $arPositionQuete[1] == $arPositionJoueur[2]
                    AND (strtotime('now') - $Quete->GetDateCombat()) > $temp_combat) {
                $lstMonster[] = array('id' => $Quete->GetIDQuete(), 'nom' => $Quete->GetNom());
            }
        }
    }
}

//Les partie Constructions
function AfficheMenuConstruction(&$oJoueur, $position, &$chkConstruction, $carte) {
    $_SESSION['main']['position'] = implode(',', array_merge(array($carte), $position));

    if (is_null($oJoueur->GetMaisonInstalle())) {  //La Maison n'est pas encore installée
        if (!ChkIfFree($position, $carte)) {
            return '
		<p>Ce terrain est déjà occupé.</p>
		<hr />';
        } else {
            $_SESSION['main']['0']['construire'] = 1;
            $_SESSION['main']['0']['prix_or'] = 0;
            $_SESSION['main']['0']['prix_bois'] = 0;
            $_SESSION['main']['0']['prix_pierre'] = 0;
            $_SESSION['main']['0']['prix_nourriture'] = 0;
            return '
		<p><a href="./fct/main.php?action=construire&amp;id=0">Construire ma Maison ici</a></p>
		<hr />';
        }
    } elseif (ChkIfFree($position, $carte)) {  //La Maison est installée ET la case est vide.
        if ($chkConstruction AND MonVillageEstProche($carte, $position, $oJoueur->GetLogin())) {
            $sqlBatiment = "SELECT * FROM table_batiment WHERE id_batiment!=1 AND batiment_type!='ressource' AND batiment_type!='carte';";
            $rqtBatiment = mysql_query($sqlBatiment) or die(mysql_error() . '<br />' . $sqlBatiment);
            $txt = null;
            $chkStart = true;
            $nbBatiment = 0;

            //on trouve la maison
            $maison = FoundBatiment(1);

            while ($row = mysql_fetch_array($rqtBatiment, MYSQL_ASSOC)) {
                if (ChkIfBatimentDejaConstruit($row['id_batiment'])) {
                    if ($chkStart) {
                        $txt = '
		<p>Vous pouvez construire les batiments suivants :
			<ul>';
                        $chkStart = false;
                    }
                    if ($oJoueur->GetArgent() >= $row['prix_or']
                            AND $maison->GetRessourceBois() >= $row['prix_bois']
                            AND $maison->GetRessourcePierre() >= $row['prix_pierre']
                            AND $maison->GetRessourceNourriture() >= $row['prix_nourriture']) {
                        $_SESSION['main'][$nbBatiment]['construire'] = $row['id_batiment'];
                        $_SESSION['main'][$nbBatiment]['prix_or'] = $row['prix_or'];
                        $_SESSION['main'][$nbBatiment]['prix_bois'] = $row['prix_bois'];
                        $_SESSION['main'][$nbBatiment]['prix_pierre'] = $row['prix_pierre'];
                        $_SESSION['main'][$nbBatiment]['prix_nourriture'] = $row['prix_nourriture'];
                        $txt .= '
				<li>"<a href="./fct/main.php?action=construire&amp;id=' . $nbBatiment . '">' . $row['batiment_nom'] . '</a>" au prix de : ' . AfficheListePrix(array('Bois' => $row['prix_bois'], 'Pierre' => $row['prix_pierre'], 'Or' => $row['prix_or'], 'Nourriture' => $row['prix_nourriture']), array('Bois' => $maison->GetRessourceBois(), 'Pierre' => $maison->GetRessourcePierre(), 'Or' => $oJoueur->GetArgent(), 'Nourriture' => $maison->GetRessourceNourriture())) . '</li>';
                    } else {
                        $txt .= '
				<li>"' . $row['batiment_nom'] . '" au prix de : ' . AfficheListePrix(array('Bois' => $row['prix_bois'], 'Pierre' => $row['prix_pierre'], 'Or' => $row['prix_or'], 'Nourriture' => $row['prix_nourriture']), array('Bois' => $maison->GetRessourceBois(), 'Pierre' => $maison->GetRessourcePierre(), 'Or' => $oJoueur->GetArgent(), 'Nourriture' => $maison->GetRessourceNourriture())) . '</li>';
                    }
                    $nbBatiment++;
                }
            }
            if (!is_null($txt)) {
                return $txt . '
			</ul>
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
function ChkIfBatimentDejaConstruit($IDBatiment) {
    global $lstBatimentConstructionUnique;
    if (in_array($IDBatiment, $lstBatimentConstructionUnique)) {
        $sql = "SELECT id_case_carte FROM table_carte WHERE id_type_batiment=$IDBatiment AND detruit IS NULL AND login='" . $_SESSION['joueur'] . "';";
        $requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
        if (mysql_num_rows($requete) > 0) {
            return false;
        }
    }
    return true;
}
function ChkIfFree($position, $carte) {
    $sql = "SELECT id_case_carte FROM table_carte WHERE coordonnee='" . implode(',', array_merge(array($carte), $position)) . "' AND detruit IS NULL;";
    $requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
    if (mysql_num_rows($requete) > 0) {
        return false;
    }

    return true;
}
function MonVillageEstProche($carte, $position, $login) {
    global $nbColonneCarte, $nbLigneCarte;
    $sql = "SELECT coordonnee, id_type_batiment FROM table_carte WHERE login='" . $login . "' AND detruit IS NULL;";
    $requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
    $lstCoordonnee[] = implode(',', array_merge(array($carte), $position));
    //la direction verticale haut
    if (($position['1'] - 1) >= 0) {
        $lstCoordonnee[] = implode(',', array($carte, $position['0'], ($position['1'] - 1)));
    }
    //la direction verticale bas
    if (($position['1'] + 1) <= $nbLigneCarte) {
        $lstCoordonnee[] = implode(',', array($carte, $position['0'], ($position['1'] + 1)));
    }
    //la direction horizontale gauche
    if (($position['0'] - 1) >= 0) {
        $lstCoordonnee[] = implode(',', array($carte, ($position['0'] - 1), $position['1']));
    }
    //la direction horizontale droite
    if (($position['0'] + 1) <= $nbColonneCarte) {
        $lstCoordonnee[] = implode(',', array($carte, ($position['0'] + 1), $position['1']));
    }
    //la direction oblique HG
    if (($position['0'] - 1) >= 0 or ($position['1'] - 1) >= 0) {
        $lstCoordonnee[] = implode(',', array($carte, ($position['0'] - 1), ($position['1'] - 1)));
    }
    //la direction oblique HD
    if (($position['0'] - 1) >= 0 or ($position['1'] + 1) <= $nbColonneCarte) {
        $lstCoordonnee[] = implode(',', array($carte, ($position['0'] - 1), ($position['1'] + 1)));
    }
    //la direction oblique BG
    if (($position['0'] + 1) <= $nbLigneCarte or ($position['1'] - 1) >= 0) {
        $lstCoordonnee[] = implode(',', array($carte, ($position['0'] + 1), ($position['1'] - 1)));
    }
    //la direction oblique BD
    if (($position['0'] + 1) <= $nbLigneCarte or ($position['1'] + 1) <= $nbColonneCarte) {
        $lstCoordonnee[] = implode(',', array($carte, ($position['0'] + 1), ($position['1'] + 1)));
    }

    while ($Coordonnee = mysql_fetch_array($requete, MYSQL_ASSOC)) {
        if (in_array($Coordonnee['coordonnee'], $lstCoordonnee)) {
            return true;
        }
    }
    return false;
}

//Les fonctions de gestion de quete
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
                    CancelQuete($row['id_quete_en_cours']);
                }
            } elseif ((strtotime('now') - strtotime($row['date_start'])) >= $infoQuete['quete_duree']) {
                CancelQuete($row['id_quete_en_cours']);
            }
        }
        return $QueteEnCours;
    } else {
        return NULL;
    }
}
function CancelQuete($IDQuete) {
    $sqlBis = "UPDATE table_quetes SET quete_reussi = 1, date_end = '" . date('Y-m-d H:i:s') . "' WHERE id_quete_en_cours = " . $IDQuete . ";";
    mysql_query($sqlBis) or die(mysql_error() . '<br />' . $sqlBis);
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
                        . AfficheCompteurTemp('Quete' . $QueteEnCours->GetIDQuete(), './quete.php', ($QueteEnCours->GetDuree() - (strtotime('now') - $QueteEnCours->GetDateStart())))
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
            . ($_SESSION['main']['uri'] != 1 ?
                    '<tr>
			<td colspan="' . $nbInfo . '">
				<form method="post" action="quete.php">
					<input type="hidden" name="num_quete" value="' . $QueteEnCours->GetIDQuete() . '" />
					<input type="submit" name="annule" value="Annuler" style="width:200px;" />
				</form>
			</td>
		</tr>' : '') .
            '</table>';
}
function ResetListeQuetes($login) {
    $sql = "SELECT id_quete_en_cours FROM  table_quetes WHERE quete_login='" . $login . "';";
    $requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
    while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
        $sqlRemove = "DELETE FROM table_quetes WHERE id_quete_en_cours=" . intval($row['id_quete_en_cours']) . ";";
        mysql_query($sqlRemove) or die(mysql_error() . '<br />' . $sqlRemove);
    }
}

//Les fonctions générales
function AfficheCollecteRessource(&$oJoueur) {
    //on vérifie si on a déja installé sa maison
    if (is_null($oJoueur->GetMaisonInstalle())) {
        return null;
    }

    //On vérifie si la case est vide, si oui --> Finis
    if (ChkIfFree($oJoueur->GetPosition(), $oJoueur->GetCarte())) {
        return null;
    }

    //Si non on continue et on récupére les infos de la case
    $sqlCarte = "SELECT * FROM table_carte WHERE coordonnee='" . implode(',', array_merge(array($oJoueur->GetCarte()), $oJoueur->GetPosition())) . "' AND detruit IS NULL;";
    $requeteCarte = mysql_query($sqlCarte) or die(mysql_error() . '<br />' . $sqlCarte);
    //on re vérifie si il y a bien quelque chose sur la case, si non --> Finis
    if (mysql_num_rows($requeteCarte) == 0) {
        return null;
    }
    //si il y a quelque chose, on continue en stockant les infos de la case
    $resultCarte = mysql_fetch_array($requeteCarte, MYSQL_ASSOC);

    //on va récupere les info du type d'occupation de la case	
    $sqlBatiment = "SELECT batiment_type, batiment_nom, batiment_description FROM table_batiment WHERE id_batiment=" . $resultCarte['id_type_batiment'] . ";";
    $requeteBatiment = mysql_query($sqlBatiment) or die(mysql_error() . '<br />' . $sqlBatiment);
    $resultBatiment = mysql_fetch_array($requeteBatiment, MYSQL_ASSOC);

    //Si le batiment n'est pas une ressource --> fini
    if ($resultBatiment['batiment_type'] != 'ressource') {
        return null;
    }
    //si c'est une ressource, on continu en créant l'objet Ressource
    $objRessource = new Ressource($resultCarte, $resultBatiment);
    $_SESSION['main']['ressource'] = $objRessource;

    switch ($objRessource->GetNom()) {
        case 'Bois': $txtRes = 'des buches';
            break;
        case 'Pierre': $txtRes = 'des pierres';
            break;
        case 'Or': $txtRes = 'de l\'or';
            break;
    }

    //Dans quel etat est la ressource?
    if (is_null($objRessource->GetCollecteur())) {
        if (is_null($oJoueur->GetNiveauCompetence($objRessource->GetCompetenceRequise()))) {
            return '<p>Vous devez avoir la compétence <u>' . $objRessource->GetCompetenceRequise() . '</u> de niveau 1 pour ramasser ' . $txtRes . '</p><hr />';
        } else {
            $txt = null;
            if ($oJoueur->GetNiveauCompetence($objRessource->GetCompetenceRequise()) >= Ressource::NIVEAU_NORMAL) {
                $txt .= '<p style="text-align:center;">'
                        . '<a href="./fct/main.php?action=ressource&amp;id='.Ressource::TYPE_NORMAL.'">'
                        . 'Collecter ' . $objRessource->GetQuantiteCollecte($oJoueur->GetNiveauCompetence($objRessource->GetCompetenceRequise())) . ' ' . AfficheIcone(strtolower($objRessource->GetNom()))
                        . '</a>'
                        . '</p>';
            }
            //Si on est sur de la pierre, on peut collecter aussi de l'or
            if ($objRessource->GetNom() == 'Pierre'
                    AND $oJoueur->GetNiveauCompetence($objRessource->GetCompetenceRequise()) >= Ressource::NIVEAU_OR) {
                $txt .= '<p style="text-align:center;">'
                        . '<a href="./fct/main.php?action=ressource&amp;id='.Ressource::TYPE_OR.'">'
                        . 'Collecter ' . $objRessource->GetQuantiteCollecte($oJoueur->GetNiveauCompetence($objRessource->GetCompetenceRequise()), Ressource::TYPE_OR) . ' ' . AfficheIcone(strtolower($objRessource->GetNom(Ressource::TYPE_OR)))
                        . '</a>'
                        . '</p>';
            }
            return $txt . '<hr />';
        }
    } else {
        if ((strtotime('now') - $objRessource->GetDateDebutAction()) >= $objRessource->GetTempRessource()) {
            return '<script language="javascript">window.location=\'./fct/main.php?action=ressource\';</script>';
        } elseif ($objRessource->GetCollecteur() == $oJoueur->GetLogin()) {
            return '<p style="display:inline;">Vous êtes en train de collecter ' . $txtRes . ' ' . AfficheIcone(strtolower($objRessource->GetNom())) . '. Vous en avez encore pour :</p>
				<div style="display:inline;" id="TimeToWaitRessource"></div><p style="display:inline;"> N\'interrompez pas votre collecte sinon ce sera perdu.</p>'
                    . AfficheCompteurTemp('Ressource', './fct/main.php?action=ressource', ($objRessource->GetTempRessource() - (strtotime('now') - $objRessource->GetDateDebutAction())))
                    . '<hr />';
        } else {
            return '<p style="display:inline;">La ressource est en cours d\'utilisation par ' . $objRessource->GetCollecteur() . ' pour encore :</p>
				<div style="display:inline;" id="TimeToWaitRessource"></div>'
                    . AfficheCompteurTemp('Ressource', './fct/main.php?action=ressource', ($objRessource->GetTempRessource() - (strtotime('now') - $objRessource->GetDateDebutAction())))
                    . '<hr />';
        }
    }
}
function RefreshData($codeUri) {
    global $objManager;

    $oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

    //on annule des transactions du marcher car délai dépassé
    $sql = "SELECT * FROM table_marcher WHERE status_vente IS NULL AND vendeur IS NOT NULL;";
    $requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
    global $TempMaxTransaction;
    if (mysql_num_rows($requete) > 0) {
        while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
            if ((strtotime('now') - strtotime($row['date_vente'])) > $TempMaxTransaction) {
                $maison = FoundBatiment(1, $row['vendeur']);
                //on récupère ses ressources mises en vente
                $maison->AddNourriture($row['vente_nourriture']);
                $maison->AddBois($row['vente_bois']);
                $maison->AddPierre($row['vente_pierre']);
                //on récupère son or mis en vente
                $vendeur = $objManager->GetPersoLogin($row['vendeur']);
                $vendeur->AddOr($row['vente_or']);
                //on enregistre
                $objManager->update($vendeur);
                $objManager->UpdateBatiment($maison);
                //on annule la transaction dans la DB
                UpdateTransaction($row['ID_troc'], 'annule');

                unset($maison);
                unset($vendeur);
            }
        }
    }

    //On termine les Compétences en cours
    FinishAllCompetenceEnCours($oJoueur);

    $objManager->update($oJoueur);

    unset($_SESSION['main'], $oJoueur);
    $_SESSION['main']['uri'] = $codeUri;
    $_SESSION['main']['deplacement'] = 'new';
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
function AfficheTempPhrase($arTemp) {
    return ($arTemp['0'] > 0 ? $arTemp['0'] . 'jrs ' : '') . ($arTemp['1'] > 0 ? $arTemp['1'] . 'hrs ' : '') . ($arTemp['2'] > 0 ? $arTemp['2'] . 'min ' : '') . ($arTemp['3'] > 0 ? $arTemp['3'] . 'sec' : '');
}
function RetourPage($numpage, $FromMain = false, $bLink = true) {
    $url = ($FromMain?'../':'./');
	
    switch ($numpage) {
        case '0':	$url = './';				break;
        case '1':	$url .= 'index.php';		break;
        case '2':	$url .= 'inventaire.php';	break;
        case '3':	$url .= 'equipement.php';	break;
        case '4':	$url .= 'competences.php';	break;
        case '5':	$url .= 'scores.php';		break;
        case '6':	$url .= 'regle.php';		break;
        case '7':	$url .= 'quete.php';		break;
        case '8':	$url .= 'village.php';		break;
        case '9':	$url .= 'alliance.php';		break;
        case '10':	$url .= 'cartes.php';		break;
        case '11':	$url .= 'bricolage.php';	break;
        case '12':	$url .= 'options.php';		break;
		case '13':	$url .= 'unconnect.php';	break;
    }
    if ($bLink) {
        return '<script language="javascript">window.location=\'' . $url . '\';</script>';
    } else {
        return $url;
    }
}
function FoundBatiment($idType = false, $login = false, $Coordonnees = false) {
	global $lstNonBatiment;
    $sql = "SELECT * FROM table_carte WHERE 
			login='". ($login ? $login : $_SESSION['joueur']) . "'"
            . ($idType ? " AND id_type_batiment=$idType" : "")
            . ($Coordonnees ? " AND coordonnee='$Coordonnees'" : "")
            . " AND detruit IS NULL;";
    
    $requete = mysql_query($sql) or die(mysql_error() . '<br />Function FoundBatiment SQL = ' . $sql);
    if (mysql_num_rows($requete) > 0) {
        $carte = mysql_fetch_array($requete, MYSQL_ASSOC);
		if(!in_array($carte['id_type_batiment'], $lstNonBatiment)){
			$sql2 = "SELECT * FROM table_batiment WHERE id_batiment=" . $carte['id_type_batiment'] . ";";
			$requete2 = mysql_query($sql2) or die(mysql_error() . '<br />' . $sql2);
			$batiment = mysql_fetch_array($requete2, MYSQL_ASSOC);
			return new $batiment['batiment_type']($carte, $batiment);
			//return new batiment($carte, $batiment);
		}
    } else {
        return null;
    }
}
function UpdateTransaction($id, $type='vendu') {
    $sql = "UPDATE table_marcher SET 
		acheteur=" . ($type == 'vendu' ? "'" . $_SESSION['joueur'] . "'" : 'NULL') . ", 
		status_vente=1 
		WHERE ID_troc=$id;";
    mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
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
        case 'marcher_cancel':		$Name = 'Annuler transaction';								break;
        case 'marcher_accept':		$Name = 'Accepter transaction';								break;
        case 'marcher_attention':	$Name = 'Transaction impossible';							break;
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
				$_SESSION['message']['alliance'] = '<p>Vous avez une ou des demande(s) d\'adhésion à votre alliance. Allez vite voir sur la page "<a href="./alliance.php">Alliance</a>".</p>';
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
function FinishAllCompetenceEnCours(&$oJoueur) {
    global $lstPoints;
    $sqlCmp = "SELECT * FROM table_competence WHERE cmp_login='" . $_SESSION['joueur'] . "' AND cmp_finish IS NULL";
    $rqtCmp = mysql_query($sqlCmp) or die(mysql_error() . '<br />' . $sqlCmp);
    while ($cmp = mysql_fetch_array($rqtCmp, MYSQL_ASSOC)) {
        if ((strtotime('now') - strtotime($cmp['cmp_date'])) >= $cmp['cmp_temp']) {
            $sql = "UPDATE  `table_competence` SET  `cmp_finish` =  TRUE WHERE `table_competence`.`cmp_id` =" . $cmp['cmp_id'] . ";";
            mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
            $oJoueur->UpdatePoints($lstPoints['CmpTerminé']);
            AddHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'Competence', NULL, NULL, 'Compétence terminée : '.$cmp['cmp_nom'].' de niveau '.$cmp['cmp_niveau']);
        }
    }
}
function CorrectDataInfoBulle($txtInfoBulle) {
    $txt = str_replace('"', '&quot;', $txtInfoBulle);
    $txt = str_replace('<', '&lt;', $txt);
    $txt = str_replace('>', '&gt;', $txt);
    $txt = str_replace("'", "\'", $txt);
    return $txt;
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
			<p>Vous avez demandé d\'être informé lors ' . ($type == 'attaque' ? 'd\'' : 'de ') . $type . '. Alors voici l\'information :</p>
			<div style="margin-left:50px; margin-right:50px; background:lightgrey;">"'
            . $info
            . '"</div>
			<p>Nous vous conseillons de vous connecter sur <a href="http://www.gaulix.be">www.gaulix.be</a> et de '
            . ($type == 'attaque' ? 'réparer votre bâtiment' : 'soigner votre personnage') . '.</p>'
            . '<p>A bientot,</p>
			<p style="margin-left:30px;">L\'équipe Gaulix</p>
			<br />
			<p>PS : Si vous ne désirez plus recevoir ce genre de message, connectez-vous sur <a href="http://www.gaulix.be">www.gaulix.be</a>, allez dans les options et désactivez ce que vous voulez. Et pour supprimer votre joueur, c\'est au même endroit.</p>
		</body>
	</html>';

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'From: Admin Gaulix<admin@gaulix.be>' . "\r\n";

    //echo $Message;
    mail($To, $Sujet, $Message, $headers);
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
    
    if ($_SESSION['main']['uri'] == 3
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
			}else{$InfoBulle = '<table class="equipement"><tr><th>Votre livre est vide.</th></tr></table>';}
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
function AfficheBatiment(&$batiment, &$oJoueur, $PageVillage = false){
	global $NiveauMaxBatiment;
	//$nbReparer = 0;
	$ImgSize = 'height';
	$txt = '
	<table class="village">';

	$contenu = 'Ne peut rien contenir';
	$PositionBatiment	= implode(',', array_merge(array($batiment->GetCarte()),$batiment->GetCoordonnee()));
	$PositionJoueur		= implode(',', array_merge(array($oJoueur->GetCarte()),$oJoueur->GetPosition()));
	$chkDruide = false;
	$chkMarcher = false;

	switch($batiment->GetType()){
		case 'maison':
			$ImgSize = 'width';
			if(!$PageVillage OR $PositionBatiment == $PositionJoueur){
				$contenu = '<p>Ne peut rien contenir.</p>';
				$chkDruide = true;
			}else{
				$contenu = '<p>Vous devez vous placez sur son emplacement pour afficher les options.</p>';
			}
			break;
		case 'bank':
			$contenu = $batiment->AfficheContenu($PageVillage, $oJoueur);
			break;
		case 'entrepot':
		case 'ferme':
		case 'mine' :
			$ImgSize = 'width';
			$contenu = $batiment->AfficheContenu($PageVillage, $oJoueur);
			break;
		case 'marcher':
			if($PositionBatiment == $PositionJoueur){
				$chkMarcher = true;
			}else{
				$contenu = '<p>Vous devez vous placez sur son emplacement pour afficher les transactions disponibles.</p>';
			}
			break;
	}
	$txt .= '
		<tr>
			<td rowspan="'.($batiment->GetType() == 'entrepot'?'5':'6').'" style="width:400px;">
				<img alt="'.$batiment->GetType().'" src="./img/batiments/'.$batiment->GetType().'-'.$batiment->GetNiveau().'.png" width="400px" />
			</td>
			<th colspan="4">'.$batiment->GetNom().' ('.$batiment->GetNiveau().' / '.$NiveauMaxBatiment.')</th>
		</tr>
		<tr>
			<td colspan="4">'.$batiment->AfficheOptionAmeliorer($oJoueur, $PageVillage).'</td>
		</tr>
		<tr>
			<td colspan="4">'.$batiment->GetDescription().'</td>
		</tr>
		<tr>
			<td colspan="4">'
				.'<img alt="Barre status" src="./fct/fct_image.php?type=statusetat&amp;value='.$batiment->GetEtat().'&amp;max='.$batiment->GetEtatMax().'" />'
				.'<br />'
				.$batiment->AfficheOptionReparer($oJoueur, $PageVillage)
			.'</td>
		</tr>
		<tr>
			<td colspan="4">
				<ul style="list-style-type:none; padding:0px; text-align:center; margin:0px;">'
					.'<li style="display:inline;">'.AfficheIcone('attaque').' : '.(is_null($batiment->GetAttaque())?'0':$batiment->GetAttaque()).'</li>'
					.'<li style="display:inline; margin-left:40px;">'.AfficheIcone('distance').' : '.(is_null($batiment->GetDistance())?'0':$batiment->GetDistance())	.'</li>'
					.'<li style="display:inline; margin-left:40px;">'.AfficheIcone('defense').' : '.(is_null($batiment->GetDefense())?'0':$batiment->GetDefense()).'</li>'
				.'</ul>
			</td>
		</tr>
		<tr>
			<td colspan="'.($batiment->GetType() == 'entrepot'?'5':'4').'">'.$contenu.'</td>
		</tr>'
	.($chkDruide?$batiment->AfficheDruide($oJoueur):'')
	.($chkMarcher?$batiment->AfficheTransactions($oJoueur):'')
	.'<tr style="background:lightgrey;"><td colspan="5">&nbsp;</td></tr>'
	.'</table>';
	return $txt;
}
function CheckIfAssezRessource(array $arRessource, personnage &$Joueur, maison &$Maison){
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
}
function AfficheLigneCouleur($Color, $IDLigne){
	if(($IDLigne % 2) == 0){
		return ' style="background:'.$Color.';"';
	}
	
	return NULL;
}

?>