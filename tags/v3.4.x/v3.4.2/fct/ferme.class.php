<?php
class ferme extends batiment{
	private $Contenu,
			$DateAction;
	
	const TYPE_COMPETENCE				= 'Agriculture';
	
	const CODE_PRODUCTION_NOURRITURE	= 0;
	const NIVEAU_COMPETENCE_NOURRITUE	= 0;
	const ICONE_NAME_NOURRITURE			= 'nourriture';
	const TEMP_CULTURE_NOURRITURE		= 1800;
	
	
	const CODE_PRODUCTION_COTTON		= 1;
	const NIVEAU_COMPETENCE_COTTON		= 2;
	const ICONE_NAME_COTTON				= 'ResFibC';
	const TEMP_CULTURE_COTTON			= 2400;
	
	const CODE_PRODUCTION_MIEL			= 2;
	const NIVEAU_COMPETENCE_MIEL		= 3;
	const ICONE_NAME_MIEL				= 'ResMiel';
	const TEMP_CULTURE_MIEL				= 3600;
	
	//--- fonction qui est lancer lors de la cr�ation de l'objet. ---
	public function __construct(array $carte, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($carte, $batiment);
		
		foreach ($carte as $key => $value){
			switch ($key){
				case 'date_action_batiment':
					$this->DateAction = (is_null($value)?NULL:strtotime($value));
					break;
				case 'contenu_batiment':
					if(is_null($value)){
						$this->Contenu = NULL;
					}else{
						$this->Contenu = $value;
					}
					break;
			}
		}
		
			//on stock ce qui a �t� produit
		if(	$this->GetStockContenu() < $this->GetStockMax()
			AND (strtotime('now') - parent::GetDateAction()) > $this->GetTempCulture($this->GetTypeContenu())){
			
			global $objManager;
			$Producteur = $objManager->GetPersoLogin(parent::GetLogin());
			
			$this->AddStock($Producteur->GetNiveauCompetence(self::TYPE_COMPETENCE));
			
			unset($Producteur);
		}
	}
	
	//Quelle quantit� maximum on peut prendre maximum par action sur une ressource
	Private function QuelleQuantite($NiveauCompetence, $CodeProduction){
		
		switch($CodeProduction){
			case self::CODE_PRODUCTION_NOURRITURE:
				switch($NiveauCompetence){
					case 0: return 10;
					case 1: return 20;
					case 2:
					case 3: return 30;
					default: return 0;
				}
				break;
			case self::CODE_PRODUCTION_COTTON:
				switch($NiveauCompetence){
					case 2: return 10;
					case 3: return 20;
					default: return 0;
				}
				break;
			case self::CODE_PRODUCTION_MIEL:
				switch($NiveauCompetence){
					case 3: return 10;
					default: return 0;
				}
				break;
		}
	}
	
	//--- Gestion du stock  ---
	public function ViderStock($stock, maison &$maison, personnage &$joueur){
		$arContenu = explode(',', $this->Contenu);
		
		switch($arContenu['0']){
			case self::CODE_PRODUCTION_NOURRITURE:
				$maison->AddNourriture($stock);
				break;
			case self::CODE_PRODUCTION_MIEL:
				$joueur->AddInventaire('ResMiel', NULL, $stock, false);
				break;
			case self::CODE_PRODUCTION_COTTON:
				$joueur->AddInventaire('ResFibC', NULL, $stock, false);
				break;
		}
		
		$this->Contenu = $arContenu['0'].','.($arContenu['1'] - $stock);
		if($this->GetStockMax() == $stock){$this->DateAction = strtotime('now');}
	}
	public function AddStock($NiveauCompetence){
		$arContenu = explode(',', $this->Contenu);
		
		$nb = intval((strtotime('now') - parent::GetDateAction()) / $this->GetTempCulture($arContenu[0]));
		
		for($i=1;$i<=$nb;$i++){
			if($this->GetStockMax() > $arContenu[1]){
				$arContenu[1] += $this->QuelleQuantite($NiveauCompetence, $arContenu[0]);
			}else{break;}
		}
		
		$this->Contenu = $arContenu[0].','.($arContenu[1] > $this->GetStockMax()?$this->GetStockMax():$arContenu[1]);
		$this->DateAction = strtotime('now');
	}
	//--- On change de type de production ---
	public function ChangerProductionBatiment($production){
		$arContenu = explode(',', $this->Contenu);
		
		$this->Contenu = $production.','.$arContenu[1];
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
							.AfficheCompteurTemp(ucfirst(strtolower(get_class($this))), 'index.php?page=village', ($this->GetTempCulture($stock[0]) - (strtotime('now') - parent::GetDateAction())));
		}else{
			$status = '<p>Votre stock est plein.</p>';
		}
		
