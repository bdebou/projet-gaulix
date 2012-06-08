<?php
include_once('model/inventaire.php');

global $objManager, $chkDebug;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

$ChkDebugVar = false;

if($chkDebug AND $ChkDebugVar){
	echo '$_SESSION[\'inventaire\']<br />';var_dump($_SESSION['inventaire']);echo '<br />';
	echo '$_GET<br />';var_dump($_GET);echo '<br />';
	echo '$_POST<br />';var_dump($_POST);echo '<br />';
	echo '<hr />';
}

$chkErr = true;
$CheckRetour = false;

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'sort':				ActionSorts($chkErr, $oJoueur); break;
	}
	unset($_GET['action']);
	$CheckRetour = true;
}elseif(isset($_POST['action'])){
	switch($_POST['action'])
	{
		case 'Vendre':				ActionVendre($chkErr, $_POST['id'], $oJoueur, $objManager, abs($_POST['qte'])); break;
		case 'Entreposer':			ActionEntreposer($chkErr, $objManager, $_GET['id'], $oJoueur); break;
		case 'Convertir':			ActionConvertir($chkErr, $_POST['id'], $oJoueur, $objManager, abs($_POST['qte'])); break;
		case 'Utiliser':			ActionUtiliser($chkErr, $_SESSION['inventaire'][$_POST['id']]['code'], $oJoueur, $objManager, abs($_POST['qte'])); break;
		case 'Abandonner':			ActionAbandonner($chkErr, $oJoueur, $_POST['id'], abs($_POST['qte'])); break;
		case 'Equiper':				ActionEquiper($chkErr, $_POST['id'], $oJoueur); break;
		case 'Sort':				ActionSorts($chkErr, $oJoueur); break;
		//case 'MettreBolga':			ActionMettreDansBolga($chkErr, $_POST['type'], $oJoueur, $objManager); break;
	}
	unset($_POST['action']);
	$CheckRetour = true;
}

if($chkDebug AND $ChkDebugVar){
	var_dump($_SESSION['inventaire']);
}

$objManager->update($oJoueur);
unset($oJoueur);

if($chkDebug OR !$chkErr){
	echo '<br /><a href="index.php?page=inventaire">Retour</a>';
}elseif($CheckRetour){
	//header('location: index.php?page=inventaire');
	echo '<script type="text/javascript">window.location="index.php?page=inventaire";</script>';
}
?>