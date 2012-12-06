<?php
class objDivers extends objMain{
	private $Ressource;

	const TYPE_RES_DEP		= 'Deplacement';
	const TYPE_RES_VIE		= 'Vie';
	
	const TYPE_SORT			= 'sort';
	const TYPE_LIVRE		= 'Livre';
	const TYPE_SAC			= 'Sac';
	
	public function __construct(array $data, $nb){
		date_default_timezone_set('Europe/Brussels');
	
		parent::Hydrate($data, $nb);
		
		foreach($data as $key=>$value)
		{
			switch($key)
			{
				
			}
		}
	}
	
	//Les GETS
	//========
	public function GetNb($Type){
		
		foreach(parent::GetRessource() as $tmpRessource)
		{
			$arTmp = explode('=', $tmpRessource);
			if($arTmp[0] == 'Res'.substr($Type, 0, 3))
			{
				return (int)$arTmp[1];
			}
		}
		return NULL;
	}
	public function GetType(){
		switch(substr($this->GetCode(), 0, 3))
		{
			case 'Srt':	return self::TYPE_SORT;
			case 'Lvr': return self::TYPE_LIVRE;
			case 'Sac':	return self::TYPE_SAC;
		}
		return NULL;
	}
}