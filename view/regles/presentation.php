<h1>Gaulix</h1>
<p>
	<img src="img/presentation/carte.png" alt="Carte" title="Exemple de carte" style="float:right; margin:5px;" />
	Vous incarnez un gaulois ou un romain qui doit survivre et évoluer.
</p>
<p>Vous vous déplacerez sur une carte de case en case.</p>
<p>La carte globale comporte 25x la carte ci-contre. Pour un total de 14x14 cases par carte, 25 cartes donc <strong>4900 cases</strong>.</p>
<p>
	<img src="img/presentation/move.png" alt="Move" title="Les flèches de directions" style="float:left; margin:5px;" />
	Vous avez un nombre X de déplacement. Toutes les <?php echo (personnage::TEMP_DEPLACEMENT_SUP / 3600);?> h, connecté ou non, vous gagnerez <?php echo personnage::NB_DEPLACEMENT_SUP;?>pt de déplacement.
</p> 
<p>Comme vous pouvez le voir ci-contre, il reste 23 déplacements et plus de 58 min pour en gagner un nouveau.</p>
<p>Vous aurez plusieurs d'actions possible en fonction de votre niveau, compétences acquises (voir <a href="#" onclick="javascript:change_onglet('compétences');">Compétences</a>), quêtes terminées (voir <a href="#" onclick="javascript:change_onglet('quêtes');">Quêtes</a>), ... .</p>
<p>
	<img src="img/presentation/actions.png" alt="Actions" title="Exemple d'actions" style="float:left;" />
	Comme dans cette exemple, vous pouvez attaquer un romain (<img src="img/carte/romains-b.png" alt="Autre joueur" title="Romains ZUT" height="20px" />) et vous prourriez construire un bâtiment.
</p>
<p>Malheureusement dans ce cas-ci, vous n'avez pas assez de ressources pour construire la "Palissade". <strong>Allez couper du bois!</strong></p>