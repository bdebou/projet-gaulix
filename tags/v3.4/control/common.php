<?php
global $objManager, $chkDebug;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

if($chkDebug){
	echo '$_SESSION[\'main\']<br />';print_r($_SESSION['main']['LoginStatus']);echo '<br />';
	echo '$_GET<br />';print_r($_GET);echo '<br />';
	echo '$_POST<br />';print_r($_POST);echo '<br />';
	echo '<hr />';
}

$chkErr = true;
$CheckRetour = false;

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'MettreBolga':			ActionMettreDansBolga($chkErr, $_GET['type'], $oJoueur, $objManager); break;
		case 'deplacement':			ActionDeplacement($chkErr, $oJoueur); break;
	}
	unset($_GET['action']);
	$CheckRetour = true;
}

if($chkDebug){
	print_r($_SESSION['main']['LoginStatus']);
}

$objManager->update($oJoueur);
unset($oJoueur);

if($chkDebug OR !$chkErr){
	echo '<br /><a href="index.php?page='.$_GET['retour'].'">Retour</a>';
}elseif($CheckRetour){
	header('location: index.php?page='.$_GET['retour']);
}
?>