<?php
function Affiche_Credits(){
	return utf8_decode(file_get_contents('http://code.google.com/p/projet-gaulix/wiki/credit?show=content'));
	/*
	 return '
			<h1>Cr�dits</h1>
			<p>Ce petit jeu est compl�tement gratuit.</p>
			<p>Un tout grand merci aux personnes ayant particip�es � ce projet personnel et toujours gratuitement.</p>
			<ul>
				<li>Programmation :
					<a rel="author" target="_blank" href="https://plus.google.com/u/0/107937906218732922871" style="text-decoration:none;">
						<img src="http://www.google.com/images/icons/ui/gprofile_button-16.png" width="16" height="16" alt="Google+ profile" title="Google+ profile" style="border:0;">
						+Bruno Deboubers
					</a>
				</li>
				<li>Illustrations : Raphael Lopez Perez</li>
			</ul>';
	*/
}
function Affiche_General(){
	global $temp_attente, $nbDeplacement, $temp_combat;
	return '
			<h1>Fonctionnement g�n�ral</h1>
			<p>Toutes les '.($temp_attente / 3600).'h, vous recevez '.$nbDeplacement.'pt de d�placement. Pour chaque d�placement, vous augmentez votre exp�rience de 1pt.</p>
			<div style="float:left; width:500px;">
				<p>Pour passer au niveau suivant, vous devez atteindre un niveau X d\'exp�rience. Pour exemple, voir tableau ci-contre.</p>
				<p>Plus votre niveau est �lev�, plus vous trouverez des objets int�ressants, vous aurez des qu�tes int�ressantes.</p>
			</div>
			<div style="float:left;">
				<table class="experience">
					<tr style="background:grey;"><th>Niveau</th><th>Exp�rience</th></tr>
					<tr><td>0</td><td>100</td></tr>
					<tr><td>1</td><td>200</td></tr>
					<tr><td>2</td><td>300</td></tr>
					<tr><td>3</td><td>400</td></tr>
					<tr><td>...</td><td>...</td></tr>
				</table>
			</div>
			<p style="clear:both;">Pour certaines actions, un nombre X de points vous sera attribu� et un tableau des scores sera cr��.</p>
			<div style="float:left; width:400px;">
				<p>Pour la liste des points attribu� pour chaque action, voir tableau ci-contre.</p>
				<p>Chaque <u>construction de batiment</u> vous rapportera un nombre sp�cifique de points, mais vous les predrez si il est d�truit.</p>
				<p>Chaque <u>Qu�te</u> vous apporte un nombre sp�cifique de points.</p>
			</div>
			<div style="float:left;">'
	.AfficheTableauGainScores()
	.'</div>
			<h2 style="clear:both;">Combats</h2>
			<p>Vous pouvez combattre d\'autres joueurs ou attaquer des b�timents ou bien encore des monstres de qu�te tant que ceux-ci n\'ont pas �t� attaqu�s dans les '.($temp_combat / 3600).'hrs.</p>
			<h3>Combats entre joueur</h3>
			<p>Une valeur de combat sera calcul�e suivant la formule suivante : <i>(Attaque * 1.15) + D�fense </i>.
			Cette valeur est calcul�e pour votre adversaire �galement et une diff�rence en est tir�e. 
			Celui qui a la valeur de combat la plus �lev�e gagne le combat. Le gagnant du combat augmente son exp�rience de 5pts et vole un peu d\'or au perdant. 
			Le perdant, lui, perd de l\'or et des pts de vie correspondant � la diff�rence des 2 valeurs de combats.</p>
			<h3>Attaques de b�timent</h3>
			<p>C\'est le m�me principe que pour le combat entre joueur mais la diff�rence se situe au niveau des cons�quences.</p>
			<p>Le b�timent perdra des pts de vie selon la formule suivante : <i>ValeurCombatJoueur - DefenseBatiment</i>. Si la diff�rence est n�gative, le b�timent ne perdra pas de points.</p>
			<p>Et le joueur perdra des pts de vies selon la formule suivante : <i>(AttaqueBatiment * 1.15) - DefenseJoueur</i>. Si la diff�rence est n�gative, le joueur ne perdra pas de points.</p>
			<p>Dans tous les cas, le joueur augmentera son exp�rience de 5pts.</p>
			<h2>Votre Village</h2>
			<p>Pour construire un b�timent, vous devez avoir assez d\'argent (logique) et surtout se trouver sur une case vide coll�e � un autre de vos b�timents. 
			Donc � partir de votre maison, vous pouvez construire sur 8 cases diff�rentes (les 4 cot�s et les 4 coins) sauf si votre maison est sur un bord. 
			Ensuite les autres b�timents que vous construirez devront �galement �tre coll� sur un des b�timents d�j� construit.</p>
			<p>Lorsque vous vous trouvez sur un de vos b�timent, des options suppl�mentaires apparaissent sur diff�rentes pages. 
			Par exemple, lorsque vous serez sur "Entrep�t", vous aurez sur votre page "Votre Bolga" la possibilit� de transf�rer de votre bolga � votre Entrep�t. 
			L\'avantage est que si vous mourez, vous r�ssusciterez � votre maison et pourrez directement repasser par votre entrep�t pour vous �quiper. 
			Et bien oui, car si vous mourez, vous perdez tout le contenu de votre bolga et �quipement. C\'est la m�me chose avec votre banque mais biensur uniquement pour votre or.</p>
			<p>'.AfficheIcone('attention').' Si votre entrep�t ou votre banque sont d�truits, leur contenu est r�cup�r� par le gagnant.</p>
			<table>
				<tr>
					<td>
						<img src="./img/druide.png" height="200px" alt="Votre druide" title="Votre Druide" />
					</td>
					<td>
						<p>Dans votre Maison, vous trouverez un Druide. Ce Druide ne parle qu\'aux personnes de niveau 1 minimum. Il vous proposera plusieurs actions dont changer de la nourriture en hydromel, des sorts en tout genre, ...</p>
					</td>
				</tr>
			</table>';
}
function Affiche_Inventaire(){
	return '
			<h1>Inventaire</h1>
			<p>Liste toutes les choses que vous avez trouv�s au cours de vos voyages avec les �quipements aussi (voir Equipements).</p>
			<h2 style="clear:both;">Les ressources</h2>'
	.ReglesAfficheTableauEquipements('ressource');
		
}
function Affiche_Equipement(){
	return '
			<h1>Equipement</h1>
			<p>Vous pr�sente votre �quipement. Chaque �quipement augmente votre pouvoir d\'attaque ou de d�fense.</p>
			<p>Si vous cliquez sur un �l�ment, il sera remis dans votre inventaire.</p>
			<p>Voici la liste des �quipements possibles avec leurs caract�ristiques et valeurs.</p>
			<h2 style="clear:both;">Armes</h2>'
	.ReglesAfficheTableauEquipements('arme')
	.'<h2 style="clear:both;">Boucliers</h2>'
	.ReglesAfficheTableauEquipements('bouclier')
	.'<h2 style="clear:both;">Casques</h2>'
	.ReglesAfficheTableauEquipements('casque')
	.'<h2 style="clear:both;">Jambi�res</h2>'
	.ReglesAfficheTableauEquipements('jambiere')
	.'<h2 style="clear:both;">Cuirasses</h2>'
	.ReglesAfficheTableauEquipements('cuirasse');
}
function Affiche_Competences(){
	return '
			<h1>Comp�tences</h1>
			<p>Dans cette partie, vous pourrez am�liorer votre attaque et votre d�fense pour un prix modeste et un temps de travail correct.</p>
			<p>Mais aussi apprendre des comp�tences diverses et vari�es qui vous permettront de fabriquer des armes, objets, boucliers, ...</p>';
}
function Affiche_Scores(){
	return '
			<h1>Scores</h1>
			<p>Vous trouverez la liste des joueurs avec le nombre de combats gagn�s et perdus. Les combats dont les r�sultats sont nuls ne sont pas comptabilis�s.</p>
			<p>Vous y trouverez �galement un aper�u complet des autres joueurs except� leurs �quipements.</p>';
}
function Affiche_Quetes(){
	global $nbQueteMax;
	return '
			<h1>Les Qu�tes</h1>
			<p>En vous inscrivant � une qu�te, vous avez la possibilit� de gagner de l\'argent, beaucoup d\'argent. Vous ne pouvez vous s\'inscrire qu\'� '.$nbQueteMax.' qu�tes maximum.</p>
			<p>Les qu�tes sont dispers�es sur toutes les cartes uniquement quand vous serez pass� au niveau 4. Avant ce niveau, les qu�tes seront cr��es sur la carte o� votre village est install�. Bonne recherche !</p>
			<p>Vous trouverez la liste des qu�tes possible en fonction de votre niveau. Il existe 4 types de qu�tes.</p>
			<ol>
				<li>Trouvez et tuez un monstre</li>
				<li>Trouvez un personnage</li>
				<li>Trouvez un personnage dans un d�lai donn�</li>
				<li>Trouvez et gardez un objet</li>
			</ol>
			<h2>Les Qu�tes "Tuez un Monstre"</h2>
			<p>Pour ces qu�tes, vous devrez trouver et attaquer un monstre. Apr�s chaque attaque ce monstre fuira pour se cacher quelque part. A vous de le retrouver au plus vite pour l\'achever.</p>
			<h2>Les Qu�tes "Trouvez un personnage"</h2>
			<p>Il y a 2 types de qu�te diff�rente: celle avec un temps donn�e et les autres. Dans les 2 cas, vous devrez juste vous baladez sur la carte � leur recherche. Quand vous aurez retrouv� le personnage, vous recevrez votre d�. Par contre dans le cas o� vous ne le trouvez pas end�ans le d�lai donn�, vous ne recevrez pas votre d�.</p>
			<h2>Les Qu�tes "Trouvez un objet"</h2>
			<p>Pour ces qu�tes, vous devrez vous balader sur la carte � la recherche de cet objet. Une fois trouv�, Vous toucherez votre d� et le garderai. Il sera mis directement dans votre inventaire.</p>
			<p>A vous maintenant de vous en �quiper ou vendre ou jeter.</p>';
}
function Affiche_Village(){
	return '
			<h1>Le Village</h1>
			<h2>Les b�timents</h2>
			<p>Vous avez plusieurs b�timents possibles � construire. Chacun d\'eux a une utilit�.</p>
			<p>Chaque b�timent peut �tre am�lior�. Chaque am�lioration � biensur un co�t mais surtout une utilit�. Son utilit� est simple et est appliqu�e pour chaque passage de niveau :</p>
			<ul>
				<li>+50 pts de vie maximum</li>
				<li>+5 pts d\'attaque</li>
				<li>+5 pts de d�fense</li>
				<li>+1 pt de distance</li>
				<li>+100 de capacit� de stock (ferme et mine)</li>
			</ul>'
	.ReglesAfficheTableauBatiment();
}
function ReglesAfficheTableauBatiment(){
	global $arCouleurs;
	$txt = '
<table class="village">';
	$sql="SELECT * FROM table_batiment;";
	$requete = mysql_query($sql) or die (mysql_error());
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		if(in_array($row['batiment_type'], array('maison', 'bank', 'entrepot', 'tour', 'mur', 'ferme', 'marcher', 'mine'))){
			$txt .= '
	<tr>
	
		<td rowspan="6">
		<img src="./img/batiments/'.$row['batiment_type'].'-0.png" alt="B�timent ('.$row['batiment_type'].')" width="300px" />
		</td>
		<th colspan="4">'.$row['batiment_nom'].' <img src="./img/'.$row['batiment_type'].'-a.png" alt="Icone b�timent ('.$row['batiment_type'].')" /></th>
	</tr>
	<tr>
		<th colspan="4">Prix</th>
	</tr>
	<tr>
		<td style="background-color:#'.$arCouleurs['Or'].';">'.AfficheIcone('or').' : '.$row['prix_or'].'</td>
		<td style="background-color:#'.$arCouleurs['Bois'].';">'.AfficheIcone('bois').' : '.$row['prix_bois'].'</td>
		<td style="background-color:#'.$arCouleurs['Pierre'].';">'.AfficheIcone('pierre').' : '.$row['prix_pierre'].'</td>
		<td style="background-color:#'.$arCouleurs['Nourriture'].';">'.AfficheIcone('nourriture').' : '.$row['prix_nourriture'].'</td>
	</tr>
	<tr>
		<th colspan="4">Combat</th>
	</tr>
	<tr>
		<td style="background-color:#'.$arCouleurs['Attaque'].';">'.AfficheIcone('attaque').': '.(is_null($row['batiment_attaque'])?'0':$row['batiment_attaque']).'</td>
		<td style="background-color:#'.$arCouleurs['Attaque'].';">'.AfficheIcone('distance').': '.(is_null($row['batiment_distance'])?'0':$row['batiment_distance']).'</td>
		<td style="background-color:#'.$arCouleurs['Defense'].';">'.AfficheIcone('defense').': '.(is_null($row['batiment_defense'])?'0':$row['batiment_defense']).'</td>
		<td style="background-color:#'.$arCouleurs['Vie'].';">'.AfficheIcone('vie').': '.$row['batiment_vie'].'</td>
	</tr>
	<tr>
		<td colspan="6" style="text-align:left;">'.$row['batiment_description'].'</td>
	</tr>
	<tr style="background:lightgrey;">
		<td colspan="5">&nbsp;</td>
	</tr>';
		}
	}
	$txt .= '
