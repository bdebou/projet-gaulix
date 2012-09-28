<?php 
global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
$oMaison = $oJoueur->GetObjSaMaison();
?>

<div class="main">
	<h1>Les qu�tes</h1>
	<p>Voici la liste des qu�tes qui vous sont propos�es. Acceptez une ou plusieurs qu�tes et bonne chance!</p>
	<p><?php echo AfficheIcone('attention');?> Les qu�tes ne peuvent s'accomplir que une seule fois. Si vous l'annul�e, elle sera perdue.</p>
	
	<?php if($oJoueur->GetCoordonnee() != $oMaison->GetCoordonnee()){?>
		<p class="important"><?php echo AfficheIcone('attention');?>Vous devez vous placer sur votre <img src="./img/carte/<?php echo $oMaison->GetImgName();?>.png" /> pour vous <u>inscrire</u> � une nouvelle qu�te ou <u>valider</u> une en cours.</p>
	<?php }?>
	
		<h2>Qu�tes en cours</h2>
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
			<p>Vous n'avez aucune Qu�te en cours.</p>
		<?php }?>
		<h2>Et les qu�tes disponibles</h2>
		<?php
		echo SelectQuete($oJoueur, $oJoueur->GetCoordonnee() == $oMaison->GetCoordonnee());
		?>
	
</div>

<?php 
$objManager->UpdateBatiment($oMaison);
$objManager->update($oJoueur);
unset($oJoueur, $oMaison);
?>