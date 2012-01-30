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
	<title>Gaulix - Quêtes</title>
</head>
<body>
<div class="container">
<div id="curseur" class="infobulle"></div>
<div class="loginstatus"><?php RefreshData(7); echo affiche_LoginStatus(); $_SESSION['QueteEnCours'] = FoundQueteEnCours();?></div>
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
<h2>Les quêtes</h2>
<p>Voici la liste des quêtes qui vous sont proposées. Acceptez une ou plusieurs quêtes et bonne chance!</p>
<p><?php echo AfficheIcone('attention');?> Les quêtes ne peuvent s'accomplir que une seule fois. Si vous l'annulée, elle sera perdue.</p>
<?php
$_SESSION['QueteEnCours'] = FoundQueteEnCours();
if(isset($_POST['inscription'])){
	InscriptionQuete($_POST['num_quete']);
}elseif(isset($_POST['annule'])){
	AnnuleQuete($_POST['num_quete']);
}else{
	//print_r($_SESSION['QueteEnCours']);
	echo SelectQuete($_SESSION['QueteEnCours']);
}
?>
</div>

<div class="version">
	<?php echo AfficheFooter(true);?>
</div>
</div>
</body>
</html>

<?php

function AnnuleQuete($numQuete){
	CancelQuete($numQuete);
	unset($_POST);
	//redir('./quete.php');
	echo RetourPage($_SESSION['main']['uri']);
}
function SelectQuete(&$QueteEnCours){
	global $nbQueteMax, $objManager;
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	$txt = null;
	$sql = "SELECT * FROM table_quete_lst WHERE niveau<=".$oJoueur->GetNiveau().";";
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	$nbCol=0; $numQueteEnCours=0;
	$txt .= '
	<table class="quetes">';
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		//$sqlBis = "SELECT id_quete_en_cours, quete_reussi FROM table_quetes WHERE (quete_login='".$_SESSION['joueur']->GetLogin()."' AND quete_id=".$row['id_quete'].") ORDER BY id_quete_en_cours DESC;";
		$sqlBis = "SELECT id_quete_en_cours, quete_reussi FROM table_quetes WHERE quete_login='".$_SESSION['joueur']."' AND quete_id=".$row['id_quete']."";
		$requeteBis = mysql_query($sqlBis) or die (mysql_error().'<br />'.$sqlBis);
		if($nbCol==0){$txt .= '
		<tr>';}
		if(mysql_num_rows($requeteBis) == 0){
			$txt .= '
			<td>'
				.AfficheDescriptifQuete($row, $QueteEnCours)
			.'</td>';
			$nbCol++;
			
		}else{
			$rowBis = mysql_fetch_array($requeteBis, MYSQL_ASSOC);
			//si le nombre de quete ne dépasse pas le max
			if(is_null($rowBis['quete_reussi'])){
				if($numQueteEnCours <= ($nbQueteMax - 1)){
					$txt .= '
			<td>'
				.AfficheAvancementQuete($QueteEnCours[$numQueteEnCours])
			.'</td>';
					$numQueteEnCours++;
					$nbCol++;
				}
			}
		}
		if($nbCol==2){$txt .= '
		</tr>'; $nbCol=0;}
	}
	
	if($nbCol<3 and $nbCol > 0){$txt .= '
		</tr>';}
	$txt .= '
	</table>';
	
	$objManager->update($oJoueur);
	unset($oJoueur);
	return $txt;
}
function AfficheDescriptifQuete($quete, &$QueteEnCours){
	global $nbQueteMax, $CodeCouleurQuete;
	
	$nbInfo=0;
	if(!is_null($quete['gain_or'])){$nbInfo++;}
	if(!is_null($quete['gain_experience'])){$nbInfo++;}
	if(!is_null($quete['gain_points'])){$nbInfo++;}
	
	return '
				<table class="fiche_quete">
					<tr style="background:'.$CodeCouleurQuete[$quete['quete_type']].';">
						<th colspan="'.$nbInfo.'">'.$quete['nom'].'</th>
					</tr>
					<tr><td colspan="'.$nbInfo.'">Gains</td></tr>
					<tr>'
						.(!is_null($quete['gain_or'])?'
						<td style="width:'.intval(100 / $nbInfo).'%">
							'.AfficheIcone('or').' : <b>'.$quete['gain_or'].'</b>
						</td>'
						:'')
						.(!is_null($quete['gain_experience'])?'
						<td style="width:'.intval(100 / $nbInfo).'%">
							Exp : <b>'.$quete['gain_experience'].'</b>
						</td>'
						:'')
						.(!is_null($quete['gain_points'])?'
						<td style="width:'.intval(100 / $nbInfo).'%">
							Points : <b>'.$quete['gain_points'].'</b>
						</td>'
						:'')
					.'</tr>'
					.((!is_null($quete['quete_duree']))?
					'<tr>
						<td colspan="'.$nbInfo.'">
							Vous avez : <b>'.AfficheTempPhrase(DecoupeTemp($quete['quete_duree'])).'</b>
						</td>
					</tr>'
					:'')
					.'<tr>
						<td class="description" colspan="'.$nbInfo.'">'
							.$quete['description']
							.(!is_null($quete['id_objet'])?'<p>Si vous remplissez votre quête à temps, vous recevrez ceci :'.AfficheInfoObjet($quete['id_objet']).'</p>':'')
						.'</td>
					</tr>
					<tr>
						<td colspan="'.$nbInfo.'">
							<form method="post" action="quete.php">
								<input type="hidden" name="num_quete" value="'.$quete['id_quete'].'" />
								<input type="submit" name="inscription" value="Accepter" style="width:200px;"'.(count($QueteEnCours)<$nbQueteMax?'':' disabled="disabled"').' />
							</form>
						</td>
					</tr>
				</table>';
}
function InscriptionQuete($numQuete){
	$sql = "SELECT * FROM table_quete_lst WHERE id_quete=$numQuete;";
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	$infoQuete = mysql_fetch_array($requete, MYSQL_ASSOC);
	$sqlAdd = "INSERT INTO table_quetes (
		`id_quete_en_cours`, 
		`quete_login`, 
		`quete_id`, 
		`quete_position`, 
		`quete_vie`, 
		`quete_reussi`, 
		`date_start`, 
		`date_end`)
		VALUE(
		NULL, 
		'".$_SESSION['joueur']."', 
		".$infoQuete['id_quete'].", 
		'".SelectionPositionQuete()."', 
		".(is_null($infoQuete['quete_vie'])?"NULL":$infoQuete['quete_vie']).", 
		NULL, 
		'".date('Y-m-d H:i:s')."', 
		NULL
		);";
	$requete = mysql_query($sqlAdd) or die (mysql_error().'<br />'.$sqlAdd);
	unset($_POST);
	//redir('./quete.php');
	echo RetourPage($_SESSION['main']['uri']);
}
function SelectionPositionQuete(){
	global $objManager;
	
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	$carte = null;
	if($oJoueur->GetNiveau() <= 3){
		if(!is_null($oJoueur->GetMaisonInstalle())){
			$arcarte = $oJoueur->GetMaisonInstalle();
			$carte = $arcarte['0'];
		}else{
			$carte = $oJoueur->GetCarte();
		}
	}
	$free = FreeCaseCarte($carte);
	
	$objManager->update($oJoueur);
	unset($oJoueur);
	return $free[array_rand($free)];
}

?>