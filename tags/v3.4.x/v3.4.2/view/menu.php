<div class="menu">
<?php
foreach ($arBtMenu as $bt) {
	echo '
	<button type="button" class="bt-menu" 
		onclick="window.location=\'' . $bt['link'] . '\';">'
	.$bt['name']
	.'</button>';
}
?>
</div>