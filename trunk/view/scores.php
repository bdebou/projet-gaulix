<div class="main">
	<h1>Scores</h1>
	<h2>Le Classement</h2>
	<table class="classement">
		<tr style="background:grey;">
			<th style="width:60px;">Points</th>
			<th style="width:160px;">Nom (Niveau)</th>
			<th style="width:150px;">Expérience</th>
			<th style="width:150px;"><?php echo AfficheIcone('vie');?></th>
			<th><?php echo AfficheIcone('attaque');?></th>
			<th><?php echo AfficheIcone('defense');?></th>
			<th>Combats<br />Gagnés</th>
			<th>Combats<br />Perdus</th>
		</tr>
<?php
global $VieMaximum;
foreach($lignes as $ligne){
	echo '
		<tr>'
			.'<td>'.$ligne['nb_points'].'</td>'
			.'<td style="background:#c8c8c8; text-align:left;">'.$ligne['login'].' ('.$ligne['niveau'].')'.AfficheRecompenses($ligne['login'], $ligne['clan']).'</td>'
			.'<td><img alt="Barre d\'expérience" src="./fct/fct_image.php?type=experience&amp;value='.$ligne['experience'].'&amp;max='.(($ligne['niveau'] + 1) * 100).'" /></td>'
			.'<td><img alt="Barre de Vie" src="./fct/fct_image.php?type=vie&amp;value='.$ligne['vie'].'&amp;max='.$VieMaximum.'" /></td>'
			.'<td>'.$ligne['val_attaque'].'</td>'
			.'<td>'.$ligne['val_defense'].'</td>'
			.'<td>'.$ligne['nb_victoire'].'</td>'
			.'<td>'.$ligne['nb_vaincu'].'</td>'
		.'</tr>';
}
?>
	</table>
	<hr />
	<table>
		<tr style="vertical-align:top;">
		<td style="width:400px;">
			<p>Pour rappel, voici la liste des gains de points pour chaque action :</p>
			<p>Chaque <u>construction de batiment</u> vous rapportera un nombre spécifique de points, mais vous les predrez si il est détruit.</p>
			<p>Chaque <u>Quête</u> vous apporte un nombre spécifique de points.</p>
		</td>
		<td>
			<?php include_once('model/regles.php'); echo AfficheTableauGainScores();?>
		</td>
		</tr>
	</table>
</div>