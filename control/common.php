<?php
if($chkDebug){
	if(isset($_SESSION['LoginStatus'])){
		echo '$_SESSION[\'LoginStatus\']<br />';var_dump($_SESSION['LoginStatus']);
	}else{
		echo '$_SESSION[\'LoginStatus\']) not set<br />';
	}
	echo '$_GET<br />';var_dump($_GET);
	echo '$_POST<br />';var_dump($_POST);
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
		var_dump($_SESSION['LoginStatus']);
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