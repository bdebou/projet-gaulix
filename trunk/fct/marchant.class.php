<?php
class marchant{
	private $Etalage,
			$LstContenu;
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $data){
		$this->Hydrate($data);
	}
	
	//--- on ajoute une marchandise ---
	public function AddMarchandise($codeObjet, $nbObjet=1){
		//$this->Contenu[]=$numObjet;
		
		$chk = false;
		//$lstBri = array('sac', 'arme', 'bouclier', 'jambiere', 'casque', 'cuirasse');
				
		//la structure est type1=nb1,type2=nb2 (exemple : cuir=6,longbaton=3)
		if(!is_null($this->LstContenu)){
			foreach($this->LstContenu as $key=>$element){
				$arTemp = explode('=', $element);
				if(	$arTemp['0'] == $codeObjet){
					$arTemp['1']+=$nbObjet;
					$this->LstContenu[$key] = implode('=', $arTemp);
					$chk = true;
					break;
				}
			}
		}
		if(!$chk){$this->LstContenu[] = $codeObjet.'='.$nbObjet;}
	}
	
	//--- On a vendu une marchandise ---
	public function RemoveMarchandise($codeObject){
		$chk = true;
		$temp = null;
		foreach($this->LstContenu as $objet){
			$arObjet = explode('=', $objet);
			if($arObjet['0']==$codeObject and $chk){
				$arObjet['1']--;
				if($arObjet['1'] > 0){$temp[] = implode('=', $arObjet);}
				$chk = false;
			}else{
				$temp[] = implode('=', $arObjet);
			}
		}
		$this->LstContenu=$temp;
	}
	
	//--- on rempli l'objet avec les valeurs correspondant. ---
	private function Hydrate(array $data){
		date_default_timezone_set('Europe/Brussels');
		
		foreach ($data as $key => $value){
			switch ($key){
				case 'contenu_vendeur':			$this->LstContenu = (is_null($value)?NULL:explode(',', $value)); break;
			}
		}
	}
	
	//--- Renvoie de valeur ---
	public function GetLstContenu(){	return $this->LstContenu;}
}
?>