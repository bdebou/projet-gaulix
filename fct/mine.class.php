<?php
class mine extends batiment{
	private $Contenu;
	private $DateAction;
	
	const COUT_AMELIORATION_NIVEAU_1	= 'ResBois=5,Sesterce=10,ResMinF=150';
	const COUT_AMELIORATION_NIVEAU_2	= 'Sesterce=1000,ResBois=1500,ResPierre=2';
	const COUT_AMELIORATION_NIVEAU_3	= 'Sesterce=150000';
	
	const ID_BATIMENT				= 10;
	
	const TYPE_COMPETENCE			= 'Mineur';
	const STOCK_MAX_DEPART			= 500;
	
	const GAIN_TEMP_PAR_ESCLAVE		= 7;
	const DUREE_ESCLAVE				= 604800;
	
	const NB_ESCLAVES_NIV_1			= 2;
	const NB_ESCLAVES_NIV_2			= 4;
	const NB_ESCLAVES_NIV_3			= 8;
	const NB_ESCLAVES_NIV_4			= 12;
	
	//Les valeurs communes de production avec la carrière sont :
	const A1_CODE				= 'a1';
	const A1_NOM				= 'du sable';
	const A1_NIVEAU_COMPETENCE	= 1;
	const A1_CODE_OBJET			= 'ResSable';
	const A1_TEMP				= 900;
	
	const A2_CODE				= 'a2';
	const A2_NOM				= 'de la chaux';
	const A2_NIVEAU_COMPETENCE	= 2;
	const A2_CODE_OBJET			= 'ResChaux';
	const A2_TEMP				= 900;
	
	const A3_CODE				= 'a3';
	const A3_NOM				= 'du gravier';
	const A3_NIVEAU_COMPETENCE	= 3;
	const A3_CODE_OBJET			= 'ResGravier';
	const A3_TEMP				= 900;
	
	const A4_CODE				= 'a4';
	const A4_NOM				= 'du ciment';
	const A4_NIVEAU_COMPETENCE	= 4;
	const A4_CODE_OBJET			= 'ResCiment';
	const A4_TEMP				= 900;
		
	//Les valeurs de production spécifique à la mine
	const B1_CODE				= 'b1';
	const B1_NOM				= 'de l\'étain';
	const B1_NIVEAU_COMPETENCE	= 1;
	const B1_CODE_OBJET			= 'ResEtain';
	const B1_TEMP				= 2400;
	
	const B2_CODE				= 'b2';
	const B2_NOM				= 'du minerai de cuivre';
	const B2_NIVEAU_COMPETENCE	= 2;
	const B2_CODE_OBJET			= 'ResMinC';
	const B2_TEMP				= 3000;
	
	const B3_CODE				= 'b3';
	const B3_NOM				= 'du minerai d\'argent';
	const B3_NIVEAU_COMPETENCE	= 3;
	const B3_CODE_OBJET			= 'ResMinA';
	const B3_TEMP				= 3000;
	
	const B4_CODE				= 'b4';
	const B4_NOM				= 'du minerai d\'or';
	const B4_NIVEAU_COMPETENCE	= 4;
	const B4_CODE_OBJET			= 'or';
	const B4_TEMP				= 3600;
	
		
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $carte, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($carte, $batiment);
		
		foreach ($carte as $key => $value){
			switch ($key){
				case 'contenu_batiment':		$this->Contenu = (is_null($value)?array('a1', 0):explode(',', $value));	break;
				case 'date_action_batiment':	$this->DateAction = (is_null($value)?NULL:strtotime($value));	break;
			}
		}
		
			//on stock ce qui a été produit
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
	
