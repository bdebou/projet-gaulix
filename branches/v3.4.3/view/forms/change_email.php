<td>
	<fieldset style="border:3px double; margin:3px;">
		<legend style="font-weight:bold;text-decoration:underline;padding-left:5px;padding-right:5px;">Changer E-Mail :</legend>		
		<form method="post">
			<table class="optionmail">
				<tr><td>E-Mail actuel :</td></tr>
				<tr><td style="text-align:right;"><?php echo recup_email();?></td></tr>
				<tr><td>Nouvel E-Mail :</td></tr>
				<tr><td style="text-align:right;"><input size="35" <?php echo $chgemail->inputTrue($chgemail->email);?> type="text" name="email" value="<?php echo $chgemail->email;?>" /></td></tr>
				<tr>
					<td colspan="2" style="text-align:center;"><input type="submit" name="chg_email" value="Envoie" /></td>
				</tr>
			</table>
		</form>
	</fieldset>
</td>