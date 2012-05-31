<?php
include_once('model/main.php');
include_once('model/carte.php');
include_once('model/village.php');
include_once('model/quete.php');

global $objManager, $chkDebug;

$_SESSION['QueteEnCours'] = FoundQueteEnCours();

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

FinishAllCompetenceEnCours($oJoueur);

$ChkDebugVar = false;

if($chkDebug AND $ChkDebugVar){
	echo '$_SESSION[\'main\']<br />';var_dump($_SESSION['main']);echo '<br />';
	echo '$_GET<br />';var_dump($_GET);echo '<br />';
	echo '$_POST<br />';var_dump($_POST);echo '<br />';
	echo '<hr />';
}

$chkErr = true;
$CheckRetour = false;

if(isset($_GET['move']) AND $oJoueur->GetDepDispo() > 0){
	ActionMove($chkErr, $oJoueur, $objManager);
	$CheckRetour = true;
}elseif(isset($_GET['action'])){
	switch($_GET['action']){
		case 'stock':				ActionStock($chkErr, $oJoueur); break;
		case 'ressource':			ActionRessource($chkErr, $oJoueur, $objManager, (isset($_GET['id'])?$_GET['id']:NULL)); break;
		case 'chasser':				ActionChasser($chkErr, $oJoueur, $objManager); break;
		case 'frapper':				ActionFrapper($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'attaquer':			ActionAttaquer($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'legionnaire':			ActionLegionnaire($chkErr, $oJoueur); break;
		case 'construire':			ActionConstruire($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'quete':				ActionQuete($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'utiliser':			ActionUtiliser($chkErr, $_SESSION['main']['objet']['code'], $oJoueur, $objManager); break;
		case 'laisser':				ActionLaisser($chkErr, $oJoueur); break;
		case 'viderstockmine':		ActionViderStock($chkErr, mine::ID_BATIMENT, 'mine', $oJoueur, $objManager); break;
		case 'viderstockferme':		ActionViderStock($chkErr, ferme::ID_BATIMENT, 'ferme', $oJoueur, $objManager); break;
		case 'viderstockcarriere':	ActionViderStock($chkErr, carriere::ID_BATIMENT, 'carriere', $oJoueur, $objManager); break;
		case 'viderstockpotager':	ActionViderStock($chkErr, potager::ID_BATIMENT, 'potager', $oJoueur, $objManager); break;
	}
	if(isset($_GET['action'])){unset($_GET['action']);}
	
	$CheckRetour = true;
}

if($chkDebug AND $ChkDebugVar){
	var_dump($_SESSION['main']);
}

$objManager->update($oJoueur);
unset($oJoueur);

if($chkDebug OR !$chkErr){
	echo '<br /><a href="index.php?page=main">Retour</a>';
}elseif($CheckRetour AND !$chkDebug){
	//header('location: index.php?page=main');
	echo '<script type="text/javascript">window.location="index.php?page=main";</script>';
}
?>