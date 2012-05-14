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
	ActionTransactionMarche($chkErr, $oJoueur, $objManager);
	$_GET['anchor'] = $_POST['anchor'];
	$CheckRetour = true;
}elseif(isset($_GET['action'])){
	switch($_GET['action']){
		case 'ameliorer':				ActionAmeliorerBatiment($chkErr, $oJoueur, $objManager, $_GET['id']); break;
		case 'reparer':					ActionReparer($chkErr, $_GET['id'], $_GET['num'], $oJoueur, $objManager); break;
		case 'reprendre':				ActionReprendre($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'viderstockferme':			ActionViderStock($chkErr, ferme::ID_BATIMENT, 'ferme', $oJoueur, $objManager); break;
		case 'productionferme':			ActionProduction($chkErr, ferme::ID_BATIMENT, 'ferme', $_GET['type'], $oJoueur, $objManager); break;
		case 'viderstockmine':			ActionViderStock($chkErr, mine::ID_BATIMENT, 'mine', $oJoueur, $objManager); break;
		case 'productionmine':			ActionProduction($chkErr, mine::ID_BATIMENT, 'mine', $_GET['type'], $oJoueur, $objManager); break;
		case 'druide':					ActionDruide($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'VenteMarche':				ActionVenteMarche($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'annulertransaction':		ActionAnnulerTransaction($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'acceptertransaction':		ActionAccepterTransaction($chkErr, $_GET['id'], $oJoueur, $objManager); break;
	}
	$CheckRetour = true;
}

if($chkDebug AND $ChkDebugVar){print_r($_SESSION['main']);}

$lstBatiment = CreateListBatiment($oJoueur);

$objManager->update($oJoueur);
unset($oJoueur);

if($chkDebug OR !$chkErr){
	echo '<br /><a href="index.php?page=village">Retour</a>';
}elseif(($CheckRetour AND !$chkDebug)){
	//header('location: index.php?page=village'.(isset($_GET['anchor'])?'#'.$_GET['anchor']:''));
	if(isset($_GET['anchor'])){
		$txtAnchor = '#'.$_GET['anchor'];
	}elseif(isset($_POST['anchor'])){
		$txtAnchor = '#'.$_POST['anchor'];
	}
	echo '<script type="text/javascript">window.location="index.php?page=village'.(isset($txtAnchor)?$txtAnchor:NULL).'";</script>';
}

?>