<div class="main">
	<h1>Les quêtes</h1>
	<p>Voici la liste des quêtes qui vous sont proposées. Acceptez une ou plusieurs quêtes et bonne chance!</p>
	<p><?php echo AfficheIcone('attention');?> Les quêtes ne peuvent s'accomplir que une seule fois. Si vous l'annulée, elle sera perdue.</p>
	<?php
	echo SelectQuete($_SESSION['QueteEnCours']);
	?>
</div>