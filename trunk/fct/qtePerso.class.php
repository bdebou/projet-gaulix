<?php
class qtePerso extends quete{
	
	Const COLOR			= '#82ff82';
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $Quete, array $InfoQuete){
		date_default_timezone_set('Europe/Brussels');
	
		parent::Hydrate($Quete, $InfoQuete);
	
		$this->hydrate($Quete, $InfoQuete);
	
	}
	
	public function hydrate(array $Quete, array $InfoQuete){
		
	}
	
	public function ActionSurQuete(personnage &$oJoueur){
		if(	$this->GetCoordonnee() == $oJoueur->GetCoordonnee())
		{
			$this->QueteAccomplie($oJoueur);
		}
	}
	
	private function QueteAccomplie(personnage &$oJoueur){
		$this->RecupereGains($oJoueur);
		
		$this->FinishQuete();
		
		if(!is_null($this->GetTextFinish()))
		{
			$txt = $this->GetTextFinish();
		}else{
			$txt = "Bravo, vous l\'avez retrouvé. Il va beaucoup mieux. Merci";
		}
		
		AddHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'quete', $this->GetNom(), NULL, $txt);
		
		$_SESSION['message']['quete'] = $txt;
	}
	
	//Les Affichages
	//==============
	
	
	//Les GETS
	//========
	
}
?>