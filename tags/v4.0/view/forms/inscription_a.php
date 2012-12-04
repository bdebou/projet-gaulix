<div class="main">
	<script type="text/javascript">
		//<!--
			var OldName = 'Login';
			function change_info(NewName){
				document.getElementById('Contenu_Info_'+OldName).style.display = 'none';
				document.getElementById('Contenu_Info_'+NewName).style.display = 'block';
				
				OldName = NewName;
			}
			//change_info(OldName);
		//-->
	</script>
	<h1>Inscription (étape 1/2)</h1>
	<?php
	if(!is_null($contact->GetMessage())){
		echo $contact->GetMessage();
		
	}
	?>
	<form method="post" action="index.php?page=inscription_a">
	<table class="inscription">
		<tr>
			<td width="150px" align="right">&nbsp;</td>
			<td width="330px">
				<p>Veuillez remplir ce formulaire :</p>
			</td>
			<td rowspan="10" class="info">
				<?php include('view/forms/inscription_info.php');?>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>(<b>*</b>) Champ obligatoire.</td>
		</tr>
		<tr>
			<td align="right"><strong>*</strong> Login :</td>
			<td>
				<input style="width:150px;" onfocus="change_info('Login');" type="text" name="Login" required="required" <?php echo $contact->GetStyleCheckLogin(); ?> placeholder="Login" />
			</td>
		</tr>
		<tr>
			<td align="right"><strong>*</strong> PassWord 1 :</td>
			<td>
				<input style="width:150px;" onfocus="change_info('PasswordA');" type="password" name="PasswordA" required="required" <?php echo $contact->GetStyleCheckPassword(); ?> placeholder="Mot de passe" />
			</td>
		</tr>
		<tr>
			<td align="right"><strong>*</strong> PassWord 2 :</td>
			<td>
				<input style="width:150px;" onfocus="change_info('PasswordB');" type="password" name="PasswordB" required="required" <?php echo $contact->GetStyleCheckPassword(); ?> placeholder="Encore votre mot de passe" />
			</td>
		</tr>
		<tr>
			<td align="right"><strong>*</strong> E-Mail :</td>
			<td>
				<input style="width:300px;" onfocus="change_info('Email');" type="email" name="mail" required="required" <?php echo $contact->GetStyleCheckEmail(); ?> placeholder="E-Mail" />
			</td>
		</tr>
		
		<tr>
			<td  align="right">
				<label for="captchaResult">Veuillez recopier le code affiché en majuscule: </label>
				<input style="width:100px;" onfocus="change_info('Captcha');" type="text" name="captchaResult" <?php echo $contact->GetStyleCheckCaptcha(); ?> />
			</td>
			<td>
				<img id="img_captcha" alt="Captcha" src="captcha/captcha.php" style="vertical-align:middle;" height="71px" width="201px" border="1px" />
				<a href="#" onclick="document.getElementById('img_captcha').src = 'captcha/captcha.php';">
					<img title="Refresh Captcha" alt="Refresh Captcha" src="img/icones/ic_Refresh.png" />
				</a>
			</td>
		</tr>
		<tr>
			<td colspan="2"><hr /></td>
		</tr>
		<tr>
			<td align="right"><strong>*</strong> Civilisation :</td>
			<td>
				<select style="width:150px;" name="Civilisation" onselect="CacherAutreVillage(this);">
					<option value="<?php echo InscriptionStepA::CIVI_GAULOIS;?>"<?php echo $contact->GetSelectCivilisation(InscriptionStepA::CIVI_GAULOIS);?>>Gaulois</option>
					<option value="<?php echo InscriptionStepA::CIVI_ROMAINS;?>"<?php echo $contact->GetSelectCivilisation(InscriptionStepA::CIVI_ROMAINS);?>>Romains</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<button type="button" style="width:160px;" onclick="window.location='./'">Annuler</button>
				<input type="submit" style="width:160px;"  name="next" value="Suivant" />
			</td>
		</tr>
	</table>
	</form>
</div>