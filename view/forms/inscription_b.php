<div class="main">
	<h1>Inscription (étape 2/2)</h1>
	<?php
	if(!is_null($objStepTwo->GetMessage())){
		echo $objStepTwo->GetMessage();
	}
	?>
	<form method="post" action="index.php?page=inscription_b">
		<table class="inscription">
			<tr>
				<td width="50%">Choisissez votre village :</td>
				<td width="50%">Choisissez votre carrière :</td>
			</tr>
			<tr>
				<td>
					<table class="lst_village">
					<?php
					if(is_array($objStepTwo->GetListeVillages())){
						foreach($objStepTwo->GetListeVillages() as $village){
							echo $village;
						}
					}else{
						echo $objStepTwo->GetListeVillages();
					}?>
						<tr>
							<td>
								<input required="required" type="radio" name="village" value="VillageNew" />
							</td>
							<td>
								<h2>Nouveau village</h2>
								<input type="text" name="VillageNew" style="width:150px;" />
							</td>
					</table>
				</td>
				<td>
					<table class="lst_carriere">
					<?php
					if(is_array($objStepTwo->GetListeCarrieres())){
						foreach($objStepTwo->GetListeCarrieres() as $carriere){
							echo $carriere;
						} 
					}else{
						echo $objStepTwo->GetListeCarrieres();
					}
					?>
					</table>
				</td>
			</tr>
			<tr>
				<th colspan="2">
					<button type="button" style="width:160px;" onclick="window.location='./'">Annuler</button>
					<input type="submit" style="width:160px;"  name="envoyer" value="Envoyer" />
				</th>
			</tr>
		</table>
	</form>
</div>