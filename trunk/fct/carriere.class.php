<?php
class mine extends batiment{
	private $Contenu;
	private $DateAction;
	
	const COUT_AMELIORATION_NIVEAU_1	= 'ResBois=5,ResOr=10,ResMinF=150';
	const COUT_AMELIORATION_NIVEAU_2	= 'ResOr=1000,ResBois=1500,ResPierre=2';
	const COUT_AMELIORATION_NIVEAU_3	= 'ResOr=150000';
	
	const TYPE_COMPETENCE			= 'Mineur';
	const STOCK_MAX_DEPART			= 500;
	
	const GAIN_TEMP_PAR_ESCLAVE		= 7;
	const DUREE_ESCLAVE				= 604800;
	
	const NB_ESCLAVES_NIV_1			= 2;
	const NB_ESCLAVES_NIV_2			= 4;
	const NB_ESCLAVES_NIV_3			= 8;
	const NB_ESCLAVES_NIV_4			= 12;
	
	//Les communs sont :
	const CODE_PRODUCTION_SABLE		= 'a1';
	const NOM_PRODUCTION_SABLE		= 'du sable';
	const NIVEAU_COMPETENCE_SABLE	= 1;
	const ICONE_NAME_SABLE			= 'ResSable';
	const TEMP_SABLE				= 900;
	
	const CODE_PRODUCTION_CHAUX		= 'a2';
	const NOM_PRODUCTION_CHAUX		= 'de la chaux';
	const NIVEAU_COMPETENCE_CHAUX	= 2;
	const ICONE_NAME_CHAUX			= 'ResChaux';
	const TEMP_CHAUX				= 900;
	
	const CODE_PRODUCTION_GRAVIER	= 'a3';
	const NOM_PRODUCTION_GRAVIER	= 'du gravier';
	const NIVEAU_COMPETENCE_GRAVIER	= 3;
	const ICONE_NAME_GRAVIER		= 'ResGravier';
	const TEMP_GRAVIER				= 900;
	
	const CODE_PRODUCTION_CIMENT	= 'a4';
	const NOM_PRODUCTION_CIMENT		= 'du ciment';
	const NIVEAU_COMPETENCE_CIMENT	= 4;
	const ICONE_NAME_CIMENT			= 'ResCiment';
	const TEMP_CIMENT				= 900;
		
	//Les sp�cifiques sont :
	const CODE_PRODUCTION_PIERRE_B	= 'b1';
	const NOM_PRODUCTION_PIERRE_B	= 'de la pierre brute';
	const NIVEAU_COMPETENCE_PIERRE_B= 1;
	const ICONE_NAME_PIERRE_B		= 'ResPierreB';
	const TEMP_PIERRE_B				= 2400;
	
	const CODE_PRODUCTION_GRANIT	= 'b2';
	const NOM_PRODUCTION_GRANIT		= 'du Granit';
	const NIVEAU_COMPETENCE_GRANIT	= 2;
	const ICONE_NAME_GRANIT			= 'ResGranit';
	const TEMP_GRANIT				= 3000;
	
	const CODE_PRODUCTION_MARBRE	= 'b3';
	const NOM_PRODUCTION_MARBRE		= 'du Marbre';
	const NIVEAU_COMPETENCE_MARBRE	= 3;
	const ICONE_NAME_MARBRE			= 'ResMarbre';
	const TEMP_MARBRE				= 3000;
	
	const CODE_PRODUCTION_DIAMANT	= 'b4';
	const NOM_PRODUCTION_DIAMANT	= 'du Diamant';
	const NIVEAU_COMPETENCE_DIAMANT	= 4;
	const ICONE_NAME_DIAMANT		= 'ResDiamant';
	const TEMP_DIAMANT				= 3600;
	
		
	//--- fonction qui est lancer lors de la cr�ation de l'objet. ---
	public function __construct(array $carte, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($carte, $batiment);
		
		foreach ($carte as $key => $value){
			switch ($key){
				case 'contenu_batiment':		$this->Contenu = (is_null($value)?array('a1', 0):explode(',', $value));	break;
				case 'date_action_batiment':	$this->DateAction = (is_null($value)?NULL:strtotime($value));	break;
			}
		}
		
			//on stock ce qui a �t� produit
		if(	$this->GetStockContenu() < $this->GetStockMax()
			AND (strtotime('now') - $this->GetDateAction()) > $this->GetTempExtraction($this->GetTypeContenu())){
				
			global $objManager;
			$Producteur = $objManager->GetPersoLogin(parent::GetLogin());
				
			$this->AddStock($Producteur->GetNiveauCompetence(self::TYPE_COMPETENCE));
				
			unset($Producteur);
		}
		
		for($i = 2; $i <= $this->GetNbMaxEsclave(); $i++)
		{
			if(isset($this->Contenu[$i]))
			{
				$arEsclave = explode('=', $this->Contenu[$i]);
				if($arEsclave[0] == parent::CODE_ESCLAVE AND (strtotime('now') - $arEsclave[1]) >= self::DUREE_ESCLAVE)
				{
					unset($this->Contenu[$i]);
				}
			}else{
				break;
			}
		}
	}
	
