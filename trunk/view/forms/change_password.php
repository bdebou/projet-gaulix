<td style="width:50%;">
	<fieldset style="border:3px double; margin:3px;">
		<legend style="font-weight:bold;text-decoration:underline;padding-left:5px;padding-right:5px;">Changer de password :</legend>
		<form method="post">
			<table>
				<tr>
					<td>Ancien password :</td><td><input <?php echo $chgpassword->inputTrue($chgpassword->old_password,'1');?> type="password" name="old_password" value="<?php echo $chgpassword->old_password;?>" /></td>
				</tr>
				<tr>
					<td>Nouveau password :</td><td><input <?php echo $chgpassword->inputTrue($chgpassword->password_1,'1');?> type="password" name="password_1" value="<?php echo $chgpassword->password_1;?>" /></td>
				</tr>
				<tr>
					<td>Nouveau password :</td><td><input <?php echo $chgpassword->inputTrue($chgpassword->password_2,'1');?> type="password" name="password_2" value="<?php echo $chgpassword->password_2;?>" /></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center;"><input type="submit" name="chg_pass" value="Envoie" /></td>
				</tr>
			</table>
		</form>
	</fieldset>
</td>