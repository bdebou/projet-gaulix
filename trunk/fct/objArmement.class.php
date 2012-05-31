<?php
class objArmement extends objMain{

	private $Attaque;
	private $Defense;
	private $Distance;
	
	const TYPE_ATTAQUE		= 'attaque';
	const TYPE_DEFENSE		= 'defense';
	const TYPE_DISTANCE		= 'distance';
	
	const TYPE_BOUCLIER		= 'bouclier';
	const TYPE_JAMBIERE		= 'jambiere';
	const TYPE_ARME			= 'arme';
	const TYPE_CASQUE		= 'casque';
	const TYPE_CUIRASSE		= 'cuirasse';
	
	public function __construct(array $data, $nb){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($data, $nb);
		
		foreach($data as $key=>$value)
		{
			switch($key)
			{
				case 'objet_attaque':		$this->Attaque			= (is_null($value)?0:intval($value)); break;
				case 'objet_defense':		$this->Defense			= (is_null($value)?0:intval($value)); break;
				case 'objet_distance':		$this->Distance			= (is_null($value)?0:intval($value)); break;
				
			}
		}
	}
	
	//Les GETS
	//========
	Public function GetType(){
		switch(substr($this->GetCode(), 0, 3))
		{
			case 'Bcl':	return self::TYPE_BOUCLIER;
			case 'Arm':	return self::TYPE_ARME;
			case 'Crs':	return self::TYPE_CUIRASSE;
			case 'Csq':	return self::TYPE_CASQUE;
			case 'Jbr':	return self::TYPE_JAMBIERE;
		}
		return NULL;
	}
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