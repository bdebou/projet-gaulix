<?php
include('model/competences.php');

global $objManager, $chkDebug;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

FinishAllCompetenceEnCours($oJoueur);

if($chkDebug){	echo '$_SESSION[\'main\']<br />';print_r($_SESSION['main']);echo '<br />';}
if($chkDebug){	echo '$_GET<br />';print_r($_GET);echo '<br />';}
if($chkDebug){	echo '$_POST<br />';print_r($_POST);echo '<br />';}
if($chkDebug){	echo '<hr />';}

$chkErr = true;
$CheckRetour = false;

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'PerfAtt':					ActionPerfAtt($chkErr, $oJoueur); break;
		case 'PerfDef':					ActionPerfDef($chkErr, $oJoueur); break;
		case 'competence':				ActionCompetence($chkErr, $oJoueur, $_GET['cmp'], $objManager); break;
	}
	unset($_GET['action']);
	$CheckRetour = true;
}

if($chkDebug){print_r($_SESSION['main']);}

$objManager->update($oJoueur);
unset($oJoueur);

if($chkDebug){
	echo '<br /><a href="index.php?page=competences">Retour</a>';
}

if($CheckRetour){
	header('location: index.php?page=competences');
}

?>