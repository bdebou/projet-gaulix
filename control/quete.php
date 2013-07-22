<?php
include_once('model/quete.php');

$ChkDebugVar = true;

if($chkDebug AND $ChkDebugVar){
	echo '$_SESSION[\'quete\']<br />';var_dump($_SESSION['quete']);echo '<br />';
	echo '$_GET<br />';var_dump($_GET);echo '<br />';
	echo '$_POST<br />';var_dump($_POST);echo '<br />';
	echo '<hr />';
}

$chkErr = true;
$CheckRetour = false;

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'inscription':		ActionInscriptionQuete($chkErr, $_GET['num_quete'], $oJoueur, $oMaison);	break;
		//case 'annule':			ActionAnnulerQuete($chkErr, $_GET['num_quete']);		break;
		case 'valider':			ActionValiderQuete($chkErr, $_GET['num_quete'], $oJoueur, $oMaison);		break;
	}
	
	unset($_GET['action']);
	
	$CheckRetour = true;
}

if($chkDebug AND $ChkDebugVar){
	var_dump($_SESSION['quete']);
}

if($chkDebug OR !$chkErr){
	echo '<br /><a href="index.php?page=quete">Retour</a>';
}elseif($CheckRetour AND !$chkDebug){
	//header('location: index.php?page=main');
	echo '<script type="text/javascript">window.location="index.php?page=quete";</script>';
}

?>