<?php
class qteObjet extends quete{
	
	Const COLOR			= '#8a8aff';
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $Quete, array $InfoQuete){
		date_default_timezone_set('Europe/Brussels');
	
		parent::Hydrate($Quete, $InfoQuete);
	
		$this->hydrate($Quete, $InfoQuete);
	
	}
	
	public function hydrate(array $Quete, array $InfoQuete){
		
	}

	public function ActionSurQuete(personnage &$oJoueur){
		if(is_null($this->CreateListObjectNeed()))
		{
			if(	$this->GetCoordonnee() == $oJoueur->GetCoordonnee())
			{
				$this->QueteAccomplie($oJoueur);
			}
		}
	}

	Public function ValiderQuete(personnage &$oJoueur, maison &$oMaison){
		if($oJoueur->CheckIfSurMaison())
		{
			if(CheckCout($this->CreateListObjectNeed(), $oJoueur, $oMaison))
			{
				foreach($this->CreateListObjectNeed() as $Objet)
				{
					UtilisationRessource(explode('=', $Objet), $oJoueur, $oMaison);
				}
				$this->QueteAccomplie($oJoueur);
			}
		}
	}
	Private function CheckCompleted(personnage &$oJoueur, maison &$oMaison){
		return CheckIfAssezRessource($arRessource, $oJoueur, $oMaison);
	}
	
	Private function QueteAccomplie(personnage &$oJoueur){
		$this->RecupereGains($oJoueur);
		
		$this->FinishQuete();
		
		if(	!is_null($this->CreateListObjectNeed())
			AND !is_null($this->GetTextFinish()))
		{
			$txt = $this->GetTextFinish();
		}else{
			$txt = 'Bravo! Vous avez trouvé "'.$this->GetNom().'"';
		}
		
		//$txt = 'Bravo! Vous avez trouvé "'.$this->GetNom();
		//.'" et gagné ';
		//.$this->GainOr.' '.AfficheIcone('or').', '.$this->GainExperience.' d\'expérience et '.$this->GainPoints.' points.';
		
		AddHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'quete', $this->GetNom(), NULL, $txt);
		
		$_SESSION['message']['quete'] = $txt;
	}
	
	//Les Affichages
	//==============
	
	
	//Les GETS
	//========
	
}
?>