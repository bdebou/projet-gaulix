<td>
	<fieldset style="border:3px double; margin:3px;">
		<legend style="font-weight:bold;text-decoration:underline;padding-left:5px;padding-right:5px;">Notifications :</legend>
		<form method="post">
			<table class="notification">
				<tr><th style="text-align:left;">En cas de </th><th>Oui</th><th>Non</th></tr>
				<tr>
					<td style="text-align:left;">Attaque de bâtiment</td>
					<td><input type="radio" name="NotAttaque" value="yes"<?php echo ($oJoueur->GetNotifAttaque()?' checked="checked"':'');?> /></td>
					<td><input type="radio" name="NotAttaque" value="no"<?php echo (!$oJoueur->GetNotifAttaque()?' checked="checked"':'');?> /></td>
				</tr>
				<tr>
					<td style="text-align:left;">Combat</td>
					<td><input type="radio" name="NotCombat" value="yes"<?php echo ($oJoueur->GetNotifCombat()?' checked="checked"':'');?> /></td>
					<td><input type="radio" name="NotCombat" value="no"<?php echo (!$oJoueur->GetNotifCombat()?' checked="checked"':'');?> /></td>
				</tr>
				<tr>
					<td colspan="3" style="text-align:center;">
						<input type="submit" name="ChgNot" />
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
</td>