<?php
function CombienQueteDisponible(personnage &$oJoueur){
	$sql = "SELECT * FROM table_quete_lst WHERE quete_niveau<=".$oJoueur->GetNiveau()." AND quete_civilisation IN ('Tous', '".$oJoueur->GetCivilisation()."');";
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);

	if(mysql_num_rows($requete) > 0){
		$NbQueteDisponible = 0;

		
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			
			$oQuete = FoundQuete($row['id_quete'], $oJoueur->GetLogin());
			
			if(	!$oQuete->CheckIfEnCours($oJoueur->GetLogin())
				AND !$oQuete->CheckIfDejaTermine($oJoueur->GetLogin())
				AND (is_null($oQuete->GetCodeCmpQuete()) OR $oJoueur->CheckIfCompetenceAvailable($oQuete->GetCodeCmpQuete())))
			{
				$NbQueteDisponible++;
			}
		}

		return $NbQueteDisponible;
	}

	return 0;
}

?>