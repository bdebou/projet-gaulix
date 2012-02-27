<?php
include_once('model/inventaire.php');

global $objManager, $chkDebug;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

if($chkDebug){
	echo '$_SESSION[\'main\']<br />';print_r($_SESSION['main']);echo '<br />';
	echo '$_GET<br />';print_r($_GET);echo '<br />';
	echo '$_POST<br />';print_r($_POST);echo '<br />';
	echo '<hr />';
}

$chkErr = true;
$CheckRetour = false;

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'utiliser':
			if(isset($_GET['id'])){	ActionUtiliser($chkErr, $_SESSION['main'][$_GET['id']], $oJoueur, $objManager);
			}else{					ActionUtiliser($chkErr, $_SESSION['main']['objet'], $oJoueur, $objManager);}
			break;
		case 'equiper':
			if(isset($_GET['id'])){	ActionEquiper($chkErr, $_SESSION['main'][$_GET['id']], $oJoueur);
			}else{					ActionEquiper($chkErr, $_SESSION['main']['objet'], $oJoueur);}
			break;
		case 'entreposer':			ActionEntreposer($chkErr, $objManager, $_GET['id'], $oJoueur); break;
		case 'vendre':				ActionVendre($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'laisser':				ActionLaisser($chkErr, $oJoueur); break;
		case 'sort':				ActionSorts($chkErr, $oJoueur); break;
	}
	unset($_GET['action']);
	$CheckRetour = true;
}

if($chkDebug){
	print_r($_SESSION['main']);
}

$objManager->update($oJoueur);
unset($oJoueur);

if($chkDebug OR !$chkErr){
	echo '<br /><a href="index.php?page=inventaire">Retour</a>';
}elseif($CheckRetour){
	header('location: index.php?page=inventaire');
}
?>