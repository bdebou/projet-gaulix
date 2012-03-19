<?php 
global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

?>
<div class="main">
	<div class="carte"><?php echo AfficheCarte($oJoueur->GetCarte());?></div>
	<div class="mouvements"><?php echo AfficheMouvements($oJoueur);?></div>
	<div class="module_social">
		<table class="module_social">
			<tr>
				<td>
					<div class="fb-like" data-href="https://www.facebook.com/pages/Gaulix/215647241841733" data-send="false" data-layout="box_count" data-width="55"></div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="g-plusone" data-size="tall" data-href="http://www.gaulix.be"></div>
				</td>
			</tr>
		</table>
	</div>
	<div class="pub">
		<script type="text/javascript"><!--
			google_ad_client = "ca-pub-2161674761092050";
			/* Pub Fight 2 */
			google_ad_slot = "3236602268";
			google_ad_width = 300;
			google_ad_height = 250;
			//-->
		</script>
		<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
	</div>
	<div class="actions"><?php AfficheActions($oJoueur);?></div>
	<div class="history">
		<h1>Historique</h1>
		<?php echo AfficheHistory($oJoueur);?>
	</div>
</div>
<?php 
$objManager->update($oJoueur);
unset($oJoueur);
?>