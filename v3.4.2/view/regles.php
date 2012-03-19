<?php
	$lstOnglets = array('g�n�ral', 'inventaire', 'equipement', 'comp�tences', 'scores', 'qu�tes', 'village', 'cr�dits');
	if(!isset($_SESSION['joueur'])){
		$lstOnglets = array_merge(array('pr�sentation'), $lstOnglets);
		$FirstOnglet = 'pr�sentation';
	}else{
		$FirstOnglet = 'g�n�ral';
	}
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
		echo '<div class="contenu_onglet" id="contenu_onglet_'.$Onglet.'">';
		
		include('view/regles/'.strtolower (str_replace(array('�', '�', '�'), 'e', $Onglet)).'.php');
		
		echo '<div style="clear:both;"></div>';
		
		echo '</div>';
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
			var OldName = '<?php echo $FirstOnglet;?>';
			change_onglet(OldName);
		//-->
	</script>
</div>