	//Quelle quantit� maximum on peut prendre maximum par action sur une ressource
	Private function QuelleQuantite($NiveauCompetence, $CodeProduction){
	
		switch($CodeProduction)
		{
			case self::CODE_PRODUCTION_SABLE:
			case self::CODE_PRODUCTION_PIERRE_B:
				switch($NiveauCompetence)
				{
					case 0: return 5;
					case 1: return 10;
					case 2: return 20;
					case 3: return 30;
					case 4: return 40;
					case 5: return 50;
				}
				break;
			case self::CODE_PRODUCTION_CHAUX:
			case self::CODE_PRODUCTION_GRANIT:
				switch($NiveauCompetence)
				{
					case 0:
					case 1:	return 5;
					case 2: return 10;
					case 3: return 10;
					case 4: return 20;
					case 5: return 30;
				}
				break;
			case self::CODE_PRODUCTION_GRAVIER:
			case self::CODE_PRODUCTION_MARBRE:
				switch($NiveauCompetence)
				{
					case 0:
					case 1:
					case 2:
					case 3: return 5;
					case 4: return 10;
					case 5: return 20;
				}
				break;
			case self::CODE_PRODUCTION_CIMENT:
			case self::CODE_PRODUCTION_DIAMANT:
				switch($NiveauCompetence)
				{
					case 0:
					case 1:
					case 2:
					case 3:
					case 4: return 5;
					case 5: return 10;
				}
				break;
		}
		
		return 0;
	}
	
	//--- Gestion du stock  ---
	public function ViderStock($stock, maison &$maison, personnage &$joueur){
		switch($this->GetTypeContenu()){
			case self::CODE_PRODUCTION_SABLE:
			case self::CODE_PRODUCTION_CHAUX:
			case self::CODE_PRODUCTION_GRAVIER:
			case self::CODE_PRODUCTION_CIMENT:
			case self::CODE_PRODUCTION_PIERRE_B:
			case self::CODE_PRODUCTION_GRANIT:
			case self::CODE_PRODUCTION_MARBRE:
			case self::CODE_PRODUCTION_DIAMANT:
				$joueur->AddInventaire($this->GetTypeContenu(), NULL, $stock, false);
				break;
		}

		//$this->Contenu = $Contenu[0].','.($Contenu[1] - $stock);
		$this->Contenu[1] = $this->GetStockContenu() - $stock;
		if($this->GetStockMax() == $stock){$this->DateAction = strtotime('now');}
	}
	public function AddStock($NiveauCompetence){
		//$Contenu = explode(',', $this->Contenu);
		
		$nb = intval((strtotime('now') - parent::GetDateAction()) / $this->GetTempExtraction($this->GetTypeContenu()));
		
		for($i=1;$i<=$nb;$i++){
			if($this->GetStockMax() > $this->Contenu['1']){
				$this->Contenu[1] += $this->QuelleQuantite($NiveauCompetence, $this->GetTypeContenu());
			}else{break;}
		}
		
		//$this->Contenu = $this->Contenu[0].','.($this->Contenu[1] > $this->GetStockMax()?$this->GetStockMax():$this->Contenu[1]);
		$this->Contenu[1] = ($this->GetStockContenu() > $this->GetStockMax()?$this->GetStockMax():$this->GetStockContenu());
		$this->DateAction = strtotime('now');
	}
	//--- On change de type de production ---
	public function ChangerProductionBatiment($production){
		//$Contenu = explode(',', $this->Contenu);
		
		//$this->Contenu = $production.','.$this->Contenu[1];
		$this->Contenu[0] = $production;
		$this->DateAction = strtotime('now');
	}
	
