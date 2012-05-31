<?php
class objRessource extends objMain{

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
			if($arTmp[0] == $Type)
			{
				return (int)$arTmp[1];
			}
		}
		return NULL;
	}
}