<?php
function Supprimer_Compte(personnage &$oJoueur){
		//on supprimer les batiments du joueur
	$sql = "SELECT id_case_carte FROM  table_carte WHERE login='" . $oJoueur->GetLogin() . "' AND id_type_batiment NOT IN (7, 8, 10, 11, 12, 13, 14, 15, 16, 17);";
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
		$sqlRemove = "DELETE FROM table_carte WHERE id_case_carte=".intval($row['id_case_carte']).";";
		mysql_query($sqlRemove) or die(mysql_error() . '<br />' . $sqlRemove);
		//echo $sqlRemove;
	}
		
		//on libère les ressources qui sont peut-etre encore en cours
	if(isset($_SESSION['main']['ressource'])){
		if($_SESSION['main']['ressource']->GetCollecteur() == $oJoueur->GetLogin()){
			$_SESSION['main']['ressource']->FreeRessource($oJoueur);
		}
		$objManager->UpdateRessource($_SESSION['main']['ressource']);
		unset($_SESSION['main']['ressource']);
	}
		
		//on reset la liste des quetes
	ResetListeQuetes($oJoueur->GetLogin());
		
		//on reset la liste des compétences
	$sql = "SELECT cmp_id FROM table_competence WHERE cmp_login='" . $oJoueur->GetLogin() . "';";
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
		$sqlRemove = "DELETE FROM table_competence WHERE cmp_id=" . intval($row['cmp_id']) . ";";
		mysql_query($sqlRemove) or die(mysql_error() . '<br />' . $sqlRemove);
		//echo $sqlRemove;
	}
		
		//On reset toutes les transactions au marcher
	$sql = "SELECT ID_troc FROM table_marcher WHERE vendeur='" . $oJoueur->GetLogin() . "';";
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
		$sqlRemove = "DELETE FROM table_marcher WHERE ID_troc=" . intval($row['ID_troc']) . ";";
		mysql_query($sqlRemove) or die(mysql_error() . '<br />' . $sqlRemove);
		//echo $sqlRemove;
	}
		
		//on reset les history du joueur
	$sql = "SELECT history_id FROM table_history WHERE history_login='" . $oJoueur->GetLogin() . "';";
	$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
		$sqlRemove = "DELETE FROM table_history WHERE history_id=" . intval($row['history_id']) . ";";
		mysql_query($sqlRemove) or die(mysql_error() . '<br />' . $sqlRemove);
		//echo $sqlRemove;
	}
		
		//on supprime le clan
	include('model/alliance.php');
	$sql = "SELECT nom_clan FROM table_alliance WHERE chef_clan='".$oJoueur->GetLogin()."';";
	$requete = mysql_query($sql) or die (mysql_error());
	if(mysql_num_rows($requete)>0){
		while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
			ActionSupprimerClan($objManager, $oJoueur, $row['nom_clan']);
		}
	}else{
		ActionDesinscrireMembreClan($objManager, $oJoueur->GetClan(), $oJoueur->GetLogin());
	}
		
		//on supprime le joueur
	$sqlRemove = "DELETE FROM table_joueurs WHERE login='".$oJoueur->GetLogin()."';";
	$requete = mysql_query($sqlRemove) or die (mysql_error() . '<br />' . $sqlRemove);
		//echo $sqlRemove;
	
}
function ChangeNotification(personnage &$oJoueur){
	if(isset($_POST['NotAttaque'])){
		switch($_POST['NotAttaque']){
			case 'yes' :	$oJoueur->SetNotification('attaque', true);	break;
			case 'no' :		$oJoueur->SetNotification('attaque', false);break;
		}
	}
	if(isset($_POST['NotCombat'])){
		switch($_POST['NotCombat']){
			case 'yes' :	$oJoueur->SetNotification('combat', true);	break;
			case 'no' :		$oJoueur->SetNotification('combat', false);	break;
		}
	}
}
function recup_email(){
		$sql = "SELECT mail FROM table_joueurs WHERE login='".$_SESSION['joueur']."'";
		$requete = mysql_query($sql) or die ( mysql_error() );
		$result = mysql_fetch_array($requete, MYSQL_ASSOC);
		return $result['mail'];
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