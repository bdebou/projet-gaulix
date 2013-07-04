<?php
class objEnnemi extends objMain{

	private $Attaque;
	private $Defense;
	private $Distance;
	
	public function __construct(array $data, $nb){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($data, $nb);
		
		foreach($data as $key=>$value)
		{
			switch($key)
			{
				case 'objet_attaque':		$this->Attaque			= (is_null($value)?0:intval($value));		break;
				case 'objet_defense':		$this->Defense			= (is_null($value)?0:intval($value));		break;
				case 'objet_distance':		$this->Distance			= (is_null($value)?0:intval($value));		break;
			}
		}
	}
	
	//Les Affichages
	//==============
	
	//Les GETS
	//========
	public function GetAttaque(){
		return $this->Attaque;
	}
	public function GetDefense(){
		return $this->Defense;
	}
	public function GetDistance(){
		return $this->Distance;
	}

}