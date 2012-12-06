<?php
class qteCmp extends quete{
	
	Const COLOR			= '#82ff82';
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $Quete, array $InfoQuete){
		date_default_timezone_set('Europe/Brussels');
	
		parent::Hydrate($Quete, $InfoQuete);
	
		$this->hydrate($Quete, $InfoQuete);
	
	}
	
	public function hydrate(array $Quete, array $InfoQuete){
		
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