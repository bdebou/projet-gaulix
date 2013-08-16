<div class="main">
	<h1>La Carte</h1>
	<table class="all_cartes">
<?php 
$numCol = 0;

for($i=0; $i <= (count($arCartes) - 1); $i++){
	if($numCol == 0){
		echo '
	<tr>';
	}
	echo '<td>'.AfficheCarte($arCartes[$i], true, $arTailleCarte).'</td>';
	
	$numCol++;
	
	if($numCol == 5){
		echo '
	</tr>';
		$numCol = 0;
	}
}

?>
	</table>
</div>