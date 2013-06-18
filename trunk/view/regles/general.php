<?php global $temp_combat;?>

<h1>Fonctionnement g�n�ral</h1>
<h2>Barre de status</h2>
<img src="img/presentation/status.png" alt="status" title="Barre de status" />
<p>Toutes les <?php echo (personnage::TEMP_DEPLACEMENT_SUP / 3600);?>h, vous recevez <?php echo personnage::NB_DEPLACEMENT_SUP;?>pt de d�placement.</p>
<p>Pour chaque d�placement, vous augmentez votre exp�rience de 1pt.</p>
<div style="float:left; width:500px;">
	<p>Pour passer au niveau suivant, vous devez atteindre un niveau X d'exp�rience. Voir tableau ci-contre.</p>
	<p>Plus votre niveau est �lev�, plus vous trouverez des objets int�ressants, vous aurez des qu�tes int�ressantes.</p>
	<p>Pour chaque action, des points vous seront attribu�s. Pour en connaitre le d�tail, allez voir dans <a href="#" onclick="javascript:change_onglet('scores');">Scores</a>.</p>
</div>
<div style="float:left;">
	<table class="experience">
		<tr style="background:grey;"><th>Niveau</th><th>Exp�rience</th></tr>
		<tr><td>0</td><td>100</td></tr>
		<tr><td>1</td><td>300</td></tr>
		<tr><td>2</td><td>600</td></tr>
		<tr><td>3</td><td>1000</td></tr>
		<tr><td>...</td><td>...</td></tr>
	</table>
</div>
<h2 style="clear:both;">Combats</h2>
<p>Vous pouvez combattre d'autres gaulois ou attaquer des b�timents ou bien encore des monstres de qu�te.</p>
<p>Entre chaque combat, il devra se passer au minimum <?php echo ($temp_combat / 3600);?>hrs. Ceci uniquement pour ne pas s'acharner sur la victime et lui laisser peut-�tre une chance.</p>
	<h3>Combats entre joueur</h3>
	<p>Pour le r�sultat du combat, 2 valeurs seront calcul�es selon les formules suivantes :</p>
		<ul>
			<li>Valeur de l'attaquant : (<var>Attaque</var><strong> * <?php echo personnage::TAUX_ATTAQUANT;?></strong>) + <var>D�fense</var></li>
			<li>Valeur de la cible : <var>Attaque</var> + <var>D�fense</var></li>
		</ul>
		<p>Le r�sultat du combat d�pendra de la comparaison de ces 2 valeurs. Celui qui a la valeur de combat la plus �lev�e gagne le combat.</p>
		<ul>
			<li>Le gagnant du combat augmente son exp�rience de 5pts et vole un peu d'or au perdant.</li> 
			<li>Le perdant, lui, perd de l'or et des pts de vie correspondant � la diff�rence des 2 valeurs de combats.</li>
		</ul>
	<h3>Attaques de b�timent</h3>
	<p>C'est le m�me principe que pour le combat entre joueur mais la diff�rence se situe au niveau des cons�quences.</p>
	<p>Le b�timent perdra des pts de vie selon la formule suivante : <i>ValeurCombatJoueur - DefenseBatiment</i>. Si la diff�rence est n�gative, le b�timent ne perdra pas de points.</p>
	<p>Et le joueur perdra des pts de vies selon la formule suivante : <i>(AttaqueBatiment * <?php echo batiment::TAUX_ATTAQUANT;?>) - DefenseJoueur</i>. Si la diff�rence est n�gative, le joueur ne perdra pas de points.</p>
	<p>Dans tous les cas, le joueur augmentera son exp�rience de 5pts.</p>