</table>
';
	return $txt;
}
function ReglesAfficheTableauEquipements($equipement){
	$bckColor='#c8c8c8';
	$txt=null;

	$sql="SELECT *
			FROM table_objets 
			WHERE objet_type='".$equipement."'"
	.($equipement != 'ressource'?' AND objet_quete IS NULL':'').";";

	$requete = mysql_query($sql) or die (mysql_error());
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		if($bckColor=='#c8c8c8'){
			$bckColor='white';
		}else{$bckColor='#c8c8c8';
		}
		if($equipement != 'ressource'){
			
			$InfoBulle = '<table class="equipement">'
			.'<tr>'
			.'<td>'.AfficheIcone('attaque').' : '.$row['objet_attaque'].'</td>'
			.($equipement=='arme'?'<td>'.AfficheIcone('distance').' : '.($row['objet_distance']!=0?$row['objet_distance']:'0'):'').'</td>'
			.'</tr>'
			.'<tr><td'.($equipement=='arme'?' colspan="2"':'').'>'.AfficheIcone('defense').' : '.$row['objet_defense'].'</td></tr>'
			.'<tr><td'.($equipement=='arme'?' colspan="2"':'').'>'.AfficheIcone('or').' : '.$row['objet_prix'].'</td></tr>'
			.'<tr><td'.($equipement=='arme'?' colspan="2"':'').'>Niveau requis : '.$row['objet_niveau'].'</td></tr>'
			.'</table>';
		}else{
			$InfoBulle = null;
		}

		$txt .= '
	
<table class="equipements">
	<tr style="background:'.$bckColor.';">
		<td >
			<img src="./img/objets/'.$row['objet_code'].'.png" 
				height="100px" '
		.'alt="'.$row['objet_nom'].'" '
		.(!is_null($InfoBulle)?'onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" onmouseout="cache();" ':'')
		.'/>
		</td>
	</tr>
	<tr style="background:'.$bckColor.';">
		<th style="text-align:left;">'.$row['objet_nom'].'</th>
	</tr>
</table>';
	}

	return $txt;
}
function AfficheTableauGainScores(){
	global $lstPoints;

	$txt = '<table class="points">
		<tr style="background:grey;"><th style="width:40px;">Pts</th><th style="width:300px;">Action</th></tr>';
	foreach($lstPoints as $arPts){
		$txt .= '<tr><td style="background:'.($arPts[0] > 0?'LightGreen':'LightSalmon').';">'.($arPts[0] > 0?'+'.$arPts[0]:$arPts[0]).'</td><td>'.$arPts[1].'</td></tr>';
	}
	$txt .= '</table>';

	return $txt;
}
?>