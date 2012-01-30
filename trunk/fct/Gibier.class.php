<?php
class Gibier{

	private	$ID,
			$Code,
			$Nom,
			$Categorie,
			$Attaque,
			$GainNourriture = null,
			$GainCuir = null;
			
	// Initialisation de l'objet
	public function __construct(array $donnees){
		$this->hydrate($donnees);
	}
	
	//Remplir l'objet Gibier
	public function hydrate(array $donnees){
		foreach ($donnees as $key => $value){
			switch ($key){
				case 'id_objet':			$this->ID = intval($value); break;
				case 'objet_code':			$this->Code = strval($value); break;
				case 'objet_nom':			$this->Nom = strval($value); break;
				case 'objet_niveau':		$this->Categorie = intval($value); break;
				case 'objet_attaque':		$this->Attaque = (is_null($value)?NULL:intval($value)); break;
				case 'objet_ressource':
					if(!is_null($value)){
						$arTmp = explode(',', $value);
						foreach($arTmp as $gain){
							$arTmpG = explode('=', $gain);
							switch($arTmpG[0]){
								case 'ResNou' :		$this->GainNourriture = $arTmpG[1];	break;
								case 'ResCuir' :	$this->GainCuir = $arTmpG[1];			break;
							}
						}
					}
					break;
			}
		}
	}
	// -------------------- GET info ----------------------
	public function GetNiveau(){
		switch($this->Categorie){
			case 1: return 'petit';
			case 2: return 'moyen';
			case 3: return 'grand';
		}
	}
	Public function GetAttaque(){
		return $this->Attaque;
	}
	Public function GetGainNourriture($NiveauBoucher){
		if(is_null($NiveauBoucher)){
			return $this->GainNourriture;
		}else{
			switch($NiveauBoucher){
				case 1:	return (3 * $this->GainNourriture);
				case 2:	return (6 * $this->GainNourriture);
				case 3:	return (10 * $this->GainNourriture);
			}
		}
	}
	Public function GetGainCuir($NiveauTanneur){
		if(is_null($NiveauTanneur)){
			return NULL;
		}else{
			switch($NiveauTanneur){
				case 1: return $this->GainCuir;
				case 2:	return (2 * $this->GainCuir);
				case 3:	return (3 * $this->GainCuir);
				case 4:	return (4 * $this->GainCuir);
			}
		}
	}
	Public function GetNom(){
		return $this->Nom;
	}
	public function GetAfficheGainChasse(&$oJoueur){
	$chk = false;
	$txt = $this->GetGainNourriture($oJoueur->GetNiveauCompetence('Boucher')).AfficheIcone('nourriture');
	if(!is_null($this->GetGainCuir($oJoueur->GetNiveauCompetence('Tanneur')))){$txt .= ' et '.$this->GetGainCuir($oJoueur->GetNiveauCompetence('Tanneur')).AfficheIcone('ResCuir');}
	return $txt;
	
}
}
?>