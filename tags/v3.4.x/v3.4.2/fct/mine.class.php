<?php
class mine extends batiment{
	private $Contenu,
			$DateAction;
	
	const TYPE_COMPETENCE			= 'Mineur';
	
	const CODE_PRODUCTION_PIERRE	= 0;
	const ICONE_NAME_PIERRE			= 'pierre';
	const TEMP_PIERRE				= 1800;
	
	const CODE_PRODUCTION_OR		= 1;
	const NIVEAU_COMPETENCE_OR		= 3;
	const ICONE_NAME_OR				= 'or';
	const TEMP_OR					= 2400;
	
	const CODE_PRODUCTION_FER		= 2;
	const NIVEAU_COMPETENCE_FER		= 4;
	const ICONE_NAME_FER			= 'ResMinF';
	const TEMP_FER					= 3000;
	
	const CODE_PRODUCTION_CUIVRE	= 3;
	const NIVEAU_COMPETENCE_CUIVRE	= 5;
	const ICONE_NAME_CUIVRE			= 'ResMinC';
	const TEMP_CUIVRE				= 3600;
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $carte, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($carte, $batiment);
		
		foreach ($carte as $key => $value){
			switch ($key){
				case 'contenu_batiment':
					if(is_null($value)){
						$this->Contenu = NULL;
					}else{
						$this->Contenu = $value;
					}
					break;
				case 'date_action_batiment':
					$this->DateAction = (is_null($value)?NULL:strtotime($value));
					break;
			}
		}
		
