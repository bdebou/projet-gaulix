<h1>Le Village</h1>
	<p>
		<img src="img/presentation/construction.png" alt="Construction" title="Case disponible pour la construction" style="float:left;margin:5px;" />
		Pour construire un bâtiment, vous devez avoir assez de ressource (logique) et surtout se trouver sur une case vide collée à un autre de vos bâtiments. 
		Donc à partir de votre <img src="img/maison-a.png" alt="Maison" title="Maison" height="20px" />, vous pouvez construire sur 8 cases différentes (les 4 cotés et les 4 coins) sauf si votre <img src="img/maison-a.png" alt="Maison" title="Maison" height="20px" /> est sur un bord. 
		Ensuite les autres bâtiments que vous construirez devront également être collé sur un des bâtiments déjà construit.
	</p>
	<p>Lorsque vous vous trouvez sur un de vos bâtiment, des options supplémentaires apparaissent sur différentes pages. 
	Par exemple, lorsque vous serez sur <img src="img/entrepot-a.png" alt="Entrepôt" title="Entrepôt" height="20px" />, vous aurez à partir de "Votre Bolga" la possibilité de transférer de votre bolga à votre <img src="img/entrepot-a.png" alt="Entrepôt" title="Entrepôt" height="20px" />. 
	L'avantage est que si vous mourez, vous réssusciterez à votre <img src="img/maison-a.png" alt="Maison" title="Maison" height="20px" /> et pourrez directement repasser par votre <img src="img/entrepot-a.png" alt="Entrepôt" title="Entrepôt" height="20px" /> pour vous équiper. 
	Et bien oui, car si vous mourez, vous perdez tout le contenu de votre bolga et équipement. C'est la même chose avec votre <img src="img/bank-a.png" alt="Banque" title="Banque" height="20px" /> mais biensur uniquement pour votre or.</p>
	<p><?php echo AfficheIcone('attention');?> Si votre <img src="img/entrepot-a.png" alt="Entrepôt" title="Entrepôt" height="20px" /> ou votre <img src="img/bank-a.png" alt="Banque" title="Banque" height="20px" /> sont détruits, leur contenu est récupéré par le gagnant.</p>
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
	<h2>Les bâtiments</h2>
		<p>Vous avez plusieurs bâtiments possibles à construire. Chacun d'eux a une utilité. (voir plus bas)</p>
		<h3>Amélioration</h3>
		<p>Chaque bâtiment peut être amélioré. Chaque amélioration à biensur un coût mais surtout une utilité. Son utilité est simple et est appliquée pour chaque passage de niveau :</p>
		<ul>
			<li>+50 pts de vie maximum</li>
			<li>+5 pts d'attaque</li>
			<li>+5 pts de défense</li>
			<li>+1 pt de distance</li>
			<li>+100 de capacité de stock (<img src="img/ferme-a.png" alt="Ferme" title="Ferme" height="20px" /> et <img src="img/mine-a.png" alt="Mine" title="Mine" height="20px" />)</li>
		</ul>
<?php echo ReglesAfficheTableauBatiment();?>