<?php
function CombienQueteDisponible(personnage &$oJoueur){
	$sql = "SELECT * FROM table_quete_lst WHERE quete_niveau<=".$oJoueur->GetNiveau()." AND quete_civilisation IN ('Tous', '".$oJoueur->GetCivilisation()."');";
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);

	if(mysql_num_rows($requete) > 0){
		$NbQueteDisponible = 0;

		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if(!CheckIfQueteEnCours($row['id_quete']) AND !CheckIfQueteDejaTermine($row['id_quete'], $oJoueur->GetLogin())){
				$NbQueteDisponible++;
			}
		}

		return $NbQueteDisponible;
	}

	return 0;
}
function CheckIfQueteEnCours($NumQuete){
	if(isset($_SESSION['QueteEnCours']))
	{
		foreach($_SESSION['QueteEnCours'] as $quete)
		{
			if($quete->GetIDTypeQuete() == $NumQuete)
			{
				return true;
			}
		}
	}
	return false;
}
function CheckIfQueteDejaTermine($NumQuete, $login){
	$sql = "SELECT id_quete_en_cours FROM table_quetes WHERE quete_login = '$login' AND quete_reussi IS NOT NULL AND quete_id = $NumQuete;";
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);

	if(mysql_num_rows($requete) > 0)
	{
		return true;
	}
	return false;
}
?>