<div class="main">

<?php
	global $objManager;
	
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
?>

<h1>Artisanat</h1>
<p>Vous devez avoir tous les éléments dans votre Bolga pour pouvoir bricoler quelque chose.</p>
<?php echo AfficheListeElementBricolage($oJoueur, (isset($_GET['onglet'])?$_GET['onglet']:null));?>
</div>

<?php 
	$objManager->update($oJoueur);
	unset($oJoueur);
?>