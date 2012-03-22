<div class="main">
	<h1>Scores</h1>
	<h2>Le Classement</h2>
	<table class="classement">
		<tr style="background:grey;">
			<th style="width:60px;" rowspan="2">Points</th>
			<th style="width:160px;" rowspan="2">Nom (Niveau)</th>
			<th style="width:150px;" rowspan="2">Expérience</th>
			<th style="width:150px;" rowspan="2"><?php echo AfficheIcone('vie');?></th>
			<th rowspan="2"><?php echo AfficheIcone('attaque');?></th>
			<th rowspan="2"><?php echo AfficheIcone('defense');?></th>
			<th colspan="3">Combats</th>
		</tr>
		<tr style="background:grey;">
			<th>Gagnés</th>
			<th>Perdus</th>
			<th>Mort</th>
		</tr>
<?php
foreach($lignes as $ligne){
	echo '
		<tr>'
			.'<td>'.$ligne['nb_points'].'</td>'
			.'<td style="background:#c8c8c8; text-align:left;">'.$ligne['login'].' ('.$ligne['niveau'].')'.AfficheRecompenses($ligne['login'], $ligne['clan']).'</td>'
			.'<td><img alt="Barre d\'expérience" src="./fct/fct_image.php?type=experience&amp;value='.$ligne['experience'].'&amp;max='.(($ligne['niveau'] + 1) * 100).'" /></td>'
			.'<td><img alt="Barre de Vie" src="./fct/fct_image.php?type=vie&amp;value='.$ligne['vie'].'&amp;max='.personnage::VIE_MAX.'" /></td>'
			.'<td>'.$ligne['val_attaque'].'</td>'
			.'<td>'.$ligne['val_defense'].'</td>'
			.'<td>'.$ligne['nb_victoire'].'</td>'
			.'<td>'.$ligne['nb_vaincu'].'</td>'
			.'<td>'.$ligne['nb_mort'].'</td>'
		.'</tr>';
}
?>
	</table>
	<hr />
	<table>
		<tr style="vertical-align:top;">
		<td style="width:400px;">
			<p>Pour rappel, voici la liste des gains de points pour chaque action :</p>
			<p>Chaque <span class="underline">construction de batiment</span> vous rapportera un nombre spécifique de points, mais vous les predrez si il est détruit.</p>
			<p>Chaque <span class="underline">Quête</span> vous apporte un nombre spécifique de points.</p>
		</td>
		<td>
			<?php include_once('model/regles.php'); echo AfficheTableauGainScores();?>
		</td>
		</tr>
	</table>
</div>