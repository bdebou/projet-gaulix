<?php 
include('model/equipement.php');

global $objManager, $chkDebug;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

$CheckA = false;

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'unuse':			ActionUnuse($chkErr, $oJoueur); break;
	}
	unset($_GET['action']);
	$CheckA = true;
}

$objManager->update($oJoueur);
unset($oJoueur);

if($CheckA){
	header('location: index.php?page=equipement');
}
?>