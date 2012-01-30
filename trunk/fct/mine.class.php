<?php
class mine extends batiment{
	private $Contenu,
			$DateAction;
	
	const TYPE						= 'mine';
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
		$this->Hydrate($carte, $batiment);
	}
	public function Hydrate(array $carte, array $batiment){
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
	public function AddStock(personnage &$joueur){
		$arContenu = explode(',', $this->Contenu);
		
		$nb = intval((strtotime('now') - parent::GetDateAction()) / $this->GetTempExtraction($arContenu[0]));
		
		for($i=1;$i<=$nb;$i++){
			if($this->GetStockMax() > $arContenu['1']){
				$arContenu[1] += $this->QuelleQuantite($joueur->GetNiveauCompetence(self::TYPE_COMPETENCE), $arContenu[0]);
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
	public function AfficheContenu(&$PageVillage, personnage &$oJoueur){//OK
		$stock = explode(',', $this->Contenu);
		
		switch($stock[0]){
			case self::CODE_PRODUCTION_PIERRE:	$IconeName = self::ICONE_NAME_PIERRE;	break;
			case self::CODE_PRODUCTION_OR:		$IconeName = self::ICONE_NAME_OR;		break;
			case self::CODE_PRODUCTION_FER:		$IconeName = self::ICONE_NAME_FER;		break;
			case self::CODE_PRODUCTION_CUIVRE:	$IconeName = self::ICONE_NAME_CUIVRE;	break;
		}

		if($stock[1] < $this->GetStockMax()){
			if((strtotime('now') - parent::GetDateAction()) > $this->GetTempExtraction($stock[0])){
				$_SESSION['main'][self::TYPE]['stock'] = intval((strtotime('now') - parent::GetDateAction()) / $this->GetTempExtraction($stock[0]));
				return '<script language="javascript">window.location=\'./fct/main.php?action=stocker'.strtolower(self::TYPE).'\';</script>';
			}else{
				$_SESSION['main'][self::TYPE]['stock'] = 1;
				$status = '
								<div style="display:inline;" id="TimeToWait'.ucfirst(strtolower(self::TYPE)).'"></div>'
								.AfficheCompteurTemp(ucfirst(strtolower(self::TYPE)), './fct/main.php?action=stocker'.strtolower(self::TYPE), ($this->GetTempExtraction($stock[0]) - (strtotime('now') - parent::GetDateAction())));
			}
		}else{
			$status = '<p>Votre stock est plein.</p>';
		}
		
		$_SESSION['main'][self::TYPE]['production']	= $stock[0];
		$_SESSION['main'][self::TYPE]['vider']		= $stock[1];
		
		$PositionBatiment	= implode(',', array_merge(array(parent::GetCarte()),parent::GetCoordonnee()));
		$PositionJoueur		= implode(',', array_merge(array($oJoueur->GetCarte()),$oJoueur->GetPosition()));
		
		if($PositionBatiment == $PositionJoueur){
			$txtAction = '
				<td>
					<a href="./fct/main.php?action=viderstock'.strtolower(self::TYPE).'">Vider votre stock</a>
				</td>
				<td>'
					.'<form method="get" action="./fct/main.php" class="production">'
						.'<input type="hidden" name="action" value="production'.strtolower(self::TYPE).'" />'
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
				<td colspan="2">Vous ne pouvez rien exécuter car vous n\'êtes pas à votre '.strtolower(self::TYPE).'.</td>';
		}
		
		$txt ='
		<table border style="margin:3px;">
			<tr>
				<td style="width:60%;">Production de '.$this->QuelleQuantite($oJoueur->GetNiveauCompetence(self::TYPE_COMPETENCE), $stock[0]).'x '.AfficheIcone($IconeName).'</td>
				<td>'.$status.'</td>
			</tr>
			<tr>
				<td>Stock</td>
				<td>'.$stock[1].'/'.$this->GetStockMax().' '.AfficheIcone($IconeName).'</td>
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