		$_SESSION['main'][get_class($this)]['production']	= $stock[0];
		$_SESSION['main'][get_class($this)]['vider']		= $stock[1];
		
		$PositionBatiment	= implode(',', array_merge(array(parent::GetCarte()), parent::GetCoordonnee()));
		$PositionJoueur		= implode(',', array_merge(array($oJoueur->GetCarte()), $oJoueur->GetPosition()));
		
		if($PositionBatiment == $PositionJoueur){
			$txtAction = '
				<td>
					<a href="index.php?page=village&action=viderstock'.strtolower(get_class($this)).'&amp;anchor='.implode('_', array_merge(array(parent::GetCarte()), parent::GetCoordonnee())).'">Vider votre stock</a>
				</td>
				<td>'
					.'<form method="get" action="index.php" class="production">'
						.'<input type="hidden" name="page" value="village" />'
						.'<input type="hidden" name="anchor" value="'.implode('_', array_merge(array(parent::GetCarte()), parent::GetCoordonnee())).'" />'
						.'<input type="hidden" name="action" value="production'.strtolower(get_class($this)).'" />'
						.'<select name="type" onclick="document.getElementById(\'BtSubmit\').disabled=false;">'
							.'<option value="'.self::CODE_PRODUCTION_NOURRITURE.'"'.(($stock[0] == self::CODE_PRODUCTION_NOURRITURE)?' disabled="disabled"':'').'>Produire de la Nourriture</option>'
							.'<option value="'.self::CODE_PRODUCTION_COTTON.'"'.(($stock[0] == self::CODE_PRODUCTION_COTTON OR $oJoueur->GetNiveauCompetence(self::TYPE_COMPETENCE) < self::NIVEAU_COMPETENCE_COTTON)?' disabled="disabled"':'').'>Produire du Cotton</option>'
							.'<option value="'.self::CODE_PRODUCTION_MIEL.'"'.(($stock[0] == self::CODE_PRODUCTION_MIEL OR $oJoueur->GetNiveauCompetence(self::TYPE_COMPETENCE) < self::NIVEAU_COMPETENCE_MIEL)?' disabled="disabled"':'').'>Produire du Miel</option>'
						.'</select>'
						.'<input type="submit" value="Go" id="BtSubmit" disabled="disabled" />'
					.'</form>'
				.'</td>';
		}else{
			$txtAction = '
				<td colspan="2">Vous ne pouvez rien ex�cuter car vous n\'�tes pas � votre '.strtolower(get_class($this)).'.</td>';
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
	public function GetDateAction(){			return $this->DateAction;}
	Public function GetIconeNameProduction($type){
		switch($type){
			case self::CODE_PRODUCTION_NOURRITURE:	return self::ICONE_NAME_NOURRITURE;
			case self::CODE_PRODUCTION_MIEL:		return self::ICONE_NAME_MIEL;
			case self::CODE_PRODUCTION_COTTON:		return self::ICONE_NAME_COTTON;
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
	public function GetTempCulture($code){
		switch($code){
			case self::CODE_PRODUCTION_NOURRITURE:	return self::TEMP_CULTURE_NOURRITURE;
			case self::CODE_PRODUCTION_MIEL:		return self::TEMP_CULTURE_MIEL;
			case self::CODE_PRODUCTION_COTTON:		return self::TEMP_CULTURE_COTTON;
		}
	}
}

?>