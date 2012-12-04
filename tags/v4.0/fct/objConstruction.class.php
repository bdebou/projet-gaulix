<?php
class objConstruction extends objMain{

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
}