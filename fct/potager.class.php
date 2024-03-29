<?php
class potager extends btProduction{
	
	const TYPE_COMPETENCE				= 'Agriculture';
	
	const COUT_AMELIORATION_NIVEAU_2	= 'BC=5,Sesterce=10,CUI=150';
	const COUT_AMELIORATION_NIVEAU_3	= 'Monnaie=1000,BC=1500,ResPierre=2';
	const COUT_AMELIORATION_NIVEAU_4	= 'Monnaie=150000';
	
	const PRIX_REPARATION				= 'Sesterce=15,PIE=50';
	
	const ID_BATIMENT					= 5;
	const STOCK_MAX_DEPART				= 500;
	
	const GAIN_TEMP_PAR_ESCLAVE			= 7;
	const DUREE_ESCLAVE					= 604800;
	
	const NB_ESCLAVES_NIV_1				= 2;
	const NB_ESCLAVES_NIV_2				= 4;
	const NB_ESCLAVES_NIV_3				= 8;
	const NB_ESCLAVES_NIV_4				= 12;
	
	const A1_CODE						= 'a1';
	const A1_NOM						= 'des L�gumes';
	const A1_NIVEAU_COMPETENCE			= 1;
	const A1_CODE_OBJET					= 'LEG';
	const A1_TEMP						= 3600;
	
	const A2_CODE						= 'a2';
	const A2_NOM						= 'du Miel';
	const A2_NIVEAU_COMPETENCE			= 2;
	const A2_CODE_OBJET					= 'MI';
	const A2_TEMP						= 3600;
	
	const A3_CODE						= 'a3';
	const A3_NOM						= 'du Raisin';
	const A3_NIVEAU_COMPETENCE			= 3;
	const A3_CODE_OBJET					= 'RAI';
	const A3_TEMP						= 3600;
	
	const A4_CODE						= 'a4';
	const A4_NOM						= 'des Epices';
	const A4_NIVEAU_COMPETENCE			= 4;
	const A4_CODE_OBJET					= 'EP';
	const A4_TEMP						= 3600;
	
	const B1_CODE						= 'b1';
	const B1_NOM						= 'du Gui';
	const B1_NIVEAU_COMPETENCE			= 1;
	const B1_CODE_OBJET					= 'G';
	const B1_TEMP						= 3600;
	
	const B2_CODE						= 'b2';
	const B2_NOM						= 'de l\'Absynthe';
	const B2_NIVEAU_COMPETENCE			= 2;
	const B2_CODE_OBJET					= 'ABS';
	const B2_TEMP						= 3600;
	
	const B3_CODE						= 'b3';
	const B3_NOM						= 'de la Verveine';
	const B3_NIVEAU_COMPETENCE			= 3;
	const B3_CODE_OBJET					= 'VER';
	const B3_TEMP						= 3600;
	
	const B4_CODE						= 'b4';
	const B4_NOM						= 'de la Petite Centaur�e';
	const B4_NIVEAU_COMPETENCE			= 4;
	const B4_CODE_OBJET					= 'PC';
	const B4_TEMP						= 3600;
	
	//--- fonction qui est lancer lors de la cr�ation de l'objet. ---
	public function __construct(array $carte = NULL, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($carte, $batiment);
		
	}
	
