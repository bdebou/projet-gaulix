<h1>Le Village</h1>
	<p>
		<img src="img/presentation/construction.png" alt="Construction" title="Case disponible pour la construction" style="float:left;margin:5px;" />
		Pour construire un b�timent, vous devez avoir assez de ressource (logique) et surtout se trouver sur une case vide coll�e � un autre de vos b�timents. 
		Donc � partir de votre <img src="img/maison-a.png" alt="Maison" title="Maison" height="20px" />, vous pouvez construire sur 8 cases diff�rentes (les 4 cot�s et les 4 coins) sauf si votre <img src="img/maison-a.png" alt="Maison" title="Maison" height="20px" /> est sur un bord. 
		Ensuite les autres b�timents que vous construirez devront �galement �tre coll� sur un des b�timents d�j� construit.
	</p>
	<p>Lorsque vous vous trouvez sur un de vos b�timent, des options suppl�mentaires apparaissent sur diff�rentes pages. 
	Par exemple, lorsque vous serez sur <img src="img/entrepot-a.png" alt="Entrep�t" title="Entrep�t" height="20px" />, vous aurez � partir de "Votre Bolga" la possibilit� de transf�rer de votre bolga � votre <img src="img/entrepot-a.png" alt="Entrep�t" title="Entrep�t" height="20px" />. 
	L'avantage est que si vous mourez, vous r�ssusciterez � votre <img src="img/maison-a.png" alt="Maison" title="Maison" height="20px" /> et pourrez directement repasser par votre <img src="img/entrepot-a.png" alt="Entrep�t" title="Entrep�t" height="20px" /> pour vous �quiper. 
	Et bien oui, car si vous mourez, vous perdez tout le contenu de votre bolga et �quipement. C'est la m�me chose avec votre <img src="img/bank-a.png" alt="Banque" title="Banque" height="20px" /> mais biensur uniquement pour votre or.</p>
	<p><?php echo AfficheIcone('attention');?> Si votre <img src="img/entrepot-a.png" alt="Entrep�t" title="Entrep�t" height="20px" /> ou votre <img src="img/bank-a.png" alt="Banque" title="Banque" height="20px" /> sont d�truits, leur contenu est r�cup�r� par le gagnant.</p>
	<table>
		<tr>
			<td>
				<img src="./img/druide.png" height="200px" alt="Votre druide" title="Votre Druide" />
			</td>
			<td>
				<p>Dans votre <img src="img/maison-a.png" alt="Maison" title="Maison" height="20px" />, vous trouverez un Druide. Ce Druide ne parle qu'aux personnes de niveau 1 minimum. Il vous proposera plusieurs actions dont changer de la nourriture en hydromel, des sorts en tout genre, ...</p>
			</td>
		</tr>
	</table>
	<h2>Les b�timents</h2>
		<p>Vous avez plusieurs b�timents possibles � construire. Chacun d'eux a une utilit�. (voir plus bas)</p>
		<h3>Am�lioration</h3>
		<p>Chaque b�timent peut �tre am�lior�. Chaque am�lioration � biensur un co�t mais surtout une utilit�. Son utilit� est simple et est appliqu�e pour chaque passage de niveau :</p>
		<ul>
			<li>+50 pts de vie maximum</li>
			<li>+5 pts d'attaque</li>
			<li>+5 pts de d�fense</li>
			<li>+1 pt de distance</li>
			<li>+100 de capacit� de stock (<img src="img/ferme-a.png" alt="Ferme" title="Ferme" height="20px" /> et <img src="img/mine-a.png" alt="Mine" title="Mine" height="20px" />)</li>
		</ul>
<?php echo ReglesAfficheTableauBatiment();?>