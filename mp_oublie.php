<?php
function chargerClasse($classname){require './fct/'.$classname.'.class.php';}
spl_autoload_register('chargerClasse');

session_start();

require_once('./fct/config.php');
require('./fct/fct_main.php');
//include('./fct/mail_mp.class.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Gaulix</title>
	<link rel="stylesheet" href="./css/styles.css" type="text/css" />
	<meta name="keywords" content="" />
	<meta name="description" content="" />
</head>
<body>
<div class="container">
<div class="main">
<?php
$contact = new MailMP();
if(isset($_POST['captchaResult'])){
	$contact->loadForm($_POST);
	if($_POST['captchaResult'] === $_SESSION['captchaResult']){$send = $contact->sendCheck;}
}
if(empty($send)){
?>
<p>Vous avez oublié votre mot de passe? Pas de problème, complétez ce formulaire et nous vous l'enverrons par email.</p>
<form method="post" action="./mp_oublie.php">
	<table>
		<tr>
			<td>
				<fieldset>
					<legend>E-Mail : </legend>
					<input type="text" name="mail" size="50" <?php $contact->inputTrue($contact->mail,'2');?> value="<?php echo $contact->mail; ?>" />
				</fieldset>
			</td>
		</tr>
		<tr>
			<td  align="right">
				<label for="captchaResult">Veuillez recopier le code affiché en majuscule: </label>
				<input type="text" name="captchaResult" size="10" <?php $contact->inputTrue($contact->captchaResult,'3'); ?> value="<?php echo $contact->captchaResult; ?>" />
			</td>
			<td>
				<img alt="Captcha" src="./captcha/captcha.php" style="vertical-align:middle;" />
			</td>
		</tr>
		<tr>
			<td style="text-align:center;">
				<input type="submit" name="submit_mp" value="Envoyer" style="width: 160px" />
				<button type="button" onclick="window.location='./'" style="width: 160px">Retour</button>
			</td>
		</tr>
	</table>
</form>
<?php
}
?>
</div>
<div class="version">
	<?php echo AfficheFooter(true);?>
</div>
</div>
</body>
</html>
