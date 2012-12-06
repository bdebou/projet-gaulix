<?php
global $objManager, $chkDebug;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
print_r($_SESSION);
if($chkDebug){
	if(isset($_SESSION['LoginStatus'])){
		echo '$_SESSION[\'LoginStatus\']<br />';print_r($_SESSION['LoginStatus']);echo '<br />';
	}else{
		echo '$_SESSION[\'LoginStatus\']) not set<br />';
	}
	echo '$_GET<br />';print_r($_GET);echo '<br />';
	echo '$_POST<br />';print_r($_POST);echo '<br />';
	echo '<hr />';
}

$chkErr = true;
$CheckRetour = false;

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'deplacement':			ActionDeplacement($chkErr, $oJoueur); break;
	}
	unset($_GET['action']);
	$CheckRetour = true;
}

if($chkDebug){
	if(isset($_SESSION['LoginStatus'])){
		print_r($_SESSION['LoginStatus']);
	}else{
		echo '$_SESSION[\'LoginStatus\']) not set<br />';
	}
}

$objManager->update($oJoueur);
unset($oJoueur);

if($chkDebug OR !$chkErr){
	echo '<br /><a href="index.php?page='.(isset($_GET['retour'])?$_GET['retour']:'main').'">Retour</a>';
}elseif($CheckRetour){
	//header('location: index.php?page='.$_GET['retour']);
	echo '<script type="text/javascript">window.location="index.php?page='.(isset($_GET['retour'])?$_GET['retour']:'main').'";</script>';
}
?>