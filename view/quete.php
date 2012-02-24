<?php 
global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
?>

<div class="main">
	<h1>Les quêtes</h1>
	<p>Voici la liste des quêtes qui vous sont proposées. Acceptez une ou plusieurs quêtes et bonne chance!</p>
	<p><?php echo AfficheIcone('attention');?> Les quêtes ne peuvent s'accomplir que une seule fois. Si vous l'annulée, elle sera perdue.</p>
	<h2>Quêtes en cours</h2>
		<table class="quetes">
			<tr>
			<?php
			Foreach($_SESSION['QueteEnCours'] as $Quete){
				echo '<td>'.AfficheAvancementQuete($Quete).'</td>';
			}
			?>
			</tr>
		</table>
	<h2>Et les quêtes disponibles</h2>
	<?php 
	echo SelectQuete($oJoueur);
	?>
</div>

<?php 
$objManager->update($oJoueur);
unset($oJoueur);
?>