	//Les Affichages
	//==============
	public function AfficheContenu(personnage &$oJoueur){

		if($this->GetStockContenu() < $this->GetStockMax()){
			$status = '<div style="display:inline;" id="TimeToWait'.ucfirst(strtolower(get_class($this))).'"></div>'
						.AfficheCompteurTemp(ucfirst(strtolower(get_class($this))), 'index.php?page=village', ($this->GetTempExtraction($this->GetTypeContenu()) - (strtotime('now') - $this->GetDateAction())));
		}else{
			$status = '<p>Votre stock est plein.</p>';
		}
		
		$_SESSION['main'][get_class($this)]['production']	= $this->GetTypeContenu();
		$_SESSION['main'][get_class($this)]['vider']		= $this->GetStockContenu();
		
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
							.($this->GetNiveau() >= self::NIVEAU_COMPETENCE_SABLE?'<option value="'.self::CODE_PRODUCTION_SABLE.'"'.($this->GetTypeContenu() == self::CODE_PRODUCTION_SABLE?' disabled="disabled"':'').'>'.self::NOM_PRODUCTION_SABLE.'</option>':'')
							.($this->GetNiveau() >= self::NIVEAU_COMPETENCE_PIERRE_B?'<option value="'.self::CODE_PRODUCTION_PIERRE_B.'"'.($this->GetTypeContenu() == self::CODE_PRODUCTION_PIERRE_B?' disabled="disabled"':'').'>'.self::NOM_PRODUCTION_PIERRE_B.'</option>':'')
							.($this->GetNiveau() >= self::NIVEAU_COMPETENCE_CHAUX?'<option value="'.self::CODE_PRODUCTION_CHAUX.'"'.($this->GetTypeContenu() == self::CODE_PRODUCTION_CHAUX?' disabled="disabled"':'').'>'.self::NOM_PRODUCTION_CHAUX.'</option>':'')
							.($this->GetNiveau() >= self::NIVEAU_COMPETENCE_GRANIT?'<option value="'.self::CODE_PRODUCTION_GRANIT.'"'.($this->GetTypeContenu() == self::CODE_PRODUCTION_GRANIT?' disabled="disabled"':'').'>'.self::NOM_PRODUCTION_GRANIT.'</option>':'')
							.($this->GetNiveau() >= self::NIVEAU_COMPETENCE_GRAVIER?'<option value="'.self::CODE_PRODUCTION_GRAVIER.'"'.($this->GetTypeContenu() == self::CODE_PRODUCTION_GRAVIER?' disabled="disabled"':'').'>'.self::NOM_PRODUCTION_GRAVIER.'</option>':'')
							.($this->GetNiveau() >= self::NIVEAU_COMPETENCE_MARBRE?'<option value="'.self::CODE_PRODUCTION_MARBRE.'"'.($this->GetTypeContenu() == self::CODE_PRODUCTION_MARBRE?' disabled="disabled"':'').'>'.self::NOM_PRODUCTION_MARBRE.'</option>':'')
							.($this->GetNiveau() >= self::NIVEAU_COMPETENCE_CIMENT?'<option value="'.self::CODE_PRODUCTION_CIMENT.'"'.($this->GetTypeContenu() == self::CODE_PRODUCTION_CIMENT?' disabled="disabled"':'').'>'.self::NOM_PRODUCTION_CIMENT.'</option>':'')
							.($this->GetNiveau() >= self::NIVEAU_COMPETENCE_DIAMANT?'<option value="'.self::CODE_PRODUCTION_DIAMANT.'"'.($this->GetTypeContenu() == self::CODE_PRODUCTION_DIAMANT?' disabled="disabled"':'').'>'.self::NOM_PRODUCTION_DIAMANT.'</option>':'')
						.'</select>'
						.'<input type="submit" value="Go" id="BtSubmit" disabled="disabled" />'
					.'</form>'
				.'</td>';
		}else{
			$txtAction = '
				<td colspan="2">Vous ne pouvez rien ex�cuter car vous n\'�tes pas � votre '.strtolower(get_class($this)).'.</td>';
		}
		
