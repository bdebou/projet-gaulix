<div class="main">
	<h1>Inscription</h1>
	<form method="post" action="index.php?page=inscription">
	<table class="inscription">
		<tr>
			<td width="20%" align="right">&nbsp;</td>
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
</div>