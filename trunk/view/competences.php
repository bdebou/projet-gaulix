<div class="main">
<h1>Compétences</h1>
<table class="perfectionnement">
	<tr>
		<td style="width:50%;">
			<?php echo AfficheModulePerfectionnement('attaque');?>
		</td>
		<td>
			<?php echo AfficheModulePerfectionnement('defense');?>
		</td>
	</tr>
	<?php
	$temp = AfficheAutreCompetences();
	echo $temp[0];
	?>
	</table>
	<?php echo $temp[1];?>
</div>