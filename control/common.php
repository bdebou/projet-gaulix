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

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'MettreBolga':			ActionMettreDansBolga($chkErr, $_GET['type'], $oJoueur, $objManager); break;
		case 'deplacement':			ActionDeplacement($chkErr, $oJoueur); break;
	}
	unset($_GET['action']);
}

if($chkDebug){
	print_r($_SESSION['main']['LoginStatus']);
}

$objManager->update($oJoueur);
unset($oJoueur);

header('Location: ./index.php?page='.$_GET['retour']);
?>