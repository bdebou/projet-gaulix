<?php
	function chargerClasse($classname){require './fct/'.$classname.'.class.php';}
	spl_autoload_register('chargerClasse');
	
	session_start();
	
	require_once('./fct/config.php');
	require('./fct/fct_main.php');
	//include('./fctinscription.php');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: 'fr'}</script>
	<title>Gaulix</title>
	<link rel="stylesheet" href="./css/styles.css" type="text/css" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<body>
<div class="container">
<div class="main">
<h2>Inscription</h2>
<?php
$contact = new InscriptionFormulaire();
if(isset($_POST['captchaResult'])){
	$contact->loadForm($_POST);
	if($_POST['captchaResult'] === $_SESSION['captchaResult']){$send = $contact->sendCheck;}
}
if(!$contact->sendCheck){

/* FORMULAIRE DEBUT */ ?>
<form method="post" action="./inscription.php">
<table class="inscription">
	<tr>
		<td width="20%" align="right">&nbsp;&nbsp;</td>
		<td>
			<p>Veuillez remplir ce formulaire :</p>
		</td>
	</tr>
	<tr>
		<td align="right">Login <b>*</b> :</td>
		<td><input type="text" name="login"  size="50" required="required" <?php $contact->inputTrue($contact->login, 3); ?> value="<?php echo $contact->login; ?>" /></td>
	</tr>
	<tr>
		<td align="right">PassWord <b>*</b> :</td>
		<td><input type="password" name="password"  size="50" required="required" <?php $contact->inputTrue($contact->password); ?> value="<?php echo $contact->password; ?>" /></td>
	</tr>
	<tr>
		<td align="right">E-Mail <b>*</b> :</td>
		<td><input type="email" name="mail" size="50" required="required" <?php $contact->inputTrue($contact->mail, 2); ?> value="<?php echo $contact->mail; ?>" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>(<b>*</b>) Champ obligatoire.</td>
	</tr>
	<tr>
		<td  align="right"><label for="captchaResult">Veuillez recopier le code affiché en majuscule: </label><input type="text" name="captchaResult" size="10" <?php $contact->inputTrue($contact->captchaResult, 4); ?> value="<?php echo $contact->captchaResult; ?>" /></td>
		<td><img alt="Captcha" src="./captcha/captcha.php" style="vertical-align:middle;" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="submit" style="width:160px;"  name="envoyer" value="Envoyer" />
			<button type="button" style="width:160px;" onclick="window.location='./'">Retour</button>
		</td>
	</tr>
</table>
</form>
<?php 
}
/* FOMULAIRE FIN*/ ?>
</div>
<div class="version">
	<?php echo AfficheFooter(true);?>
</div>
</div>
</body>
</html>