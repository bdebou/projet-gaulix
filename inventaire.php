<?php
// On prolonge la session
function chargerClasse($classname){require './fct/'.$classname.'.class.php';}
spl_autoload_register('chargerClasse');

session_start();

// On teste si la variable de session existe et contient une valeur
if(!isset($_SESSION['joueur'])) {
    // Si inexistante ou nulle, on redirige vers le formulaire de login
	echo '<script language="javascript">window.location="./index.php";</script>';
    exit();
}else{
	include('./fct/config.php');
	include('./fct/fct_main.php');
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<?php echo AfficheHead();?>
	<title>Gaulix - Inventaire</title>
</head>
<body>
<div class="container">
<div id="curseur" class="infobulle"></div>
<div class="loginstatus"><?php RefreshData(2); echo affiche_LoginStatus(); $_SESSION['QueteEnCours'] = FoundQueteEnCours();?></div>
<div class="carte"><?php echo affiche_carte();?></div>
<div class="pub">
	<script type="text/javascript"><!--
		google_ad_client = "ca-pub-2161674761092050";
		/* Pub Fight 2 */
		google_ad_slot = "3236602268";
		google_ad_width = 300;
		google_ad_height = 250;
		//-->
	</script>
	<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</div>
<div class="mouvements"><?php echo affiche_mouvements();?></div>
<div class="actions"><?php affiche_actions();?></div>
<div class="menu"><?php echo affiche_menu();?></div>
<div class="module_social"><?php echo AfficheModuleSocial();?></div>

<div class="main">
<h1>Votre Bolga <dfn>(Sac)</dfn></h1>
<?php
global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
?>
<table class="inventaire">
	<tr>
	<td rowspan="4" class="corp">
		<table class="corps">
			<tr><td colspan="2"></td><td class="membre" style="height:30px;"><?php echo AfficheEquipement(1, $oJoueur);?></td><td colspan="2">&nbsp;</td></tr>
			<tr><td class="membre" style="height:80px; width: 15px;"><?php echo AfficheEquipement(5, $oJoueur);?></td><td colspan="3" class="membre" style=""><?php echo AfficheEquipement(4, $oJoueur);?></td><td class="membre" style="height:80px; width: 15px;"><?php echo AfficheEquipement(2, $oJoueur);?></td></tr>
			<tr><td>&nbsp;</td><td class="membre" style="height: 80px;"><?php echo AfficheEquipement(3, $oJoueur);?></td><td>&nbsp;</td><td class="membre" style="height: 80px;"><?php echo AfficheEquipement(3, $oJoueur);?></td><td>&nbsp;</td></tr>
			<tr><td colspan="5">&nbsp;</td></tr>
			<tr><td class="membre"><?php echo AfficheEquipement(7, $oJoueur);?></td><td colspan="3">&nbsp;</td><td class="membre"><?php echo AfficheEquipement(6, $oJoueur);?></td></tr>
		</table>
	</td>
	</tr>
<?php
$id = 0;
$numC = 0;
$numL = 0;
if(!is_null($oJoueur->GetLstInventaire())){
	foreach($oJoueur->GetLstInventaire() as $Objet){
		$arObjet = explode('=', $Objet);
		
		if(substr($arObjet['0'], 0, 5) == 'Tissu'){$arObjet['0'] = 'Tissu';}
		
		$sql = "SELECT * FROM table_objets WHERE objet_code='".strval($arObjet['0'])."';";
		$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
		$result = mysql_fetch_array($requete, MYSQL_ASSOC);
		
		if($numC == 0){
			echo '<tr style="vertical-align:top;"><td>'.AfficheObjetInventaire($arObjet['0'], $result, $id, $arObjet['1'], $oJoueur).'</td>';
			$numC=1;
			$numL++;
		}elseif($numC == 1){
			echo '<td>'.AfficheObjetInventaire($arObjet['0'], $result, $id, $arObjet['1'], $oJoueur).'</td>';
			if($numL < 4){
				$numC = 0;
				echo '</tr>';
			}else{$numC = 2;}
		}elseif($numC == 2){
			echo '<td>'.AfficheObjetInventaire($arObjet['0'], $result, $id, $arObjet['1'], $oJoueur).'</td></tr>';
			$numC = 0;
		}
		$id++;
	}
}
For($i=$id; $i<=($oJoueur->QuelCapaciteMonBolga() -1);$i++){
	if($numC == 0){
		echo '<tr style="vertical-align:top;"><td>'.AffichePlaceVide().'</td>';
		$numC = 1;
		$numL++;
	}elseif($numC == 1){
		echo '<td>'.AffichePlaceVide().'</td>';
		if($numL < 4){
			$numC = 0;
			echo '</tr>';
		}else{$numC = 2;}
	}elseif($numC == 2){
		echo '<td>'.AffichePlaceVide().'</td></tr>';
		$numC = 0;
	}
}

$objManager->update($oJoueur);
unset($oJoueur);
?>
	</tr>
</table>
</div>

<div class="version">
	<?php echo AfficheFooter(true);?>
</div>
</div>
</body>
</html>
<?php
function AffichePlaceVide(){
	return '
<table width="100%">
	<tr style="background:lightgrey; line-height:5px;"><td colspan="8">&nbsp;</td></tr>
	<tr>
		<td rowspan="4" style="width:80px;">Vide</td>
		<td colspan="6" class="tdtitre" title="Libre">Libre</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
		<td colspan="2">&nbsp;</td>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="6">&nbsp;</td>
	</tr>
</table>';
}
function AfficheObjetInventaire($CodeObject, $arInfoObject, $id, $nbObjet, &$oJoueur){
	global $VieMaximum, $DeplacementMax;
	
	$_SESSION['main'][$id]['action'] = false;
	
	if(in_array($arInfoObject['objet_type'], array('ressource', 'potion'))){
		$_SESSION['main'][$id]['type'] = QuelTypeRessource($CodeObject);
	}else{
		$_SESSION['main'][$id]['type'] = $arInfoObject['objet_type'];
	}
	
	if(isset($arInfoObject['objet_attaque'])){
		$_SESSION['main'][$id]['value'] = $arInfoObject['objet_attaque'];
	}elseif($arInfoObject['objet_type'] == 'objet'){
		$_SESSION['main'][$id]['value'] = 1;
	}else{
		$_SESSION['main'][$id]['value'] = $arInfoObject['objet_nb'];
	}
	
	$_SESSION['main'][$id]['code'] = $CodeObject;
	$_SESSION['main'][$id]['prix'] = $arInfoObject['objet_prix'];
	
	$txtType = null;
	$IconeName = $arInfoObject['objet_type'];
	$reSizeImg = 100;
	
	switch($arInfoObject['objet_type']){
		case 'objet':
			$IconeName = $CodeObject;
			break;
		case 'sort':
			$txtType = '<button class="inventaire" type="button" onclick="window.location=\'./fct/fct_sorts.php?id='.$id.'\'">Utiliser</button>';
			break;
		case 'ressource':
		case 'potion':
			if(in_array($_SESSION['main'][$id]['type'], array('nourriture', 'bois', 'pierre'))){
					//Cas si l'objet est de la nourriture, bois ou pierre
				if(is_null($oJoueur->GetMaisonInstalle())){
					$txtType = 'Pas Encore de Maison';
				}else{
					$txtType = '<button class="inventaire" type="button" onclick="window.location=\'./fct/main.php?action=utiliser&amp;id='.$id.'\'">Utiliser</button>';
				}
			}elseif(in_array($_SESSION['main'][$id]['type'], array('argent'))){
					//Cas si l'objet est de l'argent
				$txtType = '<button class="inventaire" type="button" onclick="window.location=\'./fct/main.php?action=utiliser&amp;id='.$id.'\'">Utiliser</button>';
				$_SESSION['main'][$id]['type'] = 'argent';
			}elseif(in_array($_SESSION['main'][$id]['type'], array('vie', 'deplacement'))
					OR in_array(substr($CodeObject, 0, 6), array('PotVie', 'PotDep'))
				){
					//Cas si l'objet est de la vie ou déplacement
				if(	(in_array(substr($CodeObject, 0, 6), array('ResVie', 'PotVie')) AND ($oJoueur->GetVie() + $_SESSION['main'][$id]['value']) <= $VieMaximum)
					OR (in_array(substr($CodeObject, 0, 6), array('ResDep','PotDep')) AND ($oJoueur->GetDepDispo() + $_SESSION['main'][$id]['value']) <= $DeplacementMax)){
						$txtType = '<button class="inventaire" type="button" onclick="window.location=\'./fct/main.php?action=utiliser&amp;id='.$id.'\'">Utiliser</button>';
				}else{
					$txtType = 'Max atteint';
				}
			}
			
			$IconeName = $CodeObject;
			break;
		case 'casque':
		case 'bouclier':
		case 'cuirasse':
		case 'jambiere':
		case 'arme':
		case 'sac':
			$reSizeImg = 150;
			$txtType = '<button class="inventaire" type="button" onclick="window.location=\'./fct/main.php?action=equiper&amp;id='.$id.'\'">Equiper</button>';
			break;
		default: 
	}
	
	if(	in_array($arInfoObject['objet_type'], array('casque', 'bouclier', 'cuirasse', 'jambiere', 'arme'))){
		$txtInfo = '
		<td colspan="2">'.AfficheIcone('attaque').' : '.$arInfoObject['objet_attaque'].'</td>
		<td colspan="2">'.AfficheIcone('defense').' : '.$arInfoObject['objet_defense'].'</td>
		<td colspan="2">'.AfficheIcone('distance').' : '.$arInfoObject['objet_distance'].'</td>';
		$InfoBulle = '<table class="equipement">'
						.'<tr><th colspan="3">'.$arInfoObject['objet_nom'].'</th></tr>'
						.'<tr>'
							.'<td>'.AfficheIcone('attaque').' : '.$arInfoObject['objet_attaque'].'</td>'
							.'<td>'.AfficheIcone('defense').' : '.$arInfoObject['objet_defense'].'</td>'
							.'<td>'.AfficheIcone('distance').' : '.$arInfoObject['objet_distance'].'</td>'
						.'</tr>'
					.'</table>';
	}elseif(in_array($arInfoObject['objet_type'], array('sort'))){
		$txtInfo = NULL;
		$InfoBulle = '<table class="equipement">'
						.'<tr><th>'.$arInfoObject['objet_nom'].'</th></tr>'
						.'<tr>'
							.'<td>'.$arInfoObject['objet_description'].'</td>'
						.'</tr>'
					.'</table>';
	}else{
		$txtInfo = '
		<td colspan="6">'.AfficheIcone($IconeName).' : '.$_SESSION['main'][$id]['value'].'</td>';
		$InfoBulle = '<table class="equipement">'
						.'<tr><th>'.$arInfoObject['objet_nom'].'</th></tr>'
						.'<tr>'
							.'<td>'.$arInfoObject['objet_description'].'</td>'
						.'</tr>'
					.'</table>';
	}
	
	//Le check si on est sur un entrepot ou pas
	if(is_null(FoundBatiment(4, NULL, implode(',', array_merge(array($oJoueur->GetCarte()), $oJoueur->GetPosition()))))){
		//on est pas sur l'entrepot
		$checkEntrepot = false;
	}else{
		//on est sur l'entrepot
		$checkEntrepot = true;
	}
	
	//Les bouttons de ventes
	if($oJoueur->CheckSiObjetPeutEtreGroupe($CodeObject, $arInfoObject['objet_type'])){
		$txtButton = null;
		foreach(array(1, 10, 100) as $StepVente){
			if($nbObjet >= $StepVente){
				if($checkEntrepot){
					$InfoBulleBtV = '<table class="InfoBulle"><tr><td>Entreposer '.$StepVente.'x '.$arInfoObject['objet_nom'].'</td></tr></table>';
				}else{
					$InfoBulleBtV = '<table class="InfoBulle"><tr><td>Vendre '.$StepVente.'x '.$arInfoObject['objet_nom'].' pour le prix de '.($StepVente * $arInfoObject['objet_prix']).' '.AfficheIcone('or', 15).'</td></tr></table>';
				}
					$txtButton .= '
			<button type="button" class="inventaire" '
				.'onclick="window.location=\'./fct/main.php?action='.($checkEntrepot?'entreposer':'vendre').'&amp;id='.$id.'&amp;qte='.$StepVente.'\'" '
				.'onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulleBtV).'\');" '
				.'onmouseout="cache();" '
				.'>'
				.($checkEntrepot?$StepVente.'x Entreposer':$StepVente.'x Vendre')
			.'</button>'
			.'<br />';
			}
		}
	}else{
		if($checkEntrepot){
			$InfoBulleBtV = '<table class="InfoBulle"><tr><td>Entreposer 1x '.$arInfoObject['objet_nom'].'</td></tr></table>';
		}else{
			$InfoBulleBtV = '<table class="InfoBulle"><tr><td>Vendre 1x '.$arInfoObject['objet_nom'].' pour le prix de '.$arInfoObject['objet_prix'].' '.AfficheIcone('or', 15).'</td></tr></table>';
		}
		$txtButton = '
		<button type="button" class="inventaire" '
			.'onclick="window.location=\'./fct/main.php?action='.($checkEntrepot?'entreposer':'vendre').'&amp;id='.$id.'&amp;qte=1\'" '
			.'onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulleBtV).'\');" '
			.'onmouseout="cache();" '
			.'>'
			.($checkEntrepot?'Entreposer':'Vendre')
		.'</button>';
	}
	
	$InfoBulleBtJ = '<table class="InfoBulle"><tr><td>'.AfficheIcone('attention').'Si vous libérez cet emplacement, il vous sera impossible de récupérer les '.($nbObjet > 1?'<b>'.$nbObjet.'x </b>':'').$arInfoObject['objet_nom'].'</td></tr></table>';
	
	$txt =  '
<table width="100%">
	<tr style="background:lightgrey; line-height:5px;"><td colspan="8">&nbsp;</td></tr>
	<tr>
		<td rowspan="4" style="width:80px;">'.AfficheInfoObjet($CodeObject, $reSizeImg).'</td>
		<td colspan="6" class="tdtitre">'.($nbObjet > 1?'<b>'.$nbObjet.'x </b>':'').$arInfoObject['objet_nom'].'</td>
	</tr>
	<tr>'
	.$txtInfo
	.'</tr>
	<tr>
		<td colspan="6">'.AfficheIcone('or').' : '.$arInfoObject['objet_prix'].'</td>
	</tr>
	<tr>
		<td colspan="6">'
		.(!is_null($txtType)?
			$txtType.'<br />'
			:'')
		.$txtButton
		.'<button type="button" class="inventaire" '
			.'onclick="window.location=\'./fct/main.php?action=laisser&amp;id='.$id.'\'"'
			.'onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulleBtJ).'\');" '
			.'onmouseout="cache();" '
			.'>'
			.'Libérer'
		.'</button>'
		.'</td>
	</tr>
</table>';
return $txt;
}
?>