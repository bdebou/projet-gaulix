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
	<title>Gaulix - Cartes</title>
</head>
<body>
<div class="container">
<div id="curseur" class="infobulle"></div>
<div class="loginstatus"><?php RefreshData(10); echo affiche_LoginStatus(); $_SESSION['QueteEnCours'] = FoundQueteEnCours();?></div>
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
<h1>La Carte</h1>
<?php echo AfficheToutesLesCartes();?>
</div>

<div class="version">
	<?php echo AfficheFooter(true);?>
</div>
</div>
</body>
</html>
<?php
function AfficheToutesLesCartes(){
	$txt = null;
	$arCarteNum = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y');
	$numCol = 0;
	$txt .= '
<table style="border:none;">';
	for($i=0;$i<=24;$i++){
		if($numCol == 0){$txt .= '
	<tr>';}
		$txt .= '
		<td>
			'.AfficheCarte($arCarteNum[$i]).'
		</td>';
		$numCol++;
		if($numCol == 5){
			$txt .= '
	</tr>';
			$numCol = 0;
		}
	}
	$txt .= '
</table>';
	return $txt;
}
function AfficheCarte($numCarte){
	global $nbLigneCarte, $nbColonneCarte, $objManager, $VieMaximum;
	
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	$txt = null;
	
	$sql="SELECT vie, position, login FROM table_joueurs;";
	$requete = mysql_query($sql) or die (mysql_error());
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		$position = explode(',', $row['position']);
		if($numCarte == $position[0]){
			if(empty($grille[intval($position[1])][intval($position[2])])){
				$grille[intval($position[1])][intval($position[2])]['login'] = $row['login'];
				$grille[intval($position[1])][intval($position[2])]['vie'] = $row['vie'];
			}elseif($row['login'] == $_SESSION['joueur']){
				$grille[intval($position[1])][intval($position[2])]['login'] = $row['login'];
				$grille[intval($position[1])][intval($position[2])]['vie'] = $row['vie'];
			}
		}
	}
	//On ajoute les quetes sur la carte.
	if(isset($_SESSION['QueteEnCours'])){
		foreach($_SESSION['QueteEnCours'] as $Quete){
			if($numCarte == $Quete->GetCarte() AND in_array($Quete->GetTypeQuete(), array('romains'))){
				$arPosition = $Quete->GetPosition();
				$InfoBulle = '<b>'.$Quete->GetNom().'</b>';
				$grille[intval($arPosition[0])][intval($arPosition[1])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" onmouseout="cache();" style="background: url(\'./img/mini/'.$Quete->GetTypeQuete().'.png\') no-repeat center;"';
			}
		}
	}
	//on cache les quetes par les batiments
	//on ajoute les infos des batiments
	$sql="SELECT etat_batiment, coordonnee, login, id_type_batiment, niveau_batiment, contenu_batiment FROM table_carte WHERE detruit IS NULL;";
	$requete = mysql_query($sql) or die (mysql_error().$sql);
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		$position = explode(',', $row['coordonnee']);
		if($numCarte == $position['0']){
			$sqlb = "SELECT batiment_type, batiment_nom, batiment_description, batiment_vie FROM table_batiment WHERE id_batiment=".$row['id_type_batiment'].";";
			$requeteb = mysql_query($sqlb) or die (mysql_error().$sqlb);
			$batiment = mysql_fetch_array($requeteb, MYSQL_ASSOC);
		
			if($batiment['batiment_type'] != 'ressource' AND $batiment['batiment_type'] != 'carte'){
				$InfoBulle = 
					'<b>'.$batiment['batiment_nom'].' de '.$row['login'].'</b>'
					.'<br />'
					.'<img alt="'.$batiment['batiment_nom'].'" src="./fct/fct_image.php?type=etatcarte&amp;value='.$row['etat_batiment'].'&amp;max='.($batiment['batiment_vie'] + (50 * $row['niveau_batiment'])).'" />'
					.(($row['login'] == 'romain' AND in_array($row['id_type_batiment'], array(4, 5)))?
						'<br />Contenu : '
							.($row['id_type_batiment'] == 5?
								$row['contenu_batiment'].'x '.AfficheIcone('or')
								:'<b>Plusieurs objets</b>')
						:'');
				$grille[intval($position[1])][intval($position[2])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" onmouseout="cache();" style="background: url(\'./img/mini/'.$batiment['batiment_type'].'-';
				if($row['login']==$_SESSION['joueur']){
					$grille[intval($position[1])][intval($position[2])]['batiment'] .= 'a';
				}else{
					$grille[intval($position[1])][intval($position[2])]['batiment'] .= 'b';
				}
			}elseif($batiment['batiment_type'] == 'ressource'){
				$grille[intval($position[1])][intval($position[2])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($batiment['batiment_description']).'\');" onmouseout="cache();" style="background: url(\'./img/mini/'.$batiment['batiment_nom'].'';
			}
			if($batiment['batiment_type'] != 'carte'){$grille[intval($position[1])][intval($position[2])]['batiment'] .= '.png\') no-repeat center;"';}
		}
	}
	
	$txt .= '
			<table class="allcarte" onmouseover="montre(\''.CorrectDataInfoBulle('<b>Carte '.strtoupper($numCarte).'</b>').'\');" onmouseout="cache();">';
	for($i=0;$i<=$nbLigneCarte;$i++){
		$txt .= '
				<tr>
					';
		for($j=0;$j<=$nbColonneCarte;$j++){
			$txt .= '<td'.(isset($grille[$i][$j]['batiment'])?$grille[$i][$j]['batiment']:'').'>';
			if(isset($grille[$i][$j]['login'])){
				$txt .= '<img alt="Perso '.$grille[$i][$j]['login'].'" src="./img/homme-'.($_SESSION['joueur']==$grille[$i][$j]['login']?'green':'grey').'.png" height="8" width="8" onmouseover="montre(\''.CorrectDataInfoBulle('<b>'.$grille[$i][$j]['login'].'</b><br /><img alt="Barre de Vie" src="./fct/fct_image.php?type=VieCarte&amp;value='.$grille[$i][$j]['vie'].'&amp;max='.$VieMaximum.'" />').'\');" onmouseout="cache();" />';
			}
			$txt .= '</td>';
		}
		$txt .= '
				</tr>';
	}
	$txt .= '
			</table>';
	
	$objManager->update($oJoueur);
	unset($oJoueur);
	return $txt;
}
?>