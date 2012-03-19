<?php
class Ressource{
	
	private $intIDCase,
			$strCoordonnee,
			$strCollecteur,
			$intStock,
			$dteDateAction,
			$bolVide,
			$intTypeContenu,
			$strNom,
			$strDescription;
	
	const TEMP_RESSOURCE			= 900;		//15 minutes pour collecter une ressource
	const COMPETENCE_POUR_PIERRE	= 'Mineur';
	const COMPETENCE_POUR_BOIS		= 'Bucheron';
	const COMPETENCE_POUR_OR		= 'Mineur';
	const TYPE_NORMAL				= 1;
	const TYPE_OR					= 2;
	const CMP_MAX					= 5;
	const NIVEAU_NORMAL				= 1;
	const NIVEAU_OR					= 3;
	
	// Initialisation de l'objet
	public function __construct(array $dataCarte, array $dataBatiment){
		$this->hydrate($dataCarte, $dataBatiment);
	}
	
	//Quelle quantité maximum on peut prendre maximum par action sur une ressource
	Private function QuelQuantiteMaxParAction($NivCmp, $id){
		if($this->strNom == 'Pierre' and $id == 2){
			$temp = 'Or';
		}else{
			$temp = $this->strNom;
		}
		
		switch($temp){
			case 'Pierre':
				switch($NivCmp){
					case 1: return 30;
					case 2: return 45;
					case 3: 
					case 4: 
					case 5: return 50;
				}
				break;
			case 'Bois':
				switch($NivCmp){
					case 1: return 50;
					case 2: return 60;
					case 3: return 70;
					case 4: return 80;
					case 5: return 100;
				}
				break;
			case 'Or':
				switch($NivCmp){
					case 1:
					case 2:
					case 3:	return 30;
					case 4:
					case 5: return 45;
				}
				break;
		}
	}
	
	//On démarre la collect de ressource
	Public Function StartCollect(personnage &$oCollecteur, $id){
		$this->strCollecteur = $oCollecteur->GetLogin();
		$this->dteDateAction = strtotime('now');
		if($this->strNom == 'Pierre'){
			$this->intTypeContenu = $id;
		}else{
			$this->intTypeContenu = NULL;
		}
	}
	
	//On libère la ressource si on quitte trop tot
	Public function FreeRessource(personnage &$oJoueur){
		if($this->strCollecteur == $oJoueur->GetLogin()){
			$this->strCollecteur = NULL;
			$this->dteDateAction = 0;
			$this->intTypeContenu = NULL;
		}
	}
	
	//On a finit de collecter la ressource
	Public Function FinishCollect(personnage &$oCollecteur, batiment &$oMaison){
		$qte = $this->GetQuantiteCollecte($oCollecteur->GetNiveauCompetence($this->GetCompetenceRequise()), $this->intTypeContenu);
		
		switch($this->strNom){
			case 'Pierre':
				switch($this->intTypeContenu){
					case 1:	$oMaison->AddPierre($qte);	break(2);
					case 2: $oCollecteur->AddOr($qte);	break(2);
				}
			case 'Bois':	$oMaison->AddBois($qte);	break;
			case 'Or':		$oCollecteur->AddOr($qte);	break;
		}
			
		if($oCollecteur->GetNiveauCompetence($this->GetCompetenceRequise()) < self::CMP_MAX){
			$this->intStock -= $qte;
			if($this->intStock <= 0){$this->bolVide = true;}
		}
		
		$this->strCollecteur = NULL;
		$this->dteDateAction = 0;
		$this->intTypeContenu = NULL;
	}
	
	//Remplir l'objet Ressource
	public function hydrate(array $dataCarte, array $dataBatiment){
		foreach ($dataBatiment as $key => $value){
			switch ($key){
				case 'batiment_nom':			$this->strNom = ucfirst(strtolower($value)); break;
				case 'batiment_description':	$this->strDescription = strval($value); break;
			}
		}
		foreach ($dataCarte as $key => $value){
			switch ($key){
				case 'id_case_carte':			$this->intIDCase = intval($value); break;
				case 'coordonnee':				$this->strCoordonnee = strval($value); break;
				case 'login':					$this->strCollecteur = (is_null($value)?NULL:strval($value)); break;
				case 'res_pierre':				if($this->strNom == 'Pierre'){$this->intStock = (is_null($value)?NULL:intval($value));} break;
				case 'res_bois':				if($this->strNom == 'Bois'){$this->intStock = (is_null($value)?NULL:intval($value));} break;
				case 'res_or':					if($this->strNom == 'Or'){$this->intStock = (is_null($value)?NULL:intval($value));} break;
				case 'date_action_batiment':	$this->dteDateAction = strtotime($value); break;
				case 'detruit':					$this->bolVide = (is_null($value)?false:true); break;
				case 'contenu_batiment':		$this->intTypeContenu = (is_null($value)?NULL:intval($value)); break;
			}
		}
		
	}
	// -------------------- GET info ----------------------
	public function GetQuantiteCollecte($NiveauCompetence, $type = self::TYPE_NORMAL){
		if($this->intStock >= $this->QuelQuantiteMaxParAction($NiveauCompetence, $type)){
			return $this->QuelQuantiteMaxParAction($NiveauCompetence, $type);
		}else{
			return $this->intStock;
		}
	}
	public function GetCollecteur(){					return $this->strCollecteur;}
	public function GetNom($type = self::TYPE_NORMAL){
		if($this->strNom == 'Pierre' AND ($type == self::TYPE_OR OR $this->intTypeContenu == self::TYPE_OR)){
			return 'Or';
		}else{
			return $this->strNom;
		}
	}
	public function GetStock(){							return $this->intStock;}
	public function GetTempRessource(){					return self::TEMP_RESSOURCE;}
	public function GetCoordonnee(){					return $this->strCoordonnee;}
	public function GetVide(){							return $this->bolVide;}
	public function GetDateDebutAction(){				return $this->dteDateAction;}
	public function GetTypeContenu(){					return $this->intTypeContenu;}
	public function GetCompetenceRequise(){
		switch($this->strNom){
			case 'Bois':	return self::COMPETENCE_POUR_BOIS;
			case 'Pierre':	return self::COMPETENCE_POUR_PIERRE;
			case 'Or':		return self::COMPETENCE_POUR_OR;
		}
	}
	public function GetStatus(){
		
	}
}
?>