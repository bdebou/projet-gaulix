<div class="main">
	<h1>Les qu�tes</h1>
	<p>Voici la liste des qu�tes qui vous sont propos�es. Acceptez une ou plusieurs qu�tes et bonne chance!</p>
	<p><?php echo AfficheIcone('attention');?> Les qu�tes ne peuvent s'accomplir que une seule fois. Si vous l'annul�e, elle sera perdue.</p>
	<?php
	echo SelectQuete($_SESSION['QueteEnCours']);
	?>
</div>