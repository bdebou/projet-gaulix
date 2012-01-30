<?php
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