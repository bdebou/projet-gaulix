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
	//include('./fct/fct_batiment.php');
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<?php echo AfficheHead();?>
	<title>Gaulix - Votre Oppidum</title>
</head>
<body>
<div class="container">
<div id="curseur" class="infobulle"></div>
<div class="loginstatus"><?php RefreshData(8); echo affiche_LoginStatus(); $_SESSION['QueteEnCours'] = FoundQueteEnCours();?></div>
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
<div class="village">
<h2>Mon Oppidum <dfn>(Village)</dfn></h2>
<?php
	global $objManager, $lstNonBatiment;
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	$sql = "SELECT * FROM table_carte WHERE login='".$_SESSION['joueur']."' AND detruit IS NULL;";
	$requete = mysql_query($sql) or die ( mysql_error() );
	if(mysql_num_rows($requete) > 0){
		$txt = null;
		while($carte = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if(!in_array($carte['id_type_batiment'], $lstNonBatiment)){
				$txt .= AfficheBatiment(FoundBatiment(NULL, NULL, $carte['coordonnee']), $oJoueur, true);
			}
		}
		echo $txt;
	}
	
	$objManager->update($oJoueur);
	unset($oJoueur);
?>
</div>
</div>

<div class="version">
	<?php echo AfficheFooter(true);?>
</div>
</div>
</body>
</html>