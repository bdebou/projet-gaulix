<?php
class Gibier extends objRessource{
//class Gibier{

	private $Attaque;
	private $GainNourriture = null;
	private $GainCuir = null;
			
	Const TYPE_CMP_MAIN		= 'Chasse';
	Const TYPE_CMP_CUIR		= 'Travail du cuir';
	Const TYPE_CMP_NOUR		= 'Cuisine';
	
	const CMP_CHASSE_1		= 'cmpCha1';
	const CMP_CHASSE_2		= 'cmpCha2';
	const CMP_CHASSE_3		= 'cmpCha3';
	const CMP_CHASSE_4		= 'cmpCha4';
	
	Const CODE_GAIN_NOUR	= maison::TYPE_RES_NOURRITURE;
	Const CODE_GAIN_CUIR	= 5;
	
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
	public function GetCodeCompetenceRequise(){
		$Cout = $this->GetCoutFabrication();
		$tmp = explode('=', $Cout[0]);
		
		return $tmp[0];
	}
	Public function GetAttaque(){
		return $this->Attaque;
	}
}
?>