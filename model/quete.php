<?php
function SelectQuete(personnage &$oJoueur, maison &$oMaison = NULL){
	$txt = null;
	$sql = "SELECT * 
			FROM table_quete_lst 
			WHERE quete_niveau<=".$oJoueur->GetNiveau()." AND quete_civilisation IN ('Tous', '".$oJoueur->GetCivilisation()."');";
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	
	if(mysql_num_rows($requete) > 0){
		$nbCol = 0;
		$NbQueteDisponible = 0;
		
		$txt .= '
		<table class="quetes">';
		
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			
			$oQuete = FoundQuete($row['id_quete'], $oJoueur->GetLogin());
			
			if(	!$oQuete->CheckIfEnCours($oJoueur->GetLogin())
				AND !$oQuete->CheckIfDejaTermine($oJoueur->GetLogin())
				AND (is_null($oQuete->GetCodeCmpQuete()) OR $oJoueur->CheckIfCompetenceAvailable($oQuete->GetCodeCmpQuete())))
			{
				$NbQueteDisponible++;
				
				
				
				if($nbCol == 0){
					$txt .= '
				<tr>';
				}
				
				$txt .= '
					<td>'.$oQuete->AfficheDescriptif($oJoueur, $oMaison, false).'</td>';
					
				$nbCol++;
				
				if($nbCol == 3){
					$txt .= '
				</tr>';
					$nbCol=0;
				}
			}
		}
	
		if($nbCol < 3 AND $nbCol > 0){
			$txt .= '
			</tr>';
		}
		$txt .= '
		</table>';
	
		if($NbQueteDisponible > 0){
			return $txt;
		}else{
			return '<p>Vous êtes inscrit à toutes les quêtes disponibles pour le moment.</p>';
		}
	}else{
		return '<p>Pas de nouvelle quête disponible. Passez au niveau suivant pour avoir de nouvelles quêtes.</p>';
	}
}
function ActionInscriptionQuete(&$check, $numQuete, personnage &$oJoueur, maison &$oMaison = NULL){
	if(	count($_SESSION['QueteEnCours']) < quete::NB_QUETE_MAX
		AND isset($_SESSION['quete'][$numQuete])
		AND !$_SESSION['quete'][$numQuete])
	{
		$oNewQuete = FoundQuete($numQuete);

			//on inscrit le joueur à la quete
		if($oNewQuete->Inscription($oJoueur, $oMaison))
		{
			
			global $objManager;
			$objManager->UpdateQuete($oNewQuete);
			
			unset($_SESSION['quete'][$numQuete]);
			
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionInscriptionQuete';
		}
		
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionInscriptionQuete';
	}
	
	
}
function ActionAnnulerQuete(&$check, $numQuete) {
	if(isset($_SESSION['quete'][$numQuete]) AND $_SESSION['quete'][$numQuete])
	{
		foreach($_SESSION['QueteEnCours'] as $oCloseQuete)
		{
			if($oCloseQuete->GetIDTypeQuete() == $numQuete)
			{
				$oCloseQuete->FinishQuete();
				
				global $objManager;
				$objManager->UpdateQuete($oCloseQuete);
			}
		}
		
		ListQueteEnCours();
		
		unset($_SESSION['quete'][$numQuete]);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionAnnulerQuete';
	}
	
}
function ActionValiderQuete(&$check, $numQuete, personnage &$oJoueur, maison &$oMaison = NULL){
	if(	isset($_SESSION['quete'][$numQuete])
	AND $_SESSION['quete'][$numQuete])
	{
		foreach($_SESSION['QueteEnCours'] as $oQuete)
		{
			if($oQuete->GetIDTypeQuete() == $numQuete)
			{
				$oQuete->ValiderQuete($oJoueur, $oMaison);
				
				global $objManager;
				$objManager->UpdateQuete($oQuete);
			}
		}
		
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ACtionValiderQuete';
	}
}


?>