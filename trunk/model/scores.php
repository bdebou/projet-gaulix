<?php
function AfficheLignesClassement($Civilisation){
	global $objManager;
	
	$sql = "SELECT login 
			FROM table_joueurs 
			WHERE civilisation='".$Civilisation."' 
			ORDER BY nb_points DESC, niveau DESC, experience DESC;";
	$requete = mysql_query($sql) or die (mysql_error());
	
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		$oJ = $objManager->GetPersoLogin($row['login']);
		
		$data[] = array('login'			=> $oJ->GetLogin(),
						'niveau'		=> $oJ->GetNiveau(),
						'experience'	=> $oJ->GetExpPerso(),
						'val_attaque'	=> $oJ->GetAttPerso(),
						'val_defense'	=> $oJ->GetDefPerso(),
						'vie'			=> $oJ->GetVie(),
						'nb_points'		=> $oJ->GetNbPoints(),
						'nb_victoire'	=> $oJ->GetNbVictoire(),
						'nb_vaincu'		=> $oJ->GetNbVaincu(),
						'clan'			=> $oJ->GetClan(),
						'nb_mort'		=> $oJ->GetNbMort(),
						'MaxExp'		=> $oJ->GetMaxExperience()
						);
		
	}
	
	return $data;
}
?>