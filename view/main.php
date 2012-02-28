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
			<tr>
				<td>
					<div class="jeux-gratuit_net" style="position:relative;width:60px;height:31px;">
						<a href="http://www.jeu-gratuit.net/jeux-de-strategie-et-jeux-de-role/jeu-gaulix.html" target="_blank">
							<img src="http://www.jeu-gratuit.net/images/bannieres/88x31_general_7.png" alt="votez pour Gaulix" width="60" height="31" border="0" />
						</a>
						<a href="http://www.jeu-gratuit.net/jeux-de-strategie-et-jeux-de-role/jeu-gaulix.html#voter" target="_blank" style="position:absolute; top:8px; left:-1px; font-family:arial; font-size:11px; font-weight:bold; color:#000000; text-decoration:none; line-height:12px; width:60px; text-align:center;">Votez ici !!</a>
					</div>
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