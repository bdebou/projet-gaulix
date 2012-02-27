<?php
include_once('model/alliance.php');

global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

if (!is_null($oJoueur->GetClan()) AND $oJoueur->GetClan() != 1){
	$oJoueur->SetLastMessageLu();
}

$CheckRetour = false;

if(isset($_POST['action'])){
	switch($_POST['action']){
		case 'clanadd': 				ActionAjoutClan($oJoueur, $_POST['newclan']);										break;
		case 'chataddreccord':			ActionAddReccordChat(array('clan'=>$_POST['clan'], 'membre'=>$_POST['member'], 'text'=>$_POST['text']));	break;
		case 'chatremovereccord':		ActionRemoveReccordChat($_POST['id']);																		break;
	}
	unset($_POST['action']);
	$CheckRetour = true;
	
}elseif(isset($_GET['action'])){
	switch($_GET['action']){
		case 'claninscription':			ActionInscriptionClan($oJoueur, $_GET['nomclan'], $_GET['chefclan']);				break;
		case 'clandesinscription':		ActionDesinscriptionClan($oJoueur, $_GET['nomclan']);								break;
		case 'clandesinscrire': 		ActionDesinscrireMembreClan($objManager, $_GET['nomclan'], $_GET['membre']);		break;
		case 'clansupprimer': 			ActionSupprimerClan($objManager, $oJoueur, $_GET['nomclan']);						break;
		case 'clanaccepterinscription': ActionAccpeterInscriptionClan($objManager, $_GET['nomclan'], $_GET['membre']);		break;
	}
	unset($_GET['action']);
	$CheckRetour = true;
}

$objManager->update($oJoueur);
unset($oJoueur);

if($CheckRetour){
	header('location: index.php?page=alliance');
}

?>