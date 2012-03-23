<?php 
include_once('model/equipement.php');

global $objManager, $chkDebug;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

$chkErr = true;
$CheckRetour = false;

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'unuse':			ActionUnuse($chkErr, $oJoueur); break;
	}
	unset($_GET['action']);
	$CheckRetour = true;
}

$objManager->update($oJoueur);
unset($oJoueur);

if($chkDebug OR !$chkErr){
	echo '<br /><a href="index.php?page=equipement">Retour</a>';
}elseif($CheckRetour AND !$chkDebug){
	//header('location: index.php?page=equipement');
	echo '<script type="text/javascript">window.location="index.php?page=equipement";</script>';
}
?>