		$txt ='
		<table border style="width:100%;">
			<tr>
				<td style="width:60%;">Production de '.$this->QuelleQuantite($oJoueur->GetNiveauCompetence(self::TYPE_COMPETENCE), $this->GetTypeContenu()).'x '.AfficheIcone($this->GetIconeNameProduction($this->GetTypeContenu())).'</td>
				<td>'.$status.'</td>
			</tr>
			<tr>
				<td>Stock</td>
				<td>'.$this->GetStockContenu().'/'.$this->GetStockMax().' '.AfficheIcone($this->GetIconeNameProduction($this->GetTypeContenu())).'</td>
			</tr>
			<tr>
				'.$txtAction.'
			</tr>
		</table>';
		
		return $txt;
	}
	
	//Les GETS
	//========
	public function GetAttaque(){
		if(parent::GetNiveau() >= 4)
		{
			return 5;
		}else{
			return 0;
		}
	}
	public function GetStockMax(){				return self::STOCK_MAX_DEPART + (parent::GetNiveau() * 100);}
	public function GetContenu(){				return $this->Contenu;}
	Public function GetIconeNameProduction($code){
		switch($code){
			case self::CODE_PRODUCTION_SABLE:		return self::ICONE_NAME_SABLE;
			case self::CODE_PRODUCTION_CHAUX:		return self::ICONE_NAME_CHAUX;
			case self::CODE_PRODUCTION_GRAVIER:		return self::ICONE_NAME_GRAVIER;
			case self::CODE_PRODUCTION_CIMENT:		return self::ICONE_NAME_CIMENT;
			case self::CODE_PRODUCTION_PIERRE_B:	return self::ICONE_NAME_PIERRE_B;
			case self::CODE_PRODUCTION_GRANIT:		return self::ICONE_NAME_GRANIT;
			case self::CODE_PRODUCTION_MARBRE:		return self::ICONE_NAME_MARBRE;
			case self::CODE_PRODUCTION_DIAMANT:		return self::ICONE_NAME_DIAMANT;
		}
	}
	public function GetTypeContenu(){
		//$contenu = explode(',', $this->Contenu);
		return $this->Contenu[0];
	}
	public function GetStockContenu(){
		//$contenu = explode(',', $this->Contenu);
		return $this->Contenu[1];
	}
	public function GetDateAction(){			return $this->DateAction;}
	public function GetTempExtraction($code){
		$Duree = 0;
		
		switch($code){
			case self::CODE_PRODUCTION_SABLE:		$Duree = self::TEMP_SABLE;		break;
			case self::CODE_PRODUCTION_CHAUX:		$Duree = self::TEMP_CHAUX;		break;
			case self::CODE_PRODUCTION_GRAVIER:		$Duree = self::TEMP_GRAVIER;	break;
			case self::CODE_PRODUCTION_CIMENT:		$Duree = self::TEMP_CIMENT;		break;
			case self::CODE_PRODUCTION_PIERRE_B:	$Duree = self::TEMP_PIERRE_B;		break;
			case self::CODE_PRODUCTION_GRANIT:		$Duree = self::TEMP_GRANIT;		break;
			case self::CODE_PRODUCTION_MARBRE:		$Duree = self::TEMP_MARBRE;		break;
			case self::CODE_PRODUCTION_DIAMANT:		$Duree = self::TEMP_DIAMANT;		break;
		}
		
		$Duree = $Duree * ((100 - (self::GAIN_TEMP_PAR_ESCLAVE * $this->GetNbEsclave())) / 100);
		
		return $Duree;
	}
	public function GetNbMaxEsclave($Niveau = NULL){
		if(is_null($Niveau))
		{
			$Niveau = $this->GetNiveau();
		}
		
		switch($Niveau)
		{
			case 1:	return self::NB_ESCLAVES_NIV_1;
			case 2:	return self::NB_ESCLAVES_NIV_2;
			case 3:	return self::NB_ESCLAVES_NIV_3;
			case 4:	return self::NB_ESCLAVES_NIV_4;
		}
		return 0;
	}
	public function GetNbEsclave(){
		$nbEsclave = 0;
		
		for($i = 2; $i <= $this->GetNbMaxEsclave(); $i++)
		{
			if(isset($this->Contenu[$i]))
			{
				$arEsclave = explode('=', $this->Contenu[$i]);
				if($arEsclave[0] == parent::CODE_ESCLAVE)
				{
					$nbEsclave++;
				}
			}else{
				break;
			}
		}
		
		return $nbEsclave;
	}
}
?>