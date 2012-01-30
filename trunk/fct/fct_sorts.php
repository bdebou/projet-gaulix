<?php
// On enregistre notre autoload
function chargerClasse($classname){require './'.$classname.'.class.php';}
spl_autoload_register('chargerClasse');

session_start();

include('config.php');
include('fct_main.php');


$chkDebug = false;

global $objManager;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

if($chkDebug){echo '$_SESSION[\'main\']<br />';print_r($_SESSION['main']);echo '<br />';}
if($chkDebug){echo '$_GET<br />';print_r($_GET);echo '<br />';}
if($chkDebug){echo '$_POST<br />';print_r($_POST);echo '<br />';}
if($chkDebug){echo 'objPersonnage<br />';print_r($oJoueur);echo '<br />';}
if($chkDebug){echo '<hr />';}

$chkErr = true;

if(isset($_GET['id']) AND $_SESSION['main'][$_GET['id']]['type'] == 'sort'){
	switch($_SESSION['main'][$_GET['id']]['code']){
		case 'SrtMaison':
			$oJoueur->UpdatePosition($oJoueur->GetMaisonInstalle());
			$oJoueur->CleanInventaire($_SESSION['main'][$_GET['id']]['code']);
			break;
		case 'SrtDefense10':
		case 'SrtAttaque10':
		case 'SrtDistance1':
			$oJoueur->EquiperPerso($_SESSION['main'][$_GET['id']]['code'], 'sort');
			break;
		case 'LvrDruides':
			$oJoueur->EquiperPerso($_SESSION['main'][$_GET['id']]['code'], 'livre');
			break;
		case 'SrtQuete':
			$sqlQ = "SELECT quete_position FROM table_quetes WHERE  quete_login='".$oJoueur->GetLogin()."' AND quete_reussi IS NULL;";
			$rqtLstQuete = mysql_query($sqlQ) or die (mysql_error().'<br />'.$sqlQ);
			if(mysql_num_rows($rqtLstQuete) == 0){
				$txtMessage = '<p>Vous n\'avez aucune quête en cours.</p>';
			}else{
				$txtMessage = null;
				$numQuete = 1;
				while($Quete = mysql_fetch_array($rqtLstQuete, MYSQL_ASSOC)){
					$arPosQuete = explode(',', $Quete['quete_position']);
					$txtMessage .= 'quête #'.$numQuete.' >>> (Carte '.ucfirst($arPosQuete['0']).', Ligne '.$arPosQuete['1'].', Colonne '.$arPosQuete['2'].')<br />';
					$numQuete++;
				}
			}
			$_SESSION['message'][] = $txtMessage;
			AddHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'Sort', 'Votre Druide', NULL, $txtMessage);
				//on supprime le sort du bolga
			$oJoueur->CleanInventaire($_SESSION['main'][$_GET['id']]['code']);
			break;
		default:
			$chkErr = false;
			echo 'Erreur GLX0003: Pas d\'action correcte <br />';
			print_r($_SESSION['main']);
	}
}else{
	$chkErr = false;
	echo 'Erreur: GLX0001';
}

if($chkDebug){print_r($_SESSION['main']);echo '<br />';}
if($chkDebug){echo 'objPersonnage<br />';print_r($oJoueur);echo '<br />';}

$objManager->update($oJoueur);

if($chkErr AND !$chkDebug){echo RetourPage($_SESSION['main']['uri'], true);}
else{echo '<br /><a href="'.RetourPage($_SESSION['main']['uri'], true, false).'">Retour</a>';}


//Les Sorts
//=========


?>