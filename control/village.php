<?php
include('model/village.php');

global $objManager, $chkDebug;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

$ChkDebugVar = false;

if($chkDebug AND $ChkDebugVar){	echo '$_SESSION[\'main\']<br />';print_r($_SESSION['main']);echo '<br />';}
if($chkDebug AND $ChkDebugVar){	echo '$_GET<br />';print_r($_GET);echo '<br />';}
if($chkDebug AND $ChkDebugVar){	echo '$_POST<br />';print_r($_POST);echo '<br />';}
if($chkDebug AND $ChkDebugVar){	echo '<hr />';}

$chkErr = true;
$CheckRetour = false;

if(isset($_POST['depot'])){
	ActionDepot($chkErr, $oJoueur, $objManager);
	$CheckRetour = true;
}elseif(isset($_POST['retrait'])){
	ActionRetrait($chkErr, $oJoueur, $objManager);
	$CheckRetour = true;
}elseif(isset($_POST['transaction'])){
	ActionTransactionMarcher($chkErr, $oJoueur, $objManager);
	$CheckRetour = true;
}elseif(isset($_GET['action'])){
	switch($_GET['action']){
		case 'ameliorer':				ActionAmeliorerBatiment($chkErr, $oJoueur, $objManager, $_GET['id']); break;
		case 'reparer':					ActionReparer($chkErr, $_GET['id'], $_GET['num'], $oJoueur, $objManager); break;
		case 'reprendre':				ActionReprendre($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'stockerferme':			ActionStocker($chkErr, 6, 'ferme', $objManager, $oJoueur); break;
		case 'viderstockferme':			ActionViderStock($chkErr, 6, 'ferme', $oJoueur, $objManager); break;
		case 'productionferme':			ActionProduction($chkErr, 6, 'ferme', $_GET['type'], $oJoueur, $objManager); break;
		case 'stockermine':				ActionStocker($chkErr, 18, 'mine', $objManager, $oJoueur); break;
		case 'viderstockmine':			ActionViderStock($chkErr, 18, 'mine', $oJoueur, $objManager); break;
		case 'productionmine':			ActionProduction($chkErr, 18, 'mine', $_GET['type'], $oJoueur, $objManager); break;
		case 'druide':					ActionDruide($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'VenteMarcher':			ActionVenteMarcher($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'annulertransaction':		ActionAnnulerTransaction($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'acceptertransaction':		ActionAccepterTransaction($chkErr, $_GET['id'], $oJoueur, $objManager); break;
	}
	$CheckRetour = true;
}

if($chkDebug AND $ChkDebugVar){print_r($_SESSION['main']);}

$objManager->update($oJoueur);
unset($oJoueur);

$lstBatiment = CreateListBatiment();

if($chkDebug){
	echo '<br /><a href="index.php?page=village">Retour</a>';
}

if($CheckRetour AND !$chkDebug){
	header('location: index.php?page=village');
}

?>