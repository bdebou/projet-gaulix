<?php
include_once('model/bricolage.php');

global $objManager, $chkDebug;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

$ChkDebugVar = false;

if($chkDebug AND $ChkDebugVar){	echo '$_SESSION[\'main\'][\'bricolage\']<br />';print_r($_SESSION['main']['bricolage']);echo '<br />';}
if($chkDebug AND $ChkDebugVar){	echo '$_GET<br />';print_r($_GET);echo '<br />';}
if($chkDebug AND $ChkDebugVar){	echo '$_POST<br />';print_r($_POST);echo '<br />';}
if($chkDebug AND $ChkDebugVar){	echo '<hr />';}

$chkErr = true;
$CheckRetour = false;

if(isset($_GET['action'])){
	$RetourOnglet = $_SESSION['main']['bricolage'][$_GET['id']]['type'];
	$strAnchor = $_SESSION['main']['bricolage'][$_GET['id']]['code'];
	switch($_GET['action']){
		case 'fabriquer':				ActionFabriquer($chkErr, $_GET['id'], $oJoueur, $objManager); break;
	}
	unset($_GET['action']);
	$CheckRetour = true;
}

if($chkDebug AND $ChkDebugVar){print_r($_SESSION['main']['bricolage']);}

$objManager->update($oJoueur);
unset($oJoueur);

if($chkDebug OR !$chkErr){
	echo '<br /><a href="index.php?page=bricolage'.(isset($strAnchor)?'&onglet='.$RetourOnglet.'#'.$strAnchor:'').'">Retour</a>';
}elseif($CheckRetour){
	header('location: index.php?page=bricolage'.(isset($strAnchor)?'&onglet='.$RetourOnglet.'#'.$strAnchor:''));
}

?>