			//on stock ce qui a été produit
		if(	$this->GetStockContenu() < $this->GetStockMax()
			AND (strtotime('now') - parent::GetDateAction()) > $this->GetTempExtraction($this->GetTypeContenu())){
				
			global $objManager;
			$Producteur = $objManager->GetPersoLogin(parent::GetLogin());
				
			$this->AddStock($Producteur->GetNiveauCompetence(self::TYPE_COMPETENCE));
				
			unset($Producteur);
		}
	}
	
	//Quelle quantité maximum on peut prendre maximum par action sur une ressource
	Private function QuelleQuantite($NiveauCompetence, $CodeProduction){
	
		switch($CodeProduction){
			case self::CODE_PRODUCTION_PIERRE:
				switch($NiveauCompetence){
					case 1: return 10;
					case 2: return 20;
					case 3: return 30;
					case 4: return 40;
					case 5: return 50;
					default: return 0;
				}
				break;
			case self::CODE_PRODUCTION_OR:
				switch($NiveauCompetence){
					case 3: return 10;
					case 4: return 20;
					case 5: return 30;
					default: return 0;
				}
				break;
			case self::CODE_PRODUCTION_FER:
				switch($NiveauCompetence){
					case 4: return 10;
					case 5: return 20;
					default: return 0;
				}
				break;
			case self::CODE_PRODUCTION_CUIVRE:
				switch($NiveauCompetence){
					case 5: return 10;
					default: return 0;
				}
				break;
		}
	}
	
	//--- Gestion du stock  ---
	public function ViderStock($stock, maison &$maison, personnage &$joueur){
		$arContenu = explode(',', $this->Contenu);
		switch(intval($arContenu[0])){
			case self::CODE_PRODUCTION_PIERRE:
				$maison->AddPierre($stock);
				break;
			case self::CODE_PRODUCTION_OR:
				$joueur->AddOr($stock);
				break;
			case self::CODE_PRODUCTION_FER:
				$joueur->AddInventaire(self::ICONE_NAME_FER, NULL, $stock, false);
				break;
			case self::CODE_PRODUCTION_CUIVRE:
				$joueur->AddInventaire(self::ICONE_NAME_CUIVRE, NULL, $stock, false);
				break;
		}

		$this->Contenu = $arContenu[0].','.($arContenu[1] - $stock);
		if($this->GetStockMax() == $stock){$this->DateAction = strtotime('now');}
	}
	public function AddStock($NiveauCompetence){
		$arContenu = explode(',', $this->Contenu);
		
		$nb = intval((strtotime('now') - parent::GetDateAction()) / $this->GetTempExtraction($arContenu[0]));
		
		for($i=1;$i<=$nb;$i++){
			if($this->GetStockMax() > $arContenu['1']){
				$arContenu[1] += $this->QuelleQuantite($NiveauCompetence, $arContenu[0]);
			}else{break;}
		}
		
		$this->Contenu = $arContenu[0].','.($arContenu[1] > $this->GetStockMax()?$this->GetStockMax():$arContenu[1]);
		$this->DateAction = strtotime('now');
	}
	//--- On change de type de production ---
	public function ChangerProductionBatiment($production){
		$arContenu = explode(',', $this->Contenu);
		
		$this->Contenu = $production.','.$arContenu['1'];
		$this->DateAction = strtotime('now');
	}
	
	//Les Affichages
	//==============
	public function AfficheContenu(personnage &$oJoueur){//OK
		$stock = explode(',', $this->Contenu);

		if($stock[1] < $this->GetStockMax()){
			$_SESSION['main'][get_class($this)]['stock'] = 1;
			$status = '
							<div style="display:inline;" id="TimeToWait'.ucfirst(strtolower(get_class($this))).'"></div>'
							.AfficheCompteurTemp(ucfirst(strtolower(get_class($this))), 'index.php?page=villag', ($this->GetTempExtraction($stock[0]) - (strtotime('now') - parent::GetDateAction())));
		}else{
			$status = '<p>Votre stock est plein.</p>';
		}
		
		$_SESSION['main'][get_class($this)]['production']	= $stock[0];
		$_SESSION['main'][get_class($this)]['vider']		= $stock[1];
		
		$PositionBatiment	= implode(',', array_merge(array(parent::GetCarte()),parent::GetCoordonnee()));
		$PositionJoueur		= implode(',', array_merge(array($oJoueur->GetCarte()),$oJoueur->GetPosition()));
		
		if($PositionBatiment == $PositionJoueur){
			$txtAction = '
				<td>
					<a href="index.php?page=village&amp;action=viderstock'.strtolower(get_class($this)).'&amp;anchor='.implode('_', array_merge(array(parent::GetCarte()), parent::GetCoordonnee())).'">Vider votre stock</a>
				</td>
				<td>'
					.'<form method="get" action="index.php" class="production">'
						.'<input type="hidden" name="page" value="village" />'
						.'<input type="hidden" name="anchor" value="'.implode('_', array_merge(array(parent::GetCarte()), parent::GetCoordonnee())).'" />'
						.'<input type="hidden" name="action" value="production'.strtolower(get_class($this)).'" />'
						.'<select name="type" onclick="document.getElementById(\'BtSubmit\').disabled=false;">'
							.'<option value="'.self::CODE_PRODUCTION_PIERRE.'"'.(($stock[0] == self::CODE_PRODUCTION_PIERRE)?' disabled="disabled"':'').'>Trouver des Pierres</option>'
							.'<option value="'.self::CODE_PRODUCTION_OR.'"'.(($stock[0] == self::CODE_PRODUCTION_OR OR $oJoueur->GetNiveauCompetence(self::TYPE_COMPETENCE) < 2)?' disabled="disabled"':'').'>Trouver de l\'Or</option>'
							.'<option value="'.self::CODE_PRODUCTION_FER.'"'.(($stock[0] == self::CODE_PRODUCTION_FER OR $oJoueur->GetNiveauCompetence(self::TYPE_COMPETENCE) < 3)?' disabled="disabled"':'').'>Trouver du Minerai de Fer</option>'
							.'<option value="'.self::CODE_PRODUCTION_CUIVRE.'"'.(($stock[0] == self::CODE_PRODUCTION_CUIVRE OR $oJoueur->GetNiveauCompetence(self::TYPE_COMPETENCE) < 4)?' disabled="disabled"':'').'>Trouver du Minerai de Cuivre</option>'
						.'</select>'
						.'<input type="submit" value="Go" id="BtSubmit" disabled="disabled" />'
					.'</form>'
				.'</td>';
		}else{
			$txtAction = '
				<td colspan="2">Vous ne pouvez rien exécuter car vous n\'êtes pas à votre '.strtolower(get_class($this)).'.</td>';
		}
		
		$txt ='
		<table border style="margin:3px;">
			<tr>
				<td style="width:60%;">Production de '.$this->QuelleQuantite($oJoueur->GetNiveauCompetence(self::TYPE_COMPETENCE), $stock[0]).'x '.AfficheIcone($this->GetIconeNameProduction($stock[0])).'</td>
				<td>'.$status.'</td>
			</tr>
			<tr>
				<td>Stock</td>
				<td>'.$stock[1].'/'.$this->GetStockMax().' '.AfficheIcone($this->GetIconeNameProduction($stock[0])).'</td>
			</tr>
			<tr>
				'.$txtAction.'
			</tr>
		</table>';
		
		return $txt;
	}
	
	//Les GETS
	//========
	public function GetStockMax(){				return 500 + (parent::GetNiveau() * 100);}
	public function GetContenu(){				return $this->Contenu;}
	Public function GetIconeNameProduction($type){
		switch($type){
			case self::CODE_PRODUCTION_PIERRE:	return self::ICONE_NAME_PIERRE;
			case self::CODE_PRODUCTION_OR:		return self::ICONE_NAME_OR;
			case self::CODE_PRODUCTION_FER:		return self::ICONE_NAME_FER;
			case self::CODE_PRODUCTION_CUIVRE:	return self::ICONE_NAME_CUIVRE;
		}
	}
	public function GetTypeContenu(){
		$contenu = explode(',', $this->Contenu);
		return $contenu[0];
	}
	public function GetStockContenu(){
		$contenu = explode(',', $this->Contenu);
		return $contenu[1];
	}
	public function GetDateAction(){			return $this->DateAction;}
	public function GetTempExtraction($code){
		switch($code){
			case self::CODE_PRODUCTION_PIERRE:	return self::TEMP_PIERRE;
			case self::CODE_PRODUCTION_OR:		return self::TEMP_OR;
			case self::CODE_PRODUCTION_FER:		return self::TEMP_FER;
			case self::CODE_PRODUCTION_CUIVRE:	return self::TEMP_CUIVRE;
		}
	}
	
}
?>