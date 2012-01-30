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
	include('./fct/fct_alliance.php');
	
	global $objManager;
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	if(isset($_POST['alliance'])){
		
		switch($_POST['alliance']){
			case 'inscriptionclan':			ActionInscriptionClan($oJoueur, $_POST['nomclan'], $_POST['chefclan']);				break;
			case 'desinscriptionclan':		ActionDesinscriptionClan($oJoueur, $_POST['nomclan']);								break;
			case 'addclan': 				ActionAjoutClan($oJoueur, $_POST['addclan']);										break;
			case 'desinscrireclan': 		ActionDesinscrireMembreClan($objManager, $_POST['nomclan'], $_POST['membre']);		break;
			case 'supprimerclan': 			ActionSupprimerClan($objManager, $oJoueur, $_POST['nomclan']);						break;
			case 'accepterinscription': 	ActionAccpeterInscriptionClan($objManager, $_POST['nomclan'], $_POST['membre']);	break;
		}
	}
	
	if(isset($_POST['chat'])){
		
		switch($_POST['chat']){
			case 'addreccord':	ActionAddReccordChat(array('clan'=>$_POST['clan'], 'membre'=>$_POST['member'], 'text'=>$_POST['text']));	break;
			case 'remove':		ActionRemoveReccordChat($_POST['id']);																		break;
		}
	}
	
	$objManager->update($oJoueur);
	unset($_POST);
	unset($oJoueur);
	RetourPage($_SESSION['main']['uri']);
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<?php echo AfficheHead();?>
	<title>Gaulix - Alliances</title>
</head>
<body>
<div class="container">
<div id="curseur" class="infobulle"></div>
<div class="loginstatus"><?php RefreshData(9); echo affiche_LoginStatus(); $_SESSION['QueteEnCours'] = FoundQueteEnCours();?></div>
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
<h1>Les Alliances</h1>
	<p>Les alliances vous permettront de vous allier avec un autre joueur et donc de ne pas vous faire attaquer par ses Tours. de passer à travers ses Plissades, ...</p>
<h2>Les Clans</h2>
<?php echo AfficheListeDesClans();?>
<h2>Débats du clan</h2>
<?php echo AfficheLesDebatsDuClan();?>
</div>

<div class="version">
	<?php echo AfficheFooter(true);?>
