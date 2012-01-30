<?php
// On prolonge la session
function chargerClasse($classname){require './fct/'.$classname.'.class.php';}
spl_autoload_register('chargerClasse');

session_start();

// On teste si la variable de session existe et contient une valeur
if(!isset($_SESSION['joueur'])) {
    // Si inexistante ou nulle, on redirige vers le formulaire de login
	echo '<script language="javascript">window.location="./";</script>';
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
	<title>Gaulix - Equipement</title>
</head>
<body>
<div class="container">
<div id="curseur" class="infobulle"></div>
<div class="loginstatus"><?php RefreshData(3); echo affiche_LoginStatus(); $_SESSION['QueteEnCours'] = FoundQueteEnCours();?></div>
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
<h2>Equipement</h2>
<p>Cliquez sur un objet pour le remettre dans votre inventaire.</p>
<?php
global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

if($oJoueur->GetDisPerso() > 0){echo '<p>Grace à votre arme employée, vous pouvez combattre vos ennemis qui sont à une distance de '.$oJoueur->GetDisPerso().'. Vous verrez automatiquement les joueurs qui sont à portée de tire dans la partie "Action".</p>';}
?>
<div class="equipement">
<table style="width:100%;">
	<tr>
	<td style="width:300px; border:1px solid black;">
	<table class="corps">
		<tr><td colspan="2"></td><td class="membre" style="height:30px;"><?php echo AfficheEquipement(1, $oJoueur);?></td><td colspan="2">&nbsp;</td></tr>
		<tr><td class="membre" style="height:80px; width: 15px;"><?php echo AfficheEquipement(5, $oJoueur);?></td><td colspan="3" class="membre" style=""><?php echo AfficheEquipement(4, $oJoueur);?></td><td class="membre" style="height:80px; width: 15px;"><?php echo AfficheEquipement(2, $oJoueur);?></td></tr>
		<tr><td>&nbsp;</td><td class="membre" style="height: 80px;"><?php echo AfficheEquipement(3, $oJoueur);?></td><td>&nbsp;</td><td class="membre" style="height: 80px;"><?php echo AfficheEquipement(3, $oJoueur);?></td><td>&nbsp;</td></tr>
		<tr><td colspan="5">&nbsp;</td></tr>
		<tr><td class="membre"><?php echo AfficheEquipement(7, $oJoueur);?></td><td colspan="3">&nbsp;</td><td class="membre"><?php echo AfficheEquipement(6, $oJoueur);?></td></tr>
	</table>
	</td>
	<td style="border:1px solid black;">
<?php
	echo AfficheDescriptifEquipement($oJoueur);
	
$objManager->update($oJoueur);
unset($oJoueur);
?>
	</td>
	</tr>
</table>
</div>
</div>

<div class="version">
	<?php echo AfficheFooter(true);?>
</div>
</div>
</body>
</html>
<?php
function AfficheDescriptifEquipement(&$oJoueur){
	$txt = '
	<table class="equipement">
		<tr style="background:lightgrey;">
			<td colspan="8">&nbsp;</td>
		</tr>';
	for($i=1;$i<=5;$i++){
		switch($i){
			case 1: $CodeObjet = $oJoueur->GetCasque();		$txtNom = 'Casque';		break;
			case 2: $CodeObjet = $oJoueur->GetArme();		$txtNom = 'Arme';		break;
			case 3: $CodeObjet = $oJoueur->GetCuirasse();	$txtNom = 'Cuirasse';	break;
			case 4: $CodeObjet = $oJoueur->GetBouclier();	$txtNom = 'Bouclier';	break;
			case 5: $CodeObjet = $oJoueur->GetJambiere();	$txtNom = 'Jambière';	break;
		}
		if(!is_null($CodeObjet)){
			//$sql = "SELECT * FROM table_bricolage WHERE objet_code='".$CodeObjet."';";
			$sql = "SELECT * FROM table_objets WHERE objet_code='".$CodeObjet."';";
			$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
			$result = mysql_fetch_array($requete, MYSQL_ASSOC);
			$txt .= '
		<tr>
			<td rowspan="3" style="width:80px;">
				<a href="./fct/main.php?action=unuse&amp;id='.$i.'">'.AfficheInfoObjet($result['objet_code'], 100).'</a>
			</td>
			<td>'.$result['objet_nom'].'</td>
			<td colspan="2">'.AfficheIcone('attaque').' : '.$result['objet_attaque'].'</td>
			<td colspan="2">'.AfficheIcone('defense').' : '.$result['objet_defense'].'</td>
			<td colspan="2">'.AfficheIcone('distance').' : '.$result['objet_distance'].'</td>
		</tr>
		<tr>
			<td rowspan="2">'.$result['objet_description'].'</td>
			<td colspan="3">Niv = '.$result['objet_niveau'].'</td>
			<td colspan="3">'.AfficheIcone('or').' : '.$result['objet_prix'].'</td>
		</tr>
		<tr>
			<td colspan="6"><a href="./fct/main.php?action=unuse&amp;id='.$i.'">Remettre dans mon Bolga</a></td>
		</tr>
		<tr style="background:lightgrey;">
			<td colspan="8">&nbsp;</td>
		</tr>';
		}else{
			$txt .= '
		<tr>
			<td rowspan="3" style="width:100px;">'
			.$txtNom.'</td><td>Nom</td><td colspan="2">Attaque</td><td colspan="2">Defense</td><td colspan="2">Distance</td>
		</tr>
		<tr>
			<td rowspan="2">Description</td><td colspan="3">Niveau</td><td colspan="3">Prix</td>
		</tr>
		<tr>
			<td colspan="6">Actions</td>
		</tr>
		<tr style="background:lightgrey;">
			<td colspan="8">&nbsp;</td>
		</tr>';
		}
	}
	$txt .= '
	</table>';
	return $txt;
}

?>