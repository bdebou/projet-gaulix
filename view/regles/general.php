<?php global $temp_combat;?>

<h1>Fonctionnement général</h1>
<h2>Barre de status</h2>
<img src="img/presentation/status.png" alt="status" title="Barre de status" />
<p>Toutes les <?php echo (personnage::TEMP_DEPLACEMENT_SUP / 3600);?>h, vous recevez <?php echo personnage::NB_DEPLACEMENT_SUP;?>pt de déplacement.</p>
<p>Pour chaque déplacement, vous augmentez votre expérience de 1pt.</p>
<div style="float:left; width:500px;">
	<p>Pour passer au niveau suivant, vous devez atteindre un niveau X d'expérience. Voir tableau ci-contre.</p>
	<p>Plus votre niveau est élevé, plus vous trouverez des objets intéressants, vous aurez des quêtes intéressantes.</p>
	<p>Pour chaque action, des points vous seront attribués. Pour en connaitre le détail, allez voir dans <a href="#" onclick="javascript:change_onglet('scores');">Scores</a>.</p>
</div>
<div style="float:left;">
	<table class="experience">
		<tr style="background:grey;"><th>Niveau</th><th>Expérience</th></tr>
		<tr><td>0</td><td>100</td></tr>
		<tr><td>1</td><td>300</td></tr>
		<tr><td>2</td><td>600</td></tr>
		<tr><td>3</td><td>1000</td></tr>
		<tr><td>...</td><td>...</td></tr>
	</table>
</div>
<h2 style="clear:both;">Combats</h2>
<p>Vous pouvez combattre d'autres gaulois ou attaquer des bâtiments ou bien encore des monstres de quête.</p>
<p>Entre chaque combat, il devra se passer au minimum <?php echo ($temp_combat / 3600);?>hrs. Ceci uniquement pour ne pas s'acharner sur la victime et lui laisser peut-être une chance.</p>
	<h3>Combats entre joueur</h3>
	<p>Pour le résultat du combat, 2 valeurs seront calculées selon les formules suivantes :</p>
		<ul>
			<li>Valeur de l'attaquant : (<var>Attaque</var><strong> * <?php echo personnage::TAUX_ATTAQUANT;?></strong>) + <var>Défense</var></li>
			<li>Valeur de la cible : <var>Attaque</var> + <var>Défense</var></li>
		</ul>
		<p>Le résultat du combat dépendra de la comparaison de ces 2 valeurs. Celui qui a la valeur de combat la plus élevée gagne le combat.</p>
		<ul>
			<li>Le gagnant du combat augmente son expérience de 5pts et vole un peu d'or au perdant.</li> 
			<li>Le perdant, lui, perd de l'or et des pts de vie correspondant à la différence des 2 valeurs de combats.</li>
		</ul>
	<h3>Attaques de bâtiment</h3>
	<p>C'est le même principe que pour le combat entre joueur mais la différence se situe au niveau des conséquences.</p>
	<p>Le bâtiment perdra des pts de vie selon la formule suivante : <i>ValeurCombatJoueur - DefenseBatiment</i>. Si la différence est négative, le bâtiment ne perdra pas de points.</p>
	<p>Et le joueur perdra des pts de vies selon la formule suivante : <i>(AttaqueBatiment * <?php echo batiment::TAUX_ATTAQUANT;?>) - DefenseJoueur</i>. Si la différence est négative, le joueur ne perdra pas de points.</p>
	<p>Dans tous les cas, le joueur augmentera son expérience de 5pts.</p>
