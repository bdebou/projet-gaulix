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
	<title>Gaulix - Scores</title>
</head>
<body>
<div class="container">
<div id="curseur" class="infobulle"></div>
<div class="loginstatus"><?php RefreshData(5); echo affiche_LoginStatus(); $_SESSION['QueteEnCours'] = FoundQueteEnCours();?></div>
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
<h2>Scores</h2>
	<h3>Le Classement</h3>
	<table class="classement">
		<?php echo AfficheLignesClassement();?>
	</table>
	<hr />
	<table>
		<tr style="vertical-align:top;">
		<td style="width:400px;">
			<p>Pour rappel, voici la liste des gains de points pour chaque action :</p>
			<p>Chaque <u>construction de batiment</u> vous rapportera un nombre spécifique de points, mais vous les predrez si il est détruit.</p>
			<p>Chaque <u>Quête</u> vous apporte un nombre spécifique de points.</p>
		</td>
		<td>
			<?php include_once('./fct/fct_login.php');echo AfficheTableauGainScores();?>
		</td>
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
function AfficheLignesClassement(){
	global $VieMaximum;
	//$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	$txt = '
			<tr style="background:grey;">
				<th style="width:60px;">Points</th>
				<th style="width:160px;">Nom (Niveau)</th>
				<th style="width:150px;">Expérience</th>
				<th style="width:150px;">'.AfficheIcone('vie').'</th>
				<th>'.AfficheIcone('attaque').'</th>
				<th>'.AfficheIcone('defense').'</th>
				<th>Combats<br />Gagnés</th>
				<th>Combats<br />Perdus</th>
			</tr>';
	$sql = "SELECT login, niveau, experience, val_attaque, val_defense, vie, nb_points, nb_victoire, nb_vaincu, clan FROM table_joueurs ORDER BY nb_points DESC, niveau DESC, experience DESC;";
	$requete = mysql_query($sql) or die (mysql_error());
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		$txt.= '
			<tr>'
				.'<td>'.$row['nb_points'].'</td>'
				.'<td style="background:#c8c8c8; text-align:left;">'.$row['login'].' ('.$row['niveau'].')'.AfficheRecompenses($row['login'], $row['clan']).'</td>'
				.'<td><img alt="Barre d\'expérience" src="./fct/fct_image.php?type=experience&amp;value='.$row['experience'].'&amp;max='.(($row['niveau'] + 1) * 100).'" /></td>'
				.'<td><img alt="Barre de Vie" src="./fct/fct_image.php?type=vie&amp;value='.$row['vie'].'&amp;max='.$VieMaximum.'" /></td>'
				.'<td>'.$row['val_attaque'].'</td>'
				.'<td>'.$row['val_defense'].'</td>'
				.'<td>'.$row['nb_victoire'].'</td>'
				.'<td>'.$row['nb_vaincu'].'</td>'
			.'</tr>';
	}
	return $txt;
}
function AfficheLigneScore(){
	$txt=null;
	$sql = "SELECT login, nb_combats, nb_victoire, nb_vaincu FROM table_joueurs ORDER BY login;";
	$requete = mysql_query($sql) or die (mysql_error());
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		$txt.= '
			<tr>'
				.'<td style="background:#c8c8c8; text-align:left;">'.$row['login'].'</td>'
				.'<td>'.$row['nb_combats'].'</td>'
				.'<td>'.$row['nb_victoire'].'</td>'
				.'<td>'.$row['nb_vaincu'].'</td>'
			.'</tr>';
	}
	return $txt;
}
?>