<?php 
global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
$oMaison = $oJoueur->GetObjSaMaison();
?>

<div class="main">
	<h1>Les quêtes</h1>
	<p>Voici la liste des quêtes qui vous sont proposées. Acceptez une ou plusieurs quêtes et bonne chance!</p>
	<p><?php echo AfficheIcone('attention');?> Les quêtes ne peuvent s'accomplir que une seule fois. Si vous l'annulée, elle sera perdue.</p>
	
	<?php if($oJoueur->GetCoordonnee() != $oMaison->GetCoordonnee()){?>
		<p class="important"><?php echo AfficheIcone('attention');?>Vous devez vous placer sur votre <img src="./img/carte/<?php echo $oMaison->GetImgName();?>.png" /> pour vous <u>inscrire</u> à une nouvelle quête ou <u>valider</u> une en cours.</p>
	<?php }?>
	
		<h2>Quêtes en cours</h2>
		<?php if(isset($_SESSION['QueteEnCours'])){?>
			<table class="quetes">
				<tr>
				<?php
				Foreach($_SESSION['QueteEnCours'] as $Quete){
					//echo '<td>'.AfficheAvancementQuete($Quete, $oJoueur).'</td>';
					echo '<td>'.$Quete->AfficheDescriptif($oJoueur, $oMaison, true, $oJoueur->GetCoordonnee() == $oMaison->GetCoordonnee()).'</td>';
				}
				?>
				</tr>
			</table>
		<?php }else{?>
			<p>Vous n'avez aucune Quête en cours.</p>
		<?php }?>
		<h2>Et les quêtes disponibles</h2>
		<?php
		echo SelectQuete($oJoueur, $oJoueur->GetCoordonnee() == $oMaison->GetCoordonnee());
		?>
	
</div>

<?php 
$objManager->UpdateBatiment($oMaison);
$objManager->update($oJoueur);
unset($oJoueur, $oMaison);
?>