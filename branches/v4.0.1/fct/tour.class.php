<?php
class tour extends batiment{
	
	//Les points
	const POINT_TOUR_ATTAQUE		= 2;
	const ID_BATIMENT				= 3;
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $carte, array $batiment){
		$this->Hydrate($carte, $batiment);
	}
	public function Hydrate(array $carte, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($carte, $batiment);
	}
}
?>
