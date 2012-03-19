<div class="login">
	<form method="post" action="./index.php?page=main">
		<table>
			<tr>
				<td>
					<fieldset><legend>Login : </legend><input type="text" name="login" size="17" /></fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset><legend>Mot de passe : </legend><input type="password" name="motdepasse" size="17" /></fieldset>
				</td>
			</tr>
			<tr>
				<td style="text-align:center;">
					<input type="submit" name="submit" value="Se connecter" style="width: 160px" />
					<input type="button" name="inscription" value="S'inscrire" style="width: 160px;" onclick="window.location='./index.php?page=inscription';" />
					<a href="./index.php?page=mp_oublie" style="font-size:10px;">Mot de passe oublié</a>
				</td>
			</tr>
		</table>
	</form>
</div>