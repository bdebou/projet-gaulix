<div class="main">
	<div class="village">
	<h1>Mon Oppidum <dfn>(Village)</dfn></h1>
	<?php
		if(!is_null($lstBatiment)){
			?>
	<table class="village">
	<?php 
			foreach($lstBatiment as $batiment){
				echo $batiment;
				echo '<tr style="background:lightgrey;">
						<td colspan="5" style="text-align:right;"><a href="#TopPage" alt="TopPage">Top</a>
						</td>
					</tr>';
			}
			?>
	</table>
	<?php 
		}else{
	?>
	<p>Vous n'avez pas encore construit de batiment pour votre village. Commencez par construire une maison.</p>
	<?php }?>
	</div>
</div>