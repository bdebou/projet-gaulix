<?php
include_once('model/competences.php');

FinishAllCompetenceEnCours($oDB, $oJoueur);

$ChkDebugVar = false;

if($chkDebug AND $ChkDebugVar)
{
	echo '$_SESSION[\'competences\']<br />';var_dump($_SESSION['competences']);echo '<br />';
	echo '$_GET<br />';var_dump($_GET);echo '<br />';
	echo '$_POST<br />';var_dump($_POST);echo '<br />';
	echo '<hr />';
}


$chkErr = true;
$CheckRetour = false;

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'PerfAtt':					ActionPerfectionnement($chkErr, $oJoueur, personnage::TYPE_PERFECT_ATTAQUE); break;
		case 'PerfDef':					ActionPerfectionnement($chkErr, $oJoueur, personnage::TYPE_PERFECT_DEFENSE); break;
		case 'competence':				ActionCompetence($chkErr, $oJoueur, $_GET['cmp'], $oMaison); break;
	}
	unset($_GET['action']);
	$CheckRetour = true;
}elseif(isset($_POST['action'])){
	switch($_POST['action'])
	{
		case 'competence':				ActionCompetence($chkErr, $oJoueur, $_POST['cmp'], $oMaison); break;
	}
}

if($chkDebug AND $ChkDebugVar){var_dump($_SESSION['main']);}

if($chkDebug OR !$chkErr){
	echo '<br /><a href="index.php?page=competences">Retour</a>';
}elseif($CheckRetour){
	//header('location: index.php?page=competences');
	echo '<script type="text/javascript">window.location="index.php?page=competences";</script>';
}

?>