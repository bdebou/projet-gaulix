<?php 
global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
?>

<div class="main">
	<h1>Les qu�tes</h1>
	<p>Voici la liste des qu�tes qui vous sont propos�es. Acceptez une ou plusieurs qu�tes et bonne chance!</p>
	<p><?php echo AfficheIcone('attention');?> Les qu�tes ne peuvent s'accomplir que une seule fois. Si vous l'annul�e, elle sera perdue.</p>
	<h2>Qu�tes en cours</h2>
		<table class="quetes">
			<tr>
			<?php
			Foreach($_SESSION['QueteEnCours'] as $Quete){
				echo '<td>'.AfficheAvancementQuete($Quete).'</td>';
			}
			?>
			</tr>
		</table>
	<h2>Et les qu�tes disponibles</h2>
	<?php 
	echo SelectQuete($oJoueur);
	?>
</div>

<?php 
$objManager->update($oJoueur);
unset($oJoueur);
?>