	//Quelle quantité maximum on peut prendre maximum par action sur une ressource
	Private function QuelleQuantite($NiveauCompetence, $CodeProduction){
	
		switch($CodeProduction)
		{
			case self::A1_CODE:
			case self::B1_CODE:
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
			case self::A2_CODE:
			case self::B2_CODE:
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
			case self::A3_CODE:
			case self::B3_CODE:
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
			case self::A4_CODE:
			case self::B4_CODE:
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
	public function ViderStock($stock, personnage &$joueur){
		$joueur->AddInventaire($this->GetCodeRessource($this->GetTypeContenu()), NULL, $stock, false);

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
	Public function AfficheAchatEsclave(personnage &$oJoueur){
		$txt = NULL;
		
		if($this->GetNbEsclave() < $this->GetNbMaxEsclave())
		{
			
		}
		
		return $txt;
	}
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
							.($this->GetNiveau() >= self::A1_NIVEAU_COMPETENCE?'<option value="'.self::A1_CODE.'"'.($this->GetTypeContenu() == self::A1_CODE?' disabled="disabled"':'').'>'.self::A1_NOM.'</option>':'')
							.($this->GetNiveau() >= self::B1_NIVEAU_COMPETENCE?'<option value="'.self::B1_CODE.'"'.($this->GetTypeContenu() == self::B1_CODE?' disabled="disabled"':'').'>'.self::B1_NOM.'</option>':'')
							.($this->GetNiveau() >= self::A2_NIVEAU_COMPETENCE?'<option value="'.self::A2_CODE.'"'.($this->GetTypeContenu() == self::A2_CODE?' disabled="disabled"':'').'>'.self::A2_NOM.'</option>':'')
							.($this->GetNiveau() >= self::B2_NIVEAU_COMPETENCE?'<option value="'.self::B2_CODE.'"'.($this->GetTypeContenu() == self::B2_CODE?' disabled="disabled"':'').'>'.self::B2_NOM.'</option>':'')
							.($this->GetNiveau() >= self::A3_NIVEAU_COMPETENCE?'<option value="'.self::A3_CODE.'"'.($this->GetTypeContenu() == self::A3_CODE?' disabled="disabled"':'').'>'.self::A3_NOM.'</option>':'')
							.($this->GetNiveau() >= self::B3_NIVEAU_COMPETENCE?'<option value="'.self::B3_CODE.'"'.($this->GetTypeContenu() == self::B3_CODE?' disabled="disabled"':'').'>'.self::B3_NOM.'</option>':'')
							.($this->GetNiveau() >= self::A4_NIVEAU_COMPETENCE?'<option value="'.self::A4_CODE.'"'.($this->GetTypeContenu() == self::A4_CODE?' disabled="disabled"':'').'>'.self::A4_NOM.'</option>':'')
							.($this->GetNiveau() >= self::B4_NIVEAU_COMPETENCE?'<option value="'.self::B4_CODE.'"'.($this->GetTypeContenu() == self::B4_CODE?' disabled="disabled"':'').'>'.self::B4_NOM.'</option>':'')
						.'</select>'
						.'<input type="submit" value="Go" id="BtSubmit" disabled="disabled" />'
					.'</form>'
				.'</td>';
		}else{
			$txtAction = '
				<td colspan="2">Vous ne pouvez rien exécuter car vous n\'êtes pas à votre '.strtolower(get_class($this)).'.</td>';
		}
		
		$txt ='
		<table border style="width:100%;">
			<tr>
				<td style="width:60%;">Production de '.$this->QuelleQuantite($oJoueur->GetNiveauCompetence(self::TYPE_COMPETENCE), $this->GetTypeContenu()).'x '.AfficheIcone($this->GetCodeRessource($this->GetTypeContenu())).'</td>
				<td>'.$status.'</td>
			</tr>
			<tr>
				<td>Stock</td>
				<td>'.$this->GetStockContenu().'/'.$this->GetStockMax().' '.AfficheIcone($this->GetCodeRessource($this->GetTypeContenu())).'</td>
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
	public function GetTypeContenu(){
		//$contenu = explode(',', $this->Contenu);
		return $this->Contenu[0];
	}
	public function GetStockContenu(){
		//$contenu = explode(',', $this->Contenu);
		return $this->Contenu[1];
	}
	public function GetCodeRessource($CodeType){
		switch($CodeType){
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
	public function GetDateAction(){			return $this->DateAction;}
	public function GetTempExtraction($code){
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