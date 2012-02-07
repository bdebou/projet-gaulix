<div class="main">
	<div class="village">
	<h1>Mon Oppidum <dfn>(Village)</dfn></h1>
	<?php
		if(!is_null($lstBatiment)){
			foreach($lstBatiment as $batiment){
				echo $batiment;
			}
		}else{
	?>
	<p>Vous n'avez pas encore construit de batiment pour votre village. Commencez par construire une maison.</p>
	<?php }?>
	</div>
</div>