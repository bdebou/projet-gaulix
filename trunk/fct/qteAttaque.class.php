<?php
class qteAttaque extends quete{
	
	Private $Force;
	Private $Defense;
	Private $Vie;
	
	Const COLOR					= '#ff6464';
	
	Const TYPE_QUETE_ATTAQUE	= 'Batiment';
	
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $Quete, array $InfoQuete){
		date_default_timezone_set('Europe/Brussels');
	
		parent::Hydrate($Quete, $InfoQuete);
	
		$this->hydrate($Quete, $InfoQuete);
	
	}
	
	public function hydrate(array $Quete, array $InfoQuete){
		
		foreach ($Quete as $key => $value){
			switch ($key){
				case 'quete_vie':			$this->Vie				= intval($value);								break;
			}
		}
		
		foreach ($InfoQuete as $key => $value){
			switch ($key){
				case 'quete_attaque':		$this->Force			= (is_null($value)?NULL:intval($value));		break;
				case 'quete_defense':		$this->Defense			= (is_null($value)?NULL:intval($value));		break;
			}
		}
	}
	
	
	//On attaque un Monstre de quete
	/* private function QueteMonstre(personnage &$joueur){
		$ForceMonstre = $this->Force + ($joueur->GetNiveau() * 3);
		$arAttaque = $joueur->GetAttPerso();
		$arDefense = $joueur->GetDefPerso();
		$txt = '<p>Vous avez attaqué "'.$this->Nom.'".';
		if((($arAttaque['0'] + $arAttaque['1']) * 1.15) >= $ForceMonstre){
			//on frappe le monstre car plus fort
			$ViePerdue = intval(($arAttaque['0'] + $arAttaque['1']) * 1.15);
			$this->Vie -= $ViePerdue;
			$txt .= " Il a perdu $ViePerdue pts ".AfficheIcone('vie');
		}else{$txt .= " Il a perdu aucun pts ".AfficheIcone('vie');
		}
		if(($arDefense['0'] + $arDefense['1']) < $ForceMonstre){
			//On Perd quand meme un peu des pts de vie car le monstre est fort
			$ViePerdueJoueur = $ForceMonstre - ($arDefense['0'] + $arDefense['1']);
			$joueur->PerdreVie($ViePerdueJoueur,'quete');
			$txt .= " mais vous, vous avez perdu $ViePerdueJoueur pts ".AfficheIcone('vie');
		}
		if($this->Vie <= 0){
			$this->FinishQuete();
			$txt .= " et vous l'avez tué. Bravo!!!";
		}else{
			$this->Position = $this->MonstreFuit($joueur);
			$txt .= " et il s'est enfui.";
		}
		return $txt.'</p>';
	} */
	
	/* Private function QueteRomains(personnage &$Joueur){
		$ForceRomain = $this->Force + ($Joueur->GetNiveau() * 3);
	
		$arAttaque = $Joueur->GetAttPerso();
		$arDefense = $Joueur->GetDefPerso();
	
		$txt = '<p>Vous avez attaqué "'.$this->Nom.'".';
	
		if((($arAttaque['0'] + $arAttaque['1']) * 1.15) >= $ForceRomain){
			//on frappe les romains car plus fort
			$ViePerdue = intval(($arAttaque['0'] + $arAttaque['1']) * 1.15);
			$this->Vie -= $ViePerdue;
			$txt .= " Ils ont perdu $ViePerdue pts ".AfficheIcone('vie');
		}else{$txt .= " Ils ont perdu aucun pts ".AfficheIcone('vie');
		}
	
		if(($arDefense['0'] + $arDefense['1']) < $ForceRomain){
			//On Perd quand meme un peu des pts de vie car les romains sont forts
			$ViePerdueJoueur = $ForceRomain - ($arDefense['0'] + $arDefense['1']);
			$Joueur->PerdreVie($ViePerdueJoueur,'quete');
			$txt .= " mais vous, vous avez perdu $ViePerdueJoueur pts ".AfficheIcone('vie');
		}
	
		if($this->Vie <= 0){
			$this->FinishQuete();
			$txt .= " et vous les avez tués. Bravo!!!";
			$Joueur->UpdateScores(1, 0);
		}else{
			$this->DateCombat = strtotime('now');
			$txt .= ".";
		}
	
		return $txt.'</p>';
	} */
	
	//on update la quete romain en la déplacant
	/* private function UpdateQueteRomains(){
		$nbDep = intval((strtotime('now') - $this->date_start) / 3600);
	
		if($nbDep > 0){
				
			if($nbDep > self::MAX_DEPLACEMENT){
				$nbDep = self::MAX_DEPLACEMENT;
			}
				
			for($i = 0; $i <= $nbDep; $i++){
				$this->QueteSeDeplaceUneCase();
			}
				
			$this->date_start = strtotime('now');
		}
	} */
	
	/* private function MonstreFuit(personnage &$joueur){
		$carte = null;
		if($joueur->GetNiveau() <= 3){
			if(!is_null($joueur->GetMaisonInstalle())){
				$arcarte = $joueur->GetMaisonInstalle();
				$carte = $arcarte['0'];
			}else{
				$carte = $joueur->GetCarte();
			}
		}
		$free = FreeCaseCarte($carte);
	
		return explode(',', $free[array_rand($free)]);
	} */
	
	//Les Affichages
	//==============
	public function AfficheDescriptif(personnage &$oJoueur, maison &$oMaison = NULL, $Avancement = false){
		$_SESSION['quete'][$this->GetIDTypeQuete()] = $Avancement;
		//On ajoute l'entete de la fiche
		$txt = '
					<div class="fiche_quete">
						<table class="fiche_quete">
							<tr style="background:'.$this::COLOR.';">
								<th>'.$this->Nom.'</th>
							</tr>';
		if(!$Avancement)
		{
			//Si pas encore acceptée, on affiche son cout
			$txt .= '
							<tr><th>Coût</th></tr>
							<tr><td>'.AfficheListePrix($this->Cout, $oJoueur, $oMaison).'</td></tr>';
		}else{
			$txt .= '
							<tr><td><img alt="Barre de Vie" src="./fct/fct_image.php?type=vie&amp;value=' . $this->GetVie() . '&amp;max=' . $this->GetVieMax() . '" /></td></tr>';
		}
	
		//on ajout des infos générale sur la fiche
		$txt .= '
							<tr><td class="description">'.$this->GetDescription().'</td></tr>
							<tr><th>Gains</th></tr>
							<tr><td>'.AfficheListePrix($this->Gain).'</td></tr>
							<tr>
								<td>';
	
		if(!$Avancement)
		{
			//on ajoute le boutton ACCEPTER
			$txt .= '<button type="button"
								onclick="window.location=\'index.php?page=quete&amp;action=inscription&amp;num_quete='.$this->GetIDTypeQuete().'\'"' 
			.((count($_SESSION['QueteEnCours']) < quete::NB_QUETE_MAX AND CheckCout($this->Cout, $oJoueur, $oMaison))?
			NULL
			:'disabled=disabled ')
			.'class="quete" >Accepter</button>';
		}else{
			//On ajoute le boutton ANNULER
			$txt .= '<button type="button"
								onclick="window.location=\'index.php?page=quete&amp;action=annule&amp;num_quete='.$this->GetIDTypeQuete().'\'" 
								class="quete" >Annuler</button>';
		}
	
		//On ferme la tableau de la ficher quete.
		$txt .= '
								</td>
							</tr>
						</table>
					</div>';;
	
		return $txt;
	}
	
	//Les GETS
	//========
	public function GetForce(){
		return $this->Force;
	}
	public function GetDefense(){
		return $this->Defense;
	}
	public function GetVie(){
		return $this->Vie;
	}
}
?>