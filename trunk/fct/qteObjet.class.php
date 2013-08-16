<?php
class qteObjet extends quete{
	
	Const COLOR			= '#8a8aff';
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $Quete, array $InfoQuete){
		date_default_timezone_set('Europe/Brussels');
	
		parent::Hydrate($Quete, $InfoQuete);
	
		$this->hydrate($Quete, $InfoQuete);
	
	}
	
	public function hydrate(array $Quete, array $InfoQuete){
		
	}

	public function ActionSurQuete(personnage &$oJoueur){
		if(is_null($this->CreateListObjectNeed()))
		{
			if(	$this->GetCoordonnee() == $oJoueur->GetCoordonnee())
			{
				$this->QueteAccomplie($oJoueur);
			}
		}
	}

	Public function ValiderQuete(personnage &$oJoueur, maison &$oMaison){
		if($oJoueur->CheckIfSurMaison())
		{
			if(CheckCout($this->CreateListObjectNeed(), $oJoueur, $oMaison))
			{
				foreach($this->CreateListObjectNeed() as $Objet)
				{
					UtilisationRessource(explode('=', $Objet), $oJoueur, $oMaison);
				}
				$this->QueteAccomplie($oJoueur);
			}
		}
	}
	Private function CheckCompleted(personnage &$oJoueur, maison &$oMaison){
		return CheckIfAssezRessource($arRessource, $oJoueur, $oMaison);
	}
	
	Private function QueteAccomplie(personnage &$oJoueur){
		Global $oDB;
		
		$this->RecupereGains($oJoueur);
		
		$this->FinishQuete();
		
		if(	!is_null($this->CreateListObjectNeed())
			AND !is_null($this->GetTextFinish()))
		{
			$txt = $this->GetTextFinish();
		}else{
			$txt = 'Bravo! Vous avez trouvé "'.$this->GetNom().'"';
		}
		
		//$txt = 'Bravo! Vous avez trouvé "'.$this->GetNom();
		//.'" et gagné ';
		//.$this->GainOr.' '.AfficheIcone('or').', '.$this->GainExperience.' d\'expérience et '.$this->GainPoints.' points.';
		
		$oDB->InsertHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'quete', $this->GetNom(), NULL, $txt);
		
		$_SESSION['message']['quete'] = $txt;
	}
	public function CreateListObjectNeed(){
		if(!is_null($this->GetCout()))
		{
			foreach($this->GetCout() as $Cout)
			{
				$tmpCout = explode('=', $Cout);
					
				switch(QuelTypeObjet($tmpCout[0])){
					case personnage::TYPE_RES_MONNAIE:
					case personnage::TYPE_COMPETENCE:
					case quete::TYPE_QUETE:
					case qteCombat::TYPE_QUETE_MONSTRE:
					case qteBatiment::TYPE_QUETE_BATIMENT:
						break;
					default:
						$lstObjects[] = implode('=', $tmpCout);
					break;
				}
					
			}
	
			if(isset($lstObjects))
			{
				return $lstObjects;
			}
		}
		return NULL;
	}
	//Les Affichages
	//==============
	public function AfficheDescriptif(personnage &$oJoueur, maison &$oMaison = NULL, $bAvancement){
		$_SESSION['quete'][$this->GetIDTypeQuete()] = $bAvancement;
		//On ajoute l'entete de la fiche
		$txt = '
					<div class="fiche_quete">
						<table class="fiche_quete">
							<tr style="background:'.$this::COLOR.';">
								<th>'.$this->GetNom().'</th>
							</tr>';
		if(!$bAvancement)
		{
			//Si pas encore acceptée, on affiche son cout
			$txt .= '
							<tr><th>Coût</th></tr>
							<tr><td>'.AfficheListePrix($this->CreateListCout(), $oJoueur, $oMaison).'</td></tr>';
		}elseif(!is_null($this->CreateListObjectNeed()))
		{
			$txt .= '
							<tr><th>Vous devez avoir les objets:</th></tr>
							<tr><td>'.AfficheListePrix($this->CreateListObjectNeed(), $oJoueur, $oMaison).'</td></tr>';
		}
	
		//on ajout des infos générale sur la fiche
		$txt .= '
							<tr><td class="description">'.$this->GetDescription().'</td></tr>
							<tr><th>Gains</th></tr>
							<tr><td>'.AfficheListePrix($this->GetGain()).'</td></tr>';
	
		if(	!$bAvancement
		AND !is_null($oMaison)
		AND $oJoueur->GetCoordonnee() == $oMaison->GetCoordonnee())
		{
			//on ajoute le boutton ACCEPTER
			$txt .= '
							<tr>
								<td>
									<button type="button" 
										onclick="window.location=\'index.php?page=quete&amp;action=inscription&amp;num_quete='.$this->GetIDTypeQuete().'\'"' 
			.((count($_SESSION['QueteEnCours']) < quete::NB_QUETE_MAX AND CheckCout($this->CreateListCout(), $oJoueur, $oMaison))?
			NULL
			:'disabled=disabled ')
			.'class="quete" >S\'inscrire</button>
								</td>
							</tr>';
		}elseif(!is_null($this->CreateListObjectNeed())
		AND !is_null($oMaison)
		AND $oJoueur->GetCoordonnee() == $oMaison->GetCoordonnee())
		{
			//On ajoute le boutton VALIDER
			$txt .= '
							<tr>
								<td>
									<button type="button"
										onclick="window.location=\'index.php?page=quete&amp;action=valider&amp;num_quete='.$this->GetIDTypeQuete().'\'"'
			.(($oJoueur->CheckIfSurMaison() AND CheckCout($this->CreateListObjectNeed(), $oJoueur, $oMaison))?
			NULL
			:'disabled=disabled ')
			.'class="quete" >Valider</button>
								</td>
							</tr>';
		}
	
		//On ferme la tableau de la ficher quete.
		$txt .= '
						</table>
					</div>';
	
		return $txt;
	}
	
	//Les GETS
	//========
	
}
?>