	//Quelle quantit� maximum on peut prendre maximum par action sur une ressource
	Protected function QuelleQuantite($CodeProduction){
		
		switch($CodeProduction)
		{
			case self::A1_CODE:
				switch($this->GetNiveau())
				{
					case 1: return 2;
					case 2: return 4;
					case 3: return 6;
					case 4: return 8;
				}
				break;
			case self::B1_CODE:
				switch($this->GetNiveau())
				{
					case 1: return 1;
					case 2: return 2;
					case 3: return 3;
					case 4: return 4;
				}
				break;
			case self::A2_CODE:
				switch($this->GetNiveau())
				{
					case 2: return 2;
					case 3: return 4;
					case 4: return 6;
				}
				break;
			case self::B2_CODE:
				switch($this->GetNiveau())
				{
					case 2: return 1;
					case 3: return 2;
					case 4: return 3;
				}
				break;
			case self::A3_CODE:
				switch($this->GetNiveau())
				{
					case 3: return 2;
					case 4: return 4;
				}
				break;
			case self::B3_CODE:
				switch($this->GetNiveau())
				{
					case 3: return 1;
					case 4: return 2;
				}
				break;
			case self::A4_CODE:
				switch($this->GetNiveau())
				{
					case 4: return 2;
				}
			case self::B4_CODE:
				switch($this->GetNiveau())
				{
					case 4: return 1;
				}
		}
		return 0;
	}
	
	
	//Les Affichages
	//==============
		
	
	//Les GETS
	//========
	protected function GetListSelectOptionProducion(){
		return 	($this->GetNiveau() >= self::A1_NIVEAU_COMPETENCE?'<option value="'.self::A1_CODE.'"'.($this->GetTypeContenu() == self::A1_CODE?' disabled="disabled"':'').'>'.self::A1_NOM.'</option>':'')
				.($this->GetNiveau() >= self::B1_NIVEAU_COMPETENCE?'<option value="'.self::B1_CODE.'"'.($this->GetTypeContenu() == self::B1_CODE?' disabled="disabled"':'').'>'.self::B1_NOM.'</option>':'')
				.($this->GetNiveau() >= self::A2_NIVEAU_COMPETENCE?'<option value="'.self::A2_CODE.'"'.($this->GetTypeContenu() == self::A2_CODE?' disabled="disabled"':'').'>'.self::A2_NOM.'</option>':'')
				.($this->GetNiveau() >= self::B2_NIVEAU_COMPETENCE?'<option value="'.self::B2_CODE.'"'.($this->GetTypeContenu() == self::B2_CODE?' disabled="disabled"':'').'>'.self::B2_NOM.'</option>':'')
				.($this->GetNiveau() >= self::A3_NIVEAU_COMPETENCE?'<option value="'.self::A3_CODE.'"'.($this->GetTypeContenu() == self::A3_CODE?' disabled="disabled"':'').'>'.self::A3_NOM.'</option>':'')
				.($this->GetNiveau() >= self::B3_NIVEAU_COMPETENCE?'<option value="'.self::B3_CODE.'"'.($this->GetTypeContenu() == self::B3_CODE?' disabled="disabled"':'').'>'.self::B3_NOM.'</option>':'')
				.($this->GetNiveau() >= self::A4_NIVEAU_COMPETENCE?'<option value="'.self::A4_CODE.'"'.($this->GetTypeContenu() == self::A4_CODE?' disabled="disabled"':'').'>'.self::A4_NOM.'</option>':'')
				.($this->GetNiveau() >= self::B4_NIVEAU_COMPETENCE?'<option value="'.self::B4_CODE.'"'.($this->GetTypeContenu() == self::B4_CODE?' disabled="disabled"':'').'>'.self::B4_NOM.'</option>':'');
	}
	Public function GetCodeRessource($type){
		switch($type){
			case self::A1_CODE:	return self::A1_CODE_OBJET;		break;
			case self::B1_CODE:	return self::B1_CODE_OBJET;		break;
			case self::A2_CODE:	return self::A2_CODE_OBJET;		break;
			case self::B2_CODE:	return self::B2_CODE_OBJET;		break;
			case self::A3_CODE:	return self::A3_CODE_OBJET;		break;
			case self::B3_CODE:	return self::B3_CODE_OBJET;		break;
			case self::A4_CODE:	return self::A4_CODE_OBJET;		break;
			case self::B4_CODE:	return self::B4_CODE_OBJET;		break;
		}
	}
	public function GetTempProduction($code){
		$Duree = 0;
		
		switch($code){
			case self::A1_CODE:	$Duree = self::A1_TEMP;	break;
			case self::B1_CODE:	$Duree = self::B1_TEMP;	break;
			case self::A2_CODE:	$Duree = self::A2_TEMP;	break;
			case self::B2_CODE:	$Duree = self::B2_TEMP;	break;
			case self::A3_CODE:	$Duree = self::A3_TEMP;	break;
			case self::B3_CODE:	$Duree = self::B3_TEMP;	break;
			case self::A4_CODE:	$Duree = self::A4_TEMP;	break;
			case self::B4_CODE:	$Duree = self::B4_TEMP;	break;
		}
		
		$Duree = $Duree * ((100 - (self::GAIN_TEMP_PAR_ESCLAVE * $this->GetNbEsclave())) / 100);
		
		return $Duree;
	}
	public function GetCoutReparation($facteur = 1){
		$temp = explode(',', self::PRIX_REPARATION);
		foreach($temp as $prix)
		{
			$arPrix = explode('=', $prix);
			$lstPrix[] = $arPrix[0].'='.($arPrix[1] * $facteur);
		}
		return $lstPrix;
	}
}

?>