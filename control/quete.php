<?php
include_once('model/quete.php');

$_SESSION['QueteEnCours'] = FoundQueteEnCours();

$CheckA = false;

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'inscription':		InscriptionQuete($_GET['num_quete']);	break;
		case 'annule':			ClotureQuete($_GET['num_quete']);		break;
	}
	unset($_GET['action']);
	$CheckA = true;
}

if($CheckA){
	//header('location: index.php?page=quete');
	echo '<script type="text/javascript">window.location="index.php?page=quete";</script>';
}
?>