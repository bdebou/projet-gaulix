<?php
class qteCombat extends quete{
	
		
	Const COLOR					= '#ff6464';
	
	Const TYPE_QUETE_MONSTRE	= 'Monstre';
	Const TYPE_QUETE_ENNEMI		= 'Ennemi';
	
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $Quete, array $InfoQuete){
		date_default_timezone_set('Europe/Brussels');
	
		parent::Hydrate($Quete, $InfoQuete);
	
		$this->hydrate($Quete, $InfoQuete);
	
	}
	
	public function hydrate(array $Quete, array $InfoQuete){
		
		foreach ($Quete as $key => $value){
			switch ($key){
				case 'quete_vie':			self::SetVie(intval($value));		break;
			}
		}
		
		foreach ($InfoQuete as $key => $value){
			switch ($key){
			}
		}
	}
	public function ActionSurQuete(personnage &$joueur){
		return NULL;
	}
	public function ActionSurQueteCombat(personnage &$joueur){
		if(	$this->CheckQueteMonstre()
			OR $this->CheckQueteEnnemi())
		{
			//On crée l'objet Monstre ou Ennemi
			$oEnnemi = $this->FoundEnnemi();
			
			If(!is_null($oEnnemi))
			{
				$arAttaque = $joueur->GetAttPerso();
				$arDefense = $joueur->GetDefPerso();
				
				$txt = '<p>Vous avez attaqué "'.$oEnnemi->GetNom().'".';
				
				if((($arAttaque[0] + $arAttaque[1]) * 1.15) >= $oEnnemi->GetAttaque()){
					//on frappe le monstre car plus fort
					$ViePerdue = intval(($arAttaque[0] + $arAttaque[1]) * 1.15) - $oEnnemi->GetDefense();
					
					self::SetVie($this->GetVie() - $ViePerdue);
					
					$txt .= " Il a perdu $ViePerdue pts ".AfficheIcone(personnage::TYPE_VIE);
					
				}else{
					$txt .= " Il a perdu aucun pts ".AfficheIcone(personnage::TYPE_VIE);
				}
				
				if(($arDefense[0] + $arDefense[1]) < $oEnnemi->GetAttaque()){
					//On Perd quand meme un peu des pts de vie car le monstre est fort
					$ViePerdueJoueur = $oEnnemi->GetAttaque() - ($arDefense[0] + $arDefense[1]);
					
					$joueur->PerdreVie($ViePerdueJoueur, quete::TYPE_QUETE);
					
					$txt .= " mais vous, vous avez perdu $ViePerdueJoueur pts ".AfficheIcone(personnage::TYPE_VIE);
				}
				if($this->GetVie() <= 0)
				{
					$this->RecupereGains($joueur);
					$this->FinishQuete();
					$txt .= " et vous l'avez tué. Bravo!!!";
				}else{
					$this->SetPosition($this->MonstreFuit($joueur));
					$txt .= " et il s'est enfui.";
				}
				return $txt.'</p>';
			}
		}
		
		return NULL;
	}
	
	private function FoundEnnemi(){
		if(	!is_null($this->GetCout()))
		{
			foreach($this->GetCout() as $Cout)
			{
				$arCout = explode('=', $Cout);
				
				if(	$arCout[0] === self::TYPE_QUETE_ENNEMI
					OR $arCout[0] === self::TYPE_QUETE_MONSTRE)
				{
					$oEnnemi = FoundObjet($arCout[1]);
						
					if(!is_null($oEnnemi))
					{
						return $oEnnemi;
					}
				}
			}
		}
		return NULL;
	}
	/**
	 * Retourne une coordonnée libre
	 * @param personnage $joueur
	 * @return array(0=>Carte, 1=>X, 2=>Y) 
	 */
	private function MonstreFuit(personnage &$joueur){
		$carte = null;
		if(!$this->GetCheckCartes()){
			if(!is_null($joueur->GetMaisonInstalle())){
				$arcarte = explode(',', $joueur->GetMaisonInstalle());
				$carte = $arcarte[0];
			}else{
				$carte = $joueur->GetCarte();
			}
		}
		$free = FreeCaseCarte($carte);
	
		return explode(',', $free[array_rand($free)]);
	}
	
	//Les Affichages
	//==============
	public function AfficheDescriptif(personnage &$oJoueur, maison &$oMaison = NULL, $Avancement = false){
		$_SESSION['quete'][$this->GetIDTypeQuete()] = $Avancement;
		//On ajoute l'entete de la fiche
		$txt = '
					<div class="fiche_quete">
						<table class="fiche_quete">
							<tr style="background:'.$this::COLOR.';">
								<th>'.$this->GetNom().'</th>
							</tr>';
		if(!$Avancement)
		{
			//Si pas encore acceptée, on affiche son cout
			$txt .= '
							<tr><th>Coût</th></tr>
							<tr><td>'.AfficheListePrix($this->GetCout(), $oJoueur, $oMaison).'</td></tr>';
		}else{
			$txt .= '
							<tr><td><img alt="Barre de Vie" src="./fct/fct_image.php?type=vie&amp;value=' . $this->GetVie() . '&amp;max=' . $this->GetVieMax() . '&amp;taille=180x25" /></td></tr>';
		}
	
		//on ajout des infos générale sur la fiche
		$txt .= '
							<tr><td class="description">'.$this->GetDescription().'</td></tr>
							<tr><th>Gains</th></tr>
							<tr><td>'.AfficheListePrix($this->GetGain()).'</td></tr>';
	
		if(	!$Avancement
			AND !is_null($oMaison)
			AND $oJoueur->GetCoordonnee() == $oMaison->GetCoordonnee())
		{
			//on ajoute le boutton ACCEPTER
			$txt .= '<tr>
						<td>
							<button type="button"
								onclick="window.location=\'index.php?page=quete&amp;action=inscription&amp;num_quete='.$this->GetIDTypeQuete().'\'"' 
								.((count($_SESSION['QueteEnCours']) < quete::NB_QUETE_MAX AND CheckCout($this->GetCout(), $oJoueur, $oMaison))?
									NULL
									:'disabled=disabled ')
								.'class="quete" >S\'inscrire</button>
						</td>
					</tr>';
		}
	
		//On ferme la tableau de la ficher quete.
		$txt .= '		</table>
					</div>';
	
		return $txt;
	}

	//Les Checks
	//==========
	public function CheckQueteMonstre(){
		if(	!is_null($this->GetCout()))
		{
			foreach($this->GetCout() as $Cout)
			{
				$arCout = explode('=', $Cout);
				if($arCout[0] === self::TYPE_QUETE_MONSTRE)
				{
					return true;
				}
			}
		}
		return false;
	}
	public function CheckQueteEnnemi(){
		if(	!is_null($this->GetCout()))
		{
			foreach($this->GetCout() as $Cout)
			{
				$arCout = explode('=', $Cout);
				if($arCout[0] === self::TYPE_QUETE_ENNEMI)
				{
					return true;
				}
			}
		}
		return false;
	}
		
	//Les GETS
	//========
	public function GetTypeQuete(){
		if($this->CheckQueteMonstre())
		{
			return self::TYPE_QUETE_MONSTRE;
		}
		if($this->CheckQueteEnnemi()){
			return self::TYPE_QUETE_ENNEMI;
		}
		return NULL;
	}
	public function GetNomEnnemi(){
		$oEnnemi = $this->FoundEnnemi();
		
		if(!is_null($oEnnemi))
		{
			return $oEnnemi->GetNom();
		}
		return NULL;
	}
}
?>