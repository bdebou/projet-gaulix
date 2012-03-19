<div class="main">
	<h1>Envoie Password</h1>
	<p>Vous avez oublié votre mot de passe? Pas de problème, complétez ce formulaire et nous vous l'enverrons par email.</p>
	<form method="post" action="./index.php?page=mp_oublie">
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
</div>