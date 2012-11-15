<?php
class Gibier extends objRessource{

	private $Attaque;
			
	Const TYPE_CMP_MAIN		= 'Chasse';
	
	// Initialisation de l'objet
	public function __construct(array $donnees){
		$this->hydrate($donnees);
	}
	
	//Remplir l'objet Gibier
	public function hydrate(array $donnees){
		parent::Hydrate($donnees, 1);
		
		foreach ($donnees as $key => $value){
			switch ($key){
				case 'objet_attaque':		$this->Attaque = (is_null($value)?NULL:intval($value)); break;
			}
		}
	}
	// -------------------- GET info ----------------------
	Public function GetAttaque(){
		return $this->Attaque;
	}
}
?>