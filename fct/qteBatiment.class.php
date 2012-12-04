<?php
class qteBatiment extends quete{
	
	Const COLOR			= '#82ff82';
	
	Const TYPE_QUETE_BATIMENT	= 'Batiment';
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $Quete, array $InfoQuete){
		date_default_timezone_set('Europe/Brussels');
	
		parent::Hydrate($Quete, $InfoQuete);
	
		$this->hydrate($Quete, $InfoQuete);
	
	}
	
	public function hydrate(array $Quete, array $InfoQuete){
		
	}
	
	public function Inscription(personnage &$oJoueur, maison &$oMaison){
		
		if(parent::Inscription($oJoueur, $oMaison))
		{
			foreach($this->Cout as $Cout)
			{
				$tmpCout = explode('=', $Cout);
		
				switch(QuelTypeObjet($tmpCout[0])){
					case qteBatiment::TYPE_QUETE_BATIMENT:
						$this->Status[$tmpCout[1]] = 0;
						break;
				}
		
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
	
	public function SetStatus(){
		
	
		if(isset($lstBatiments))
		{
			return $lstBatiments;
		}
	
		return NULL;
	}
	
	Public function CheckCompleted(personnage &$oJoueur, maison &$oMaison){
		CheckIfAssezRessource($arRessource, $oJoueur, $oMaison);
		
		return false;
	}
	
	//Les Affichages
	//==============
	
	
	//Les GETS
	//========
	
}
?>