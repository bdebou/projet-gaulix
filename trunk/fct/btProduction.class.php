<?php
abstract class btProduction extends batiment{
	private $Contenu;
	Private $DateAction;
	
	
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $carte = NULL, array $batiment){
		
		
		$this->Hydrate($carte, $batiment);
	}
	
	Public function Hydrate(array $carte = NULL, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($carte, $batiment);
		
		if(!is_null($carte))
		{
			foreach ($carte as $key => $value){
				switch ($key){
					case 'date_action_batiment':	$this->DateAction = (is_null($value)?NULL:strtotime($value)); break;
					case 'contenu_batiment':		$this->Contenu = (is_null($value)?array('a1', 0):explode(',', $value));	break;
				}
			}
		
			//on stock ce qui a été produit
			if($this->GetStockContenu() >= $this->GetStockMax())
			{
				$this->DateAction = strtotime('now');
				
			}elseif((strtotime('now') - $this->GetDateAction()) > $this->GetTempProduction($this->GetTypeContenu()))
			{
					
				global $objManager;
				$Producteur = $objManager->GetPersoLogin(parent::GetLogin());
					
				$this->AddStock($Producteur->GetNiveauCompetence($this::TYPE_COMPETENCE));
				
				unset($Producteur);
				
			}
			
			for($i = 2; $i <= $this->GetNbMaxEsclave(); $i++)
			{
				if(isset($this->Contenu[$i]))
				{
					$arEsclave = explode('=', $this->Contenu[$i]);
					if($arEsclave[0] == parent::CODE_ESCLAVE AND (strtotime('now') - $arEsclave[1]) >= $this::DUREE_ESCLAVE)
					{
						unset($this->Contenu[$i]);
					}
				}else{
					break;
				}
			}
		}
	}
	
	//--- Gestion du stock  ---
	public function ViderStock(personnage &$joueur){
		if($this->GetStockContenu() > 0)
		{
			$joueur->AddInventaire($this->GetCodeRessource($this->GetTypeContenu()), $this->GetStockContenu(), false);
		}
		
		$this->Contenu[1] -= $this->GetStockContenu();
		
		if($this->GetStockMax() == $this->GetStockContenu())
		{
			$this->DateAction = strtotime('now');
		}
	}
	public function AddStock(){
		//$arContenu = explode(',', $this->Contenu);
		
		$nb = intval((strtotime('now') - parent::GetDateAction()) / $this->GetTempProduction($this->GetTypeContenu()));
		
		for($i=1;$i<=$nb;$i++){
			if($this->GetStockMax() > $this->Contenu[1]){
				$this->Contenu[1] += $this->QuelleQuantite($this->GetTypeContenu());
			}else{break;}
		}
		
		$this->Contenu[1] = ($this->GetStockContenu() > $this->GetStockMax()?$this->GetStockMax():$this->GetStockContenu());
		$this->DateAction = strtotime('now');
	}
	//--- On change de type de production ---
	public function ChangerProductionBatiment($production){
		//$arContenu = explode(',', $this->Contenu);
		
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
	public function AfficheContenu(personnage &$oJoueur){//OK
		//$stock = explode(',', $this->Contenu);
		
		if($this->GetStockContenu() < $this->GetStockMax()){
			$_SESSION['main'][get_class($this)]['stock'] = 1;
			$status = '
							<div style="display:inline;" id="TimeToWait'.ucfirst(strtolower(get_class($this))).'"></div>'
							.AfficheCompteurTemp(ucfirst(strtolower(get_class($this))), 'index.php?page=village', ($this->GetTempProduction($this->GetTypeContenu()) - (strtotime('now') - parent::GetDateAction())));
		}else{
			$status = '<p>Votre stock est plein.</p>';
		}
		
		$_SESSION['main'][get_class($this)]['production']	= $this->GetTypeContenu();
		//$_SESSION['main'][get_class($this)]['vider']		= $this->GetStockContenu();
		
		//$PositionBatiment	= implode(',', $this->GetCoordonnee());
		//$PositionJoueur		= implode(',', $oJoueur->GetCoordonnee());
		
		if($this->GetCoordonnee() == $oJoueur->GetCoordonnee()){
			$txtAction = '
				<td>
					<form method="post" action="index.php?page=village">
						<input type="hidden" name="action" value="viderstock'.strtolower(get_class($this)).'" />
						<input type="hidden" name="anchor" value="'.str_replace(',', '_', $this->GetCoordonnee()).'" />
						<input type="submit" name="submit" value="Vider votre stock" />
					</form>
				</td>
				<td>'
					.'<form method="post" action="index.php?page=village" class="production">'
						.'<input type="hidden" name="page" value="village" />'
						.'<input type="hidden" name="anchor" value="'.str_replace(',', '_', $this->GetCoordonnee()).'" />'
						.'<input type="hidden" name="action" value="production'.strtolower(get_class($this)).'" />'
						.'<select name="type" onclick="document.getElementById(\'BtSubmit\').disabled=false;">'
							.$this->GetListSelectOptionProducion()
						.'</select>'
						.'<input type="submit" value="Go" id="BtSubmit" disabled="disabled" />'
					.'</form>'
				.'</td>';
		}else{
			$txtAction = '
				<td colspan="2">Vous ne pouvez rien exécuter car vous n\'êtes pas à votre '.strtolower(get_class($this)).'.</td>';
		}
		
		
		$txt ='
		<table border style="margin:3px; width:100%;">
			<tr>
				<td style="width:60%;">Production de '.$this->QuelleQuantite($this->GetTypeContenu()).'x '.AfficheIcone($this->GetCodeRessource($this->GetTypeContenu())).'</td>
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
	public function GetStockMax(){				return 500 + ($this->GetNiveau() * 100);}
	public function GetContenu(){				return $this->Contenu;}
	public function GetDateAction(){			return $this->DateAction;}
	public function GetTypeContenu(){
		//$contenu = explode(',', $this->Contenu);
		return $this->Contenu[0];
	}
	public function GetStockContenu(){
		//$contenu = explode(',', $this->Contenu);
		return $this->Contenu[1];
	}
	public function GetNbMaxEsclave($Niveau = NULL){
		if(is_null($Niveau))
		{
			$Niveau = $this->GetNiveau();
		}
	
		switch($Niveau)
		{
			case 1:	return $this::NB_ESCLAVES_NIV_1;
			case 2:	return $this::NB_ESCLAVES_NIV_2;
			case 3:	return $this::NB_ESCLAVES_NIV_3;
			case 4:	return $this::NB_ESCLAVES_NIV_4;
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