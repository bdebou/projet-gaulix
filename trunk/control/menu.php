<?php
include_once('model/menu.php');

global $objManager, $chkDebug;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

$arBtMenu[0] = array('link' => './',							'name' => 'Principal');
$arBtMenu[1] = array('link' => './index.php?page=inventaire',	'name' => 'Bolga');
$arBtMenu[2] = array('link' => './index.php?page=equipement',	'name' => 'Equipements');
$arBtMenu[3] = array('link' => './index.php?page=competences',	'name' => 'Compétences');
$arBtMenu[4] = array('link' => './index.php?page=bricolage',	'name' => 'Artisanat');
$arBtMenu[5] = array('link' => './index.php?page=quete',		'name' => 'Quêtes '.count($_SESSION['QueteEnCours']).'/'.(count($_SESSION['QueteEnCours']) + CombienQueteDisponible($oJoueur)));
$arBtMenu[6] = array('link' => './index.php?page=scores',		'name' => 'Scores');
$arBtMenu[7] = array('link' => './index.php?page=village',		'name' => 'Oppidum');
$arBtMenu[8] = array('link' => './index.php?page=alliance',		'name' => 'Alliances');
if(AfficheNbMessageAlliance($oJoueur->GetClan(), date('Y-m-d H:i:s', $oJoueur->GetDateLasMessageLu())) > 0){
	$arBtMenu[8]['name'] .= ' <span style="color:red;font-size:12px;">'
								.AfficheNbMessageAlliance($oJoueur->GetClan(), date('Y-m-d H:i:s', $oJoueur->GetDateLasMessageLu()))
								.' '.AfficheIcone('Message', 12)
							.'</span>';
}
$arBtMenu[9] = array('link' => './index.php?page=cartes',		'name' => 'Carte');
$arBtMenu[10] = array('link' => './index.php?page=regles',		'name' => 'Règles');
$arBtMenu[11] = array('link' => './index.php?page=options',		'name' => AfficheIcone('options', 15) . ' Options');

include('view/menu.php');

$objManager->update($oJoueur);
unset($oJoueur);


?>