</div>
</div>
</body>
</html>
<?php
function AfficheLesDebatsDuClan(){
	global $objManager;
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	If(is_null($oJoueur->GetClan())){
		return 'Créez ou inscrivez-vous dans une alliance pour pouvoir chatter avec les membres de votre alliance.';
	}elseif($oJoueur->GetClan() == '1'){
		return 'Pas encore accepté dans l\'alliance.';
	}else{
		$debat = new DebatDeClan($oJoueur->GetClan());
		return $debat->AfficheDebat($oJoueur);
	}
	
	$objManager->update($oJoueur);
	unset($oJoueur);
}
function AfficheListeDesClans(){
	global $objManager;
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	$chkMembre = false;
	$txt = '
	<table class="clans">
		<tr>
			<th>Nom et Chef de clan</th>
			<th>Membres</th>
			<th>Actions</th>
		</tr>';
	
	$sql = "SELECT * FROM table_alliance WHERE membre_actif IS NULL ORDER BY nom_clan ASC;";
	$requete = mysql_query($sql) or die (mysql_error());
	
	if(mysql_num_rows($requete)>0){
		$precedentNom = null;
		$nbMembres = 0;
		$txtM = null;
		$chkA = false;
		
		$nbLigne = 0;
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if($row['membre_clan'] == $oJoueur->GetLogin()){$chkMembre=true;}
			if($precedentNom != $row['nom_clan'] AND !is_null($precedentNom)){
				$txt .= '
		<tr'.AfficheLigneCouleur('#C0C0C0', $nbLigne).'>
			<td rowspan="'.$nbMembres.'">
				Clan : "<b>'.$precedentNom.'</b>"'.AfficheRecompenses(NULL, $precedentNom).'<br />Chef : '.$precedentChef.'<br />'
				.(($row['membre_clan'] != $oJoueur->GetLogin() AND is_null($oJoueur->GetClan()))?FormInscriptionClan($precedentNom, $precedentChef):'')
			.'</td>'.$txtM;
				
				$nbLigne++;
				$nbMembres = 1;
				$txtM = null;
				$chkA = false;
			}else{$nbMembres++;}
			
			if($chkA){
				$txtM .= '
		<tr'.AfficheLigneCouleur('#C0C0C0', $nbLigne).'>';
			}
			$chkA = true;
			$NomClan = $row['nom_clan'];
			$ChefClan = $row['chef_clan'];
			$txtM .= '
			<td>'.$row['membre_clan'].''.AfficheRecompenses($row['membre_clan']).'</td>';
			if($row['membre_clan'] == $row['chef_clan'] AND $row['chef_clan'] == $oJoueur->GetLogin()){
				$txtM .= '<td>'.FormSupprimerClan($row['nom_clan']).'</td>';
			}elseif($row['membre_clan'] == $oJoueur->GetLogin() AND $row['chef_clan']!=$oJoueur->GetLogin()){
				if(!is_null($oJoueur->GetClan()) AND $oJoueur->GetClan() != '1'){
					$txtM .= '<td>'.FormDesinscriptionClan($row['nom_clan']).'</td>';
				}else{
					$txtM .= '<td>En Cours d\'acceptation</td>';
				}
			}elseif($row['chef_clan']==$oJoueur->GetLogin() AND $row['membre_clan'] != $oJoueur->GetLogin()){
				if(is_null($row['date_inscription'])){
					$txtM .= '<td>'.FormRemoveMembre($row['nom_clan'], $row['membre_clan']).'</td>';
				}else{
					$txtM .= '<td>'.FormAccepteInscription($row['nom_clan'], $row['membre_clan']).'</td>';
				}
			}else{$txtM .= '<td>&nbsp;</td>';}
			$txtM .= '</tr>';
		
			$precedentNom = $row['nom_clan'];
			$precedentChef = $row['chef_clan'];
		}
		$txt .= '
		<tr'.AfficheLigneCouleur('#C0C0C0', $nbLigne).'>
			<td rowspan="'.$nbMembres.'">
				Clan : "<b>'.htmlspecialchars_decode($precedentNom, ENT_QUOTES).'</b>"'.AfficheRecompenses(NULL, $precedentNom).'<br />Chef : '.$precedentChef.'<br />'
				.(is_null($oJoueur->GetClan())?FormInscriptionClan($precedentNom, $precedentChef):'')
			.'</td>'.$txtM;
	}else{$txt .= '
		<tr><td colspan="3" style="text-align:center;">Il y a encore aucun clan.</td></tr>';
	}
	if(!$chkMembre AND is_null($oJoueur->GetClan())){
		$txt .= '
		<tr>
			<td colspan="3">
				<form method="post" action="./alliance.php">
					<input type="hidden" name="alliance" value="addclan" />
					<input type="text" name="addclan" size="20" />
					<input type="submit" value="Créer un nouveau clan" />
				</form>
			</td>
		</tr>';
	}
	$txt .= '
	</table>';
	
	$objManager->update($oJoueur);
	unset($oJoueur);
	
	return $txt;
}
function FormAccepteInscription($clan, $membre){
	return '<form method="post" action="./alliance.php">
				<input type="hidden" name="alliance" value="accepterinscription" />
				<input type="hidden" name="nomclan" value="'.$clan.'" />
				<input type="hidden" name="membre" value="'.$membre.'" />
				<input type="submit" value="Accepter" />
			</form>';
}
function FormSupprimerClan($nom){
	return '<form method="post" action="./alliance.php">
				<input type="hidden" name="alliance" value="supprimerclan" />
				<input type="hidden" name="nomclan" value="'.$nom.'" />
				<input type="submit" value="Supprimer clan" />
			</form>';
}
function FormInscriptionClan($nom, $chef){
	return '<form method="post" action="./alliance.php">
				<input type="hidden" name="alliance" value="inscriptionclan" />
				<input type="hidden" name="nomclan" value="'.$nom.'" />
				<input type="hidden" name="chefclan" value="'.$chef.'" />
				<input type="submit" value="S\'inscrire" />
			</form>';
}
function FormDesinscriptionClan($nom){
	return '<form method="post" action="./alliance.php">
				<input type="hidden" name="alliance" value="desinscriptionclan" />
				<input type="hidden" name="nomclan" value="'.$nom.'" />
				<input type="submit" value="Se désinscrire" />
			</form>';
}
function FormRemoveMembre($clan, $membre){
	return '<form method="post" action="./alliance.php">
				<input type="hidden" name="alliance" value="desinscrireclan" />
				<input type="hidden" name="nomclan" value="'.$clan.'" />
				<input type="hidden" name="membre" value="'.$membre.'" />
				<input type="submit" value="Désinscrire" />
			</form>';
}

?>