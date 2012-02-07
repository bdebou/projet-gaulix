<?php
function AfficheLesDebatsDuClan(personnage &$oJoueur){
	If(is_null($oJoueur->GetClan())){
		return 'Créez ou inscrivez-vous dans une alliance pour pouvoir chatter avec les membres de votre alliance.';
	}elseif($oJoueur->GetClan() == '1'){
		return 'Pas encore accepté dans l\'alliance.';
	}else{
		$debat = new DebatDeClan($oJoueur->GetClan());
		return $debat->AfficheDebat($oJoueur);
	}
}
function AfficheListeDesClans(personnage &$oJoueur){
	$chkMembre = false;
	$txt = '
	<table class="clans">
		<tr>
			<th>Nom et Chef de clan</th>
			<th>Membres</th>
			<th>Actions</th>
		</tr>';
	
	$sql = "SELECT * FROM table_alliance WHERE membre_actif IS NULL ORDER BY nom_clan ASC;";
	$requete = mysql_query($sql) or die (mysql_error());
	
	if(mysql_num_rows($requete)>0){
		$precedentNom = null;
		$nbMembres = 0;
		$txtM = null;
		$chkA = false;
		
		$nbLigne = 0;
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if($row['membre_clan'] == $oJoueur->GetLogin()){$chkMembre=true;}
			if($precedentNom != $row['nom_clan'] AND !is_null($precedentNom)){
				$txt .= '
		<tr'.AfficheLigneCouleur('#C0C0C0', $nbLigne).'>
			<td rowspan="'.$nbMembres.'">
				Clan : "<b>'.htmlspecialchars_decode($precedentNom, ENT_QUOTES).'</b>"'.AfficheRecompenses(NULL, $precedentNom).'<br />Chef : '.$precedentChef.'<br />'
					//On affiche le boutton pour s'inscrire à l'alliance
				.(($row['membre_clan'] != $oJoueur->GetLogin() AND is_null($oJoueur->GetClan()))?FormInscriptionClan($precedentNom, $precedentChef):'')
			.'</td>'.$txtM;
				
				$nbLigne++;
				$nbMembres = 1;
				$txtM = null;
				$chkA = false;
			}else{$nbMembres++;}
			
			if($chkA){
				$txtM .= '
		<tr'.AfficheLigneCouleur('#C0C0C0', $nbLigne).'>';
			}
			$chkA = true;
			$NomClan = $row['nom_clan'];
			$ChefClan = $row['chef_clan'];
			$txtM .= '
			<td>'.$row['membre_clan'].''.AfficheRecompenses($row['membre_clan']).'</td>';
			if($row['membre_clan'] == $row['chef_clan'] AND $row['chef_clan'] == $oJoueur->GetLogin()){
				$txtM .= '<td>'
							.FormSupprimerClan($row['nom_clan'])
						.'</td>';
			}elseif($row['membre_clan'] == $oJoueur->GetLogin() AND $row['chef_clan']!=$oJoueur->GetLogin()){
				if(!is_null($oJoueur->GetClan()) AND $oJoueur->GetClan() != '1'){
					$txtM .= '<td>'
								.FormDesinscriptionClan($row['nom_clan'])
							.'</td>';
				}else{
					$txtM .= '<td>En Cours d\'acceptation</td>';
				}
			}elseif($row['chef_clan']==$oJoueur->GetLogin() AND $row['membre_clan'] != $oJoueur->GetLogin()){
				if(is_null($row['date_inscription'])){
					$txtM .= '<td>'
								.FormRemoveMembre($row['nom_clan'], $row['membre_clan'])
							.'</td>';
				}else{
					$txtM .= '<td>'
								.FormAccepteInscription($row['nom_clan'], $row['membre_clan'])
							.'</td>';
				}
			}else{$txtM .= '<td>&nbsp;</td>';}
			$txtM .= '</tr>';
		
			$precedentNom = $row['nom_clan'];
			$precedentChef = $row['chef_clan'];
		}
		$txt .= '
		<tr'.AfficheLigneCouleur('#C0C0C0', $nbLigne).'>
			<td rowspan="'.$nbMembres.'">
				Clan : "<b>'.htmlspecialchars_decode($precedentNom, ENT_QUOTES).'</b>"'.AfficheRecompenses(NULL, $precedentNom).'<br />Chef : '.$precedentChef.'<br />'
				.(is_null($oJoueur->GetClan())?FormInscriptionClan($precedentNom, $precedentChef):'')
			.'</td>'.$txtM;
	}else{$txt .= '
		<tr><td colspan="3" style="text-align:center;">Il y a encore aucun clan.</td></tr>';
	}
	if(!$chkMembre AND is_null($oJoueur->GetClan())){
		$txt .= '
		<tr>
			<td colspan="3">'
				.'<form method="post">
					<input type="hidden" name="page" value="alliance" />
					<input type="hidden" name="action" value="clanadd" />
					<input type="text" name="newclan" size="20" />
					<input type="submit" value="Créer un nouveau clan" />
				</form>'
			.'</td>
		</tr>';
	}
	$txt .= '
	</table>';
	
	return $txt;
}
function FormAccepteInscription($clan, $membre){
	return '<form method="get" action="index.php">
				<input type="hidden" name="page" value="alliance" />
				<input type="hidden" name="action" value="clanaccepterinscription" />
				<input type="hidden" name="nomclan" value="'.$clan.'" />
				<input type="hidden" name="membre" value="'.$membre.'" />
				<input type="submit" value="Accepter" />
			</form>';
}
function FormSupprimerClan($nom){
	return '<form method="get" action="index.php">
				<input type="hidden" name="page" value="alliance" />
				<input type="hidden" name="action" value="clansupprimer" />
				<input type="hidden" name="nomclan" value="'.$nom.'" />
				<input type="submit" value="Supprimer clan" />
			</form>';
}
function FormInscriptionClan($nom, $chef){
	return '<form method="get" action="index.php">
				<input type="hidden" name="page" value="alliance" />
				<input type="hidden" name="action" value="claninscription" />
				<input type="hidden" name="nomclan" value="'.$nom.'" />
				<input type="hidden" name="chefclan" value="'.$chef.'" />
				<input type="submit" value="S\'inscrire" />
			</form>';
}
function FormDesinscriptionClan($nom){
	return '<form method="get" action="index.php">
				<input type="hidden" name="page" value="alliance" />
				<input type="hidden" name="action" value="clandesinscription" />
				<input type="hidden" name="nomclan" value="'.$nom.'" />
				<input type="submit" value="Se désinscrire" />
			</form>';
}
function FormRemoveMembre($clan, $membre){
	return '<form method="get" action="index.php">
				<input type="hidden" name="page" value="alliance" />
				<input type="hidden" name="action" value="clandesinscrire" />
				<input type="hidden" name="nomclan" value="'.$clan.'" />
				<input type="hidden" name="membre" value="'.$membre.'" />
				<input type="submit" value="Désinscrire" />
			</form>';
}
function AfficheLigneCouleur($Color, $IDLigne){
	if(($IDLigne % 2) == 0){
		return ' style="background:'.$Color.';"';
	}

	return NULL;
}
//=====================
//Gestion des Alliances
//=====================
function ActionSupprimerClan(&$objManager, &$oJoueur, $NomClan){
	if(isset($NomClan)){
		//on désinscrit tous les membres
		$sql = "SELECT id_alliance, membre_clan FROM `table_alliance` WHERE nom_clan='".htmlspecialchars($NomClan, ENT_QUOTES)."' AND chef_clan='".$_SESSION['joueur']."';";
		$requete = mysql_query($sql) or die (mysql_error());
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			$persoMembre = $objManager->GetPersoLogin($row['membre_clan']);
			$persoMembre->DesinscriptionClan();
			$objManager->update($persoMembre);
			unset($persoMembre);
		}

		//on supprime l'enregistrement de ce membre
		$sql = "DELETE FROM table_alliance WHERE nom_clan='".htmlspecialchars($NomClan, ENT_QUOTES)."';";
		mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);

		//on Supprime les chat de clan
		$sql = "DELETE FROM table_chat WHERE clan_chat='".htmlspecialchars($NomClan, ENT_QUOTES)."';";
		mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);

		//on supprime le joueur du clan
		$oJoueur->DesinscriptionClan();
	}
}
function ActionDesinscrireMembreClan(&$objManager, $NomClan, $Membre){
	if(isset($NomClan) AND isset($Membre)){
		//on met à jour la liste des membres
		$sql = "UPDATE `table_alliance` SET membre_actif=1 WHERE nom_clan='".htmlspecialchars($NomClan, ENT_QUOTES)."' AND membre_clan='".$Membre."';";
		mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);
		//on ajoute le joueur au clan
		$persoMembre = $objManager->GetPersoLogin($Membre);
		$persoMembre->DesinscriptionClan();
		$objManager->update($persoMembre);
		//on supprime la variable
		unset($persoMembre);
	}
}
function ActionDesinscriptionClan(&$oJoueur, $NomClan){
	if(isset($NomClan)){
		//on met à jour la liste des membres
		$sql = "UPDATE `table_alliance` SET membre_actif=1 WHERE nom_clan='".htmlspecialchars($NomClan, ENT_QUOTES)."' AND membre_clan='".$oJoueur->GetLogin()."';";
		mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);
		//on ajoute le joueur au clan
		$oJoueur->DesinscriptionClan();
	}
}
function ActionInscriptionClan(&$oJoueur, $NomClan, $ChefClan){
	if(isset($NomClan) AND isset($ChefClan)){
		//on ajoute le membre à la liste des membres
		$sql = "INSERT INTO `table_alliance` (`id_alliance`, `chef_clan`, `nom_clan`, `membre_clan`, `date_inscription`, `membre_actif`)
				VALUES (NULL, '".$ChefClan."', '".htmlspecialchars($NomClan, ENT_QUOTES)."', '".$oJoueur->GetLogin()."', '".date('Y-m-d H:i:s')."', NULL);";
		mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);
		//on ajoute le joueur au clan
		$oJoueur->InscriptionClan('1');
	}
}
function ActionAjoutClan(&$oJoueur, $AddClan){
	if(isset($AddClan) AND !empty($AddClan) AND !is_null($AddClan)){
		if(!CheckIfClanExiste($AddClan)){
			//on ajoute le clan
			$sql="INSERT INTO `table_alliance` (`id_alliance`, `chef_clan`, `nom_clan`, `membre_clan`, `date_inscription`, `membre_actif`)
				VALUES (NULL, '".$oJoueur->GetLogin()."', '".htmlspecialchars($AddClan, ENT_QUOTES)."', '".$oJoueur->GetLogin()."', NULL, NULL);";
			mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);
			//on inscrit le joueur dans le clan
			$oJoueur->InscriptionClan($AddClan);
		}
	}
}
function ActionAccpeterInscriptionClan(&$objManager, $NomClan, $Membre){
	if(isset($NomClan) AND isset($Membre)){
		//on met à jour la liste des membres
		$sql = "UPDATE `table_alliance` SET date_inscription=NULL WHERE nom_clan='".htmlspecialchars($NomClan, ENT_QUOTES)."' AND membre_clan='".$Membre."' AND membre_actif IS NULL;";
		mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);
		//on ajoute le joueur au clan
		$persoMembre = $objManager->GetPersoLogin($Membre);
		$persoMembre->InscriptionClan($NomClan);
		$objManager->update($persoMembre);
		//on supprime la variable
		unset($persoMembre);
		unset($_SESSION['message']['alliance']);
	}
}

//===============
//Gestion du chat
//===============
function ActionAddReccordChat($arReccord){
	if(isset($arReccord)){
		$sql = "INSERT INTO `table_chat` (`id_chat`, `clan_chat`, `member_chat`, `date_chat`, `text_chat`)
					VALUES (NULL, '".htmlspecialchars($arReccord['clan'], ENT_QUOTES)."', '".$arReccord['membre']."', '".date('Y-m-d H:i:s')."', '".htmlspecialchars($arReccord['text'], ENT_QUOTES)."');";
		mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);
	}
}
function ActionRemoveReccordChat($id){
	$sql = "DELETE FROM table_chat WHERE id_chat=".$_SESSION['chat'][$id].";";
	mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);
}

?>