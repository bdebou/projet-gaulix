<?php
include('model/main.php');
include('model/carte.php');
include('model/village.php');

global $objManager, $chkDebug;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

FinishAllCompetenceEnCours($oJoueur);

$ChkDebugVar = false;

if($chkDebug AND $ChkDebugVar){
	echo '$_SESSION[\'main\']<br />';print_r($_SESSION['main']);echo '<br />';
	echo '$_GET<br />';print_r($_GET);echo '<br />';
	echo '$_POST<br />';print_r($_POST);echo '<br />';
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
		case 'utiliser':
			if(isset($_GET['id'])){
				ActionUtiliser($chkErr, $_SESSION['main'][$_GET['id']], $oJoueur, $objManager);
			}else{					ActionUtiliser($chkErr, $_SESSION['main']['objet'], $oJoueur, $objManager);
			}
			break;
		case 'laisser':				ActionLaisser($chkErr, $oJoueur); break;
		case 'viderstockmine':		ActionViderStock($chkErr, 18, 'mine', $oJoueur, $objManager); break;
		case 'viderstockferme':		ActionViderStock($chkErr, 6, 'ferme', $oJoueur, $objManager); break;
	}
	if(isset($_GET['action'])){unset($_GET['action']);}
	
	$CheckRetour = true;
}

if($chkDebug AND $ChkDebugVar){
	print_r($_SESSION['main']);
}

$objManager->update($oJoueur);
unset($oJoueur);

if($chkDebug){
	echo '<br /><a href="index.php?page=main">Retour</a>';
}
if($CheckRetour AND !$chkDebug){
	header('location: index.php?page=main');
}
?>