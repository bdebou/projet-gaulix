<?php
function AfficheLignesClassement($Civilisation){

	$sql = "SELECT login, niveau, experience, val_attaque, val_defense, vie, nb_points, nb_victoire, nb_vaincu, clan, nb_mort 
			FROM table_joueurs 
			WHERE civilisation='".$Civilisation."' 
			ORDER BY nb_points DESC, niveau DESC, experience DESC;";
	$requete = mysql_query($sql) or die (mysql_error());
	
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		$lignes[] = $row;
	}
	
	return $lignes;
}
?>