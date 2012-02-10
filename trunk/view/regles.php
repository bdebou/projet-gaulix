<?php
	$lstOnglets = array('général', 'inventaire', 'equipement', 'compétences', 'scores', 'quêtes', 'village', 'crédits');
?>
<div class="main">
	<div class="onglets">
<?php
	Foreach($lstOnglets as $Onglet){
		echo '
		<span 
			class="onglet_0 onglet" 
			id="onglet_'.$Onglet.'" 
			onclick="javascript:change_onglet(\''.$Onglet.'\');">'.ucfirst($Onglet).'
		</span>';
	}
?>
	</div>
	<div class="contenu_onglets">
<?php
	foreach($lstOnglets as $Onglet){
		$fctAffiche = 'Affiche_'.ucfirst(str_replace(array('é', 'è', 'ê'), 'e', $Onglet));
		echo '
		<div class="contenu_onglet" id="contenu_onglet_'.$Onglet.'">'
			.$fctAffiche()
			.'<div style="clear:both;"></div>'
		.'</div>';
	}
?>
	</div>
	<script type="text/javascript">
		//<!--
			function change_onglet(NewName){
				document.getElementById('onglet_'+OldName).className = 'onglet_0 onglet';
				document.getElementById('onglet_'+NewName).className = 'onglet_1 onglet';
				document.getElementById('contenu_onglet_'+OldName).style.display = 'none';
				document.getElementById('contenu_onglet_'+NewName).style.display = 'block';
				
				OldName = NewName;
			}
			var OldName = 'général';
			change_onglet(OldName);
		//-->
	</script>
</div>