<?php
	include('model/village.php');
	Global $lstBatimentConstructible;
	
	echo utf8_decode(file_get_contents('http://code.google.com/p/projet-gaulix/wiki/Village?show=content'));
?>
	<table class="village">
	
<?php 
	Foreach($lstBatimentConstructible as $IDBatiment)
	{
		echo AfficheBatiment(FoundBatiment($IDBatiment));
	}
?>
	</table>