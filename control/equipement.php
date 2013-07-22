<?php 
include_once('model/equipement.php');

$ChkDebugVar = false;

if($chkDebug AND $ChkDebugVar){	var_dump($_SESSION['Equipement']);}
if($chkDebug AND $ChkDebugVar){	var_dump($_GET);}
if($chkDebug AND $ChkDebugVar){	var_dump($_POST);}
if($chkDebug AND $ChkDebugVar){	echo '<hr />';}

$chkErr = true;
$CheckRetour = false;

if(isset($_POST['action'])){
	switch($_POST['action']){
		case 'unUse':			ActionUnuse($chkErr, $_POST['id'], $oJoueur); break;
	}
	unset($_POST['action']);
	$CheckRetour = true;
}

if($chkDebug AND ($ChkDebugVar OR !$chkErr)){
	var_dump($_SESSION['Bricolage']);
}

if($chkDebug OR !$chkErr){
	echo '<br /><a href="index.php?page=equipement">Retour</a>';
}elseif($CheckRetour AND !$chkDebug){
	//header('location: index.php?page=equipement');
	echo '<script type="text/javascript">window.location="index.php?page=equipement";</script>';
}
?>