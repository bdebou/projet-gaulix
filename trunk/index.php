<?php
// On prolonge la session
function chargerClasse($classname){require './fct/'.$classname.'.class.php';}
spl_autoload_register('chargerClasse');

session_start();

require('./fct/config.php');
require('./fct/fct_main.php');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<?php echo AfficheHead();?>
	<title>Gaulix</title>
</head>
<body>
<div class="container">
<div id="curseur" class="infobulle"></div>
<div id="fb-root"></div>

<?php
if(!isset($_SESSION['joueur'])){
	include('./fct/fct_login.php');
	echo '
	<div class="login">';
	if(isset($_POST['login']) and !empty($_POST['login']) and isset($_POST['motdepasse']) and !empty($_POST['motdepasse'])){
		echo CheckIfLoginMPCorrect($_POST['login'], $_POST['motdepasse']);
	}else{
		echo AfficheFormLogin();
		//echo '<p style="color:red; font-size:24px; text-align:center; font-weight:bold;">Bientôt</p>';
	}
	echo '
	</div>';
	echo AfficheInfosRegles();
}else{
	//include('./fct/fct_batiment.php');
	//include('./fct/fct_main.php');
	
?>
<div class="loginstatus"><?php RefreshData(1); echo affiche_LoginStatus(); $_SESSION['QueteEnCours'] = FoundQueteEnCours();?></div>
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
<?php
	//print_r($_SESSION);
?>
<div class="history">
<h2>Historique</h2>
<?php
	echo AfficheHistory();
?>
</div>
<hr />
<?php

if(isset($_SESSION['QueteEnCours'])){
	global $objManager;
	echo '
	<table class="quetes">';
	$nbCol = 0;
	foreach($_SESSION['QueteEnCours'] as $Quete){
		if($nbCol == 0){
			echo '
		<tr>';
		}
		echo '<td>'.AfficheAvancementQuete($Quete).'</td>';
		$nbCol++;
		if($nbCol == 2){
			echo '
		</tr>';
			$nbCol = 0;
		}
	}
	echo '
	</table>';
	reset($_SESSION['QueteEnCours']);
}
	$batiment = FoundBatiment(null, null, $_SESSION['main']['position']);
	if(!is_null($batiment) AND $batiment->GetType() != 'ressource'){
		$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
		echo '
<hr />
	<div style="text-align:center;">'
		.AfficheBatiment($batiment, $oJoueur)
	.'</div>';
		$objManager->update($oJoueur);
		unset($oJoueur);
	}
?>

</div>
<?php
}
?>
<div class="version">
	<?php echo AfficheFooter(true);?>
</div>
</div>
</body>
</html>

<?php

function AfficheHistory(){
	global $objManager;
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	$sql = "SELECT * FROM table_history WHERE 
		history_login='".$oJoueur->GetLogin()."' 
		AND history_type IN ('combat', 'attaque', 'ressucite', 'quete', 'sort', 'voleur') 
		ORDER BY history_date DESC LIMIT 0, 5;";
	$requete = mysql_query($sql) or die (mysql_error());
	if(mysql_num_rows($requete) > 0){
		$txt = '
	<table class="history">
		<tr>
			<th>Date</th>
			<th>Position</th>
			<th>Contre</th>
			<th>Resultat</th>
		</tr>';
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			$txt .= '
		<tr>
			<td style="width:120px;">'.date('j M - G:i:s', strtotime($row['history_date'])).'</td>
			<td style="width:60px; text-align:center;">'.$row['history_position'].'</td>
			<td style="width:100px;">'.$row['history_adversaire'].'</td>
			<td style="width:500px;">'.html_entity_decode($row['history_info'], ENT_QUOTES).'</td>
		</tr>';
		}
		$txt .= '
	</table>';
	}else{$txt = '<p>Pas d\'historique</p>';}
	
	$objManager->update($oJoueur);
	unset($oJoueur);
	
	return $txt;
}

?>