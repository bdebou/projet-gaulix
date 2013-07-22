<?php
include_once('model/bricolage.php');

$ChkDebugVar = false;

if($chkDebug AND $ChkDebugVar){	var_dump($_SESSION['Bricolage']);}
if($chkDebug AND $ChkDebugVar){	var_dump($_GET);}
if($chkDebug AND $ChkDebugVar){	var_dump($_POST);}
if($chkDebug AND $ChkDebugVar){	echo '<hr />';}

$chkErr = true;
$CheckRetour = false;

if(isset($_POST['action'])){
	$RetourOnglet = $_SESSION['Bricolage'][$_POST['id']]['type'];
	$strAnchor = $_SESSION['Bricolage'][$_POST['id']]['code'];
	switch($_POST['action']){
		case 'Fabriquer':				ActionFabriquer($chkErr, $_POST['id'], $oJoueur, $oMaison); break;
	}
	unset($_POST['action']);
	$CheckRetour = true;
}

if($chkDebug AND ($ChkDebugVar OR !$chkErr)){var_dump($_SESSION['Bricolage']);}

if($chkDebug OR !$chkErr){
	echo '<br /><a href="index.php?page=bricolage'.(isset($strAnchor)?'&onglet='.$RetourOnglet.'#'.$strAnchor:'').'">Retour</a>';
}elseif($CheckRetour){
	//header('location: index.php?page=bricolage'.(isset($strAnchor)?'&onglet='.$RetourOnglet.'#'.$strAnchor:''));
	echo '<script type="text/javascript">window.location="index.php?page=bricolage'.(isset($strAnchor)?'&onglet='.$RetourOnglet.'#'.$strAnchor:'').'";</script>';
}

?>