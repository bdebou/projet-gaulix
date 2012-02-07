<?php
include('model/bricolage.php');

global $objManager, $chkDebug;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

if($chkDebug){	echo '$_SESSION[\'main\']<br />';print_r($_SESSION['main']);echo '<br />';}
if($chkDebug){	echo '$_GET<br />';print_r($_GET);echo '<br />';}
if($chkDebug){	echo '$_POST<br />';print_r($_POST);echo '<br />';}
if($chkDebug){	echo '<hr />';}

$chkErr = true;
$CheckRetour = false;

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'fabriquer':				ActionFabriquer($chkErr, $_GET['id'], $oJoueur, $objManager); break;
	}
	unset($_GET['action']);
	$CheckRetour = true;
}

if($chkDebug){print_r($_SESSION['main']);}

$objManager->update($oJoueur);
unset($oJoueur);

if($chkDebug){
	echo '<br /><a href="index.php?page=bricolage">Retour</a>';
}

if($CheckRetour){
	header('location: index.php?page=bricolage');
}

?>