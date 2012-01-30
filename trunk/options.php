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
	<title>Gaulix - Options</title>
</head>
<body>
<div class="container">
<div id="curseur" class="infobulle"></div>
<div class="loginstatus"><?php RefreshData(12); echo affiche_LoginStatus(); $_SESSION['QueteEnCours'] = FoundQueteEnCours();?></div>
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
<h2>Options</h2>
<table class="options">
	<tr>
		<td style="width:50%;">
			<fieldset style="border:3px double; margin:3px;">
				<legend style="font-weight:bold;text-decoration:underline;padding-left:5px;padding-right:5px;">Changer de password :</legend>
				<?php echo change_password();?>
			</fieldset>
		</td>
		<td>
			<fieldset style="border:3px double; margin:3px;">
				<legend style="font-weight:bold;text-decoration:underline;padding-left:5px;padding-right:5px;">Notifications :</legend>
				<?php echo ChangeNotification();?>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td>
			<fieldset style="border:3px double; margin:3px;">
				<legend style="font-weight:bold;text-decoration:underline;padding-left:5px;padding-right:5px;">Changer E-Mail :</legend>
				<?php echo change_email();?>
			</fieldset>
		</td>
		<td>
			<fieldset style="border:3px double; margin:3px;">
				<legend style="font-weight:bold;text-decoration:underline;padding-left:5px;padding-right:5px;">Supprimer compte</legend>
				<?php echo Supprimer_Compte();?>
			</fieldset>
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
function Supprimer_Compte(){
	global $objManager;
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	if(isset($_POST['SupprimerCpt']) and $_POST['SupprimerCpt'] == 'Supprimer'){
		
		//on supprimer les batiments du joueur
		$sql = "SELECT id_case_carte FROM  table_carte WHERE login='" . $oJoueur->GetLogin() . "' AND id_type_batiment NOT IN (7, 8, 10, 11, 12, 13, 14, 15, 16, 17);";
		$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
		while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
			$sqlRemove = "DELETE FROM table_carte WHERE id_case_carte=".intval($row['id_case_carte']).";";
			mysql_query($sqlRemove) or die(mysql_error() . '<br />' . $sqlRemove);
			//echo $sqlRemove;
		}
		
		//on libère les ressources qui sont peut-etre encore en cours
		if(isset($_SESSION['main']['ressource'])){
			if($_SESSION['main']['ressource']->GetCollecteur() == $oJoueur->GetLogin()){
				$_SESSION['main']['ressource']->FreeRessource($oJoueur);
			}
			$objManager->UpdateRessource($_SESSION['main']['ressource']);
			unset($_SESSION['main']['ressource']);
		}
		
		//on reset la liste des quetes
		ResetListeQuetes($oJoueur->GetLogin());
		
		//on reset la liste des compétences
		$sql = "SELECT cmp_id FROM table_competence WHERE cmp_login='" . $oJoueur->GetLogin() . "';";
		$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
		while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
			$sqlRemove = "DELETE FROM table_competence WHERE cmp_id=" . intval($row['cmp_id']) . ";";
			mysql_query($sqlRemove) or die(mysql_error() . '<br />' . $sqlRemove);
			//echo $sqlRemove;
		}
		
		//On reset toutes les transactions au marcher
		$sql = "SELECT ID_troc FROM table_marcher WHERE vendeur='" . $oJoueur->GetLogin() . "';";
		$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
		while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
			$sqlRemove = "DELETE FROM table_marcher WHERE ID_troc=" . intval($row['ID_troc']) . ";";
			mysql_query($sqlRemove) or die(mysql_error() . '<br />' . $sqlRemove);
			//echo $sqlRemove;
		}
		
		//on reset les history du joueur
		$sql = "SELECT history_id FROM table_history WHERE history_login='" . $oJoueur->GetLogin() . "';";
		$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
		while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
			$sqlRemove = "DELETE FROM table_history WHERE history_id=" . intval($row['history_id']) . ";";
			mysql_query($sqlRemove) or die(mysql_error() . '<br />' . $sqlRemove);
			//echo $sqlRemove;
		}
		
		//on supprime le clan
		include('./fct/fct_alliance.php');
		$sql = "SELECT nom_clan FROM table_alliance WHERE chef_clan='".$oJoueur->GetLogin()."';";
		$requete = mysql_query($sql) or die (mysql_error());
		if(mysql_num_rows($requete)>0){
			while ($row = mysql_fetch_array($requete, MYSQL_ASSOC)) {
				ActionSupprimerClan($objManager, $oJoueur, $row['nom_clan']);
			}
		}else{
			ActionDesinscrireMembreClan($objManager, $oJoueur->GetClan(), $oJoueur->GetLogin());
		}
		
		//on supprime le joueur
		$sqlRemove = "DELETE FROM table_joueurs WHERE login='".$oJoueur->GetLogin()."';";
		$requete = mysql_query($sqlRemove) or die (mysql_error() . '<br />' . $sqlRemove);
		//echo $sqlRemove;
		$objManager->update($oJoueur);
		unset($oJoueur);
		unset($_POST['SupprimerCpt']);
		return RetourPage(13);
	}else{
		return '
			<form method="post">
				<table class="suppresion">
					<tr><th>Suppression du compte</th></tr>
					<tr>
						<td>
							<p>La suppression du compte est directement définitive. Vos batiments seront détruits, les ressources libérées, alliances dissoutes, ...</p>
							<p>Donc, etes-vous vraiment sur de vouloir supprimer votre compte?</p>
						</td>
					</tr>
					<tr>
						<td style="text-align:center;"><input type="submit" name="SupprimerCpt" value="Supprimer" /></td>
					</tr>
				</table>
			</form>';
	}
}
function ChangeNotification(){
	global $objManager;
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	if(isset($_POST['ChgNot'])){
		if(isset($_POST['NotAttaque'])){
			switch($_POST['NotAttaque']){
				case 'yes' :	$oJoueur->SetNotification('attaque', true);	break;
				case 'no' :		$oJoueur->SetNotification('attaque', false);	break;
			}
		}
		if(isset($_POST['NotCombat'])){
			switch($_POST['NotCombat']){
				case 'yes' :	$oJoueur->SetNotification('combat', true);	break;
				case 'no' :		$oJoueur->SetNotification('combat', false);	break;
			}
		}
		$objManager->update($oJoueur);
		unset($oJoueur);
		unset($_POST['ChgNot'], $_POST['NotAttaque'], $_POST['NotCombat']);
		return RetourPage($_SESSION['main']['uri']);
	}else{
		$txt = '
			<form method="post">
				<table class="notification">
					<tr><th style="text-align:left;">En cas de </th><th>Oui</th><th>Non</th></tr>
					<tr>
						<td style="text-align:left;">Attaque de bâtiment</td>
						<td><input type="radio" name="NotAttaque" value="yes"'.($oJoueur->GetNotifAttaque()?' checked="checked"':'').' /></td>
						<td><input type="radio" name="NotAttaque" value="no"'.(!$oJoueur->GetNotifAttaque()?' checked="checked"':'').' /></td>
					</tr>
					<tr>
						<td style="text-align:left;">Combat</td>
						<td><input type="radio" name="NotCombat" value="yes"'.($oJoueur->GetNotifCombat()?' checked="checked"':'').' /></td>
						<td><input type="radio" name="NotCombat" value="no"'.(!$oJoueur->GetNotifCombat()?' checked="checked"':'').' /></td>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="text-align:center;">
							<input type="submit" name="ChgNot" />
						</td>
					</tr>
				</table>
			</form>';
		$objManager->update($oJoueur);
		unset($oJoueur);
		unset($_POST['ChgNot'], $_POST['NotAttaque'], $_POST['NotCombat']);
		return $txt;
	}
}
function change_password(){
	$chgpassword = new ChangePassword();
	if(isset($_POST['chg_pass'])){
		$chgpassword->loadForm($_POST);
		$changeit = $chgpassword->ChangeCheck;
		unset($_POST['chg_pass']);
	}
	if(empty($changeit)){
		return '
				<form method="post">
					<table>
						<tr>
							<td>Ancien password :</td><td><input'.$chgpassword->inputTrue($chgpassword->old_password,'1').' type="password" name="old_password" value="'.$chgpassword->old_password.'" /></td>
						</tr>
						<tr>
							<td>Nouveau password :</td><td><input'.$chgpassword->inputTrue($chgpassword->password_1,'1').' type="password" name="password_1" value="'.$chgpassword->password_1.'" /></td>
						</tr>
						<tr>
							<td>Nouveau password :</td><td><input'.$chgpassword->inputTrue($chgpassword->password_2,'1').' type="password" name="password_2" value="'.$chgpassword->password_2.'" /></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align:center;"><input type="submit" name="chg_pass" value="Envoie" /></td>
						</tr>
					</table>
				</form>';
	}
}
function change_email(){
	$chgemail = new ChangeEmail();
	if(isset($_POST['chg_email'])){
		$chgemail->loadForm($_POST);
		$changeit = $chgemail->ChangeCheck;
		unset($_POST['chg_email']);
	}
	if(empty($changeit)){
		return '
				<form method="post">
					<table class="optionmail">
						<tr><td>E-Mail actuel :</td></tr>
						<tr><td style="text-align:right;">'.recup_email().'</td></tr>
						<tr><td>Nouvel E-Mail :</td></tr>
						<tr><td style="text-align:right;"><input size="35"'.$chgemail->inputTrue($chgemail->email).' type="text" name="email" value="'.$chgemail->email.'" /></td></tr>
						<tr>
							<td colspan="2" style="text-align:center;"><input type="submit" name="chg_email" value="Envoie" /></td>
						</tr>
					</table>
				</form>';
	}
}
function recup_email(){
		$sql = "SELECT mail FROM table_joueurs WHERE login='".$_SESSION['joueur']."'";
		$requete = mysql_query($sql) or die ( mysql_error() );
		$result = mysql_fetch_array($requete, MYSQL_ASSOC);
		return $result['mail'];
}




?>