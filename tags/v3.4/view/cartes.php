<div class="main">
	<h1>La Carte</h1>
	<table style="border:none;" class="all_cartes">
<?php 
$arCarteNum = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y');
$numCol = 0;

for($i=0; $i <= (count($arCarteNum) - 1); $i++){
	if($numCol == 0){
		echo '
	<tr>';
	}
	echo '<td>'.AfficheCarte($arCarteNum[$i], true).'</td>';
	
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