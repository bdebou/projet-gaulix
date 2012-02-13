<?php
class quete{
	
	private $ID_quete,
			$Login, 
			$Position,
			$Vie,
			$VieMax,
			$Reussi, 
			$date_start,
			$date_end,
			$Type,
			$Groupe,
			$Nom,
			$Description,
			$Niveau,
			$GainOr, $GainExperience, $GainPoints,
			$CodeObjet,
			$Force,
			$Duree,
			$DateCombat;
	
	const NB_QUETE_MAX			= 3;					// Nombre maximum de quete autorisée en  meme temp
	
	public function __construct(array $a, array $b){
		$this->hydrate($a, $b);
		if(in_array($this->Type, array('romains'))){$this->UpdateQueteRomains();}
	}
	//On attaque un Monstre de quete
	private function QueteMonstre(personnage &$joueur){
		$ForceMonstre = $this->Force + ($joueur->GetNiveau() * 3);
		$arAttaque = $joueur->GetAttPerso();
		$arDefense = $joueur->GetDefPerso();
		$txt = '<p>Vous avez attaqué "'.$this->Nom.'".';
		if((($arAttaque['0'] + $arAttaque['1']) * 1.15) >= $ForceMonstre){
			//on frappe le monstre car plus fort
			$ViePerdue = intval(($arAttaque['0'] + $arAttaque['1']) * 1.15);
			$this->Vie -= $ViePerdue;
			$txt .= " Il a perdu $ViePerdue pts ".AfficheIcone('vie');
		}else{$txt .= " Il a perdu aucun pts ".AfficheIcone('vie');}
		if(($arDefense['0'] + $arDefense['1']) < $ForceMonstre){
			//On Perd quand meme un peu des pts de vie car le monstre est fort
			$ViePerdueJoueur = $ForceMonstre - ($arDefense['0'] + $arDefense['1']);
			$joueur->PerdreVie($ViePerdueJoueur,'quete');
			$txt .= " mais vous, vous avez perdu $ViePerdueJoueur pts ".AfficheIcone('vie');
		}
		if($this->Vie <= 0){
			$this->FinishQuete($joueur);
			$txt .= " et vous l'avez tué. Bravo!!!";
		}else{
			$this->Position = $this->MonstreFuit($joueur);
			$txt .= " et il s'est enfui.";
		}
		return $txt.'</p>';
	}
	
	public function ActionSurQueteCombat(personnage $joueur){
		switch($this->Type){
			case 'romains':		return $this->QueteRomains($joueur);	break;
			case 'monstre':		return $this->QueteMonstre($joueur);	break;
		}
	}
	//on vérifie la quete après un déplacement.
	public function ActionSurQuete(personnage &$joueur){
		if(	$this->GetCarte() == $joueur->GetCarte()
			AND $this->GetPosition() == $joueur->GetPosition()){
			switch($this->Type){
				case 'recherche':	$this->QueteRecherche($joueur);			break;
				case 'objet':		$this->QueteObjet($joueur);				break;
			}
		}
	}
	Private function QueteRomains(personnage &$Joueur){
		$ForceRomain = $this->Force + ($Joueur->GetNiveau() * 3);
		
		$arAttaque = $Joueur->GetAttPerso();
		$arDefense = $Joueur->GetDefPerso();
		
		$txt = '<p>Vous avez attaqué "'.$this->Nom.'".';
		
		if((($arAttaque['0'] + $arAttaque['1']) * 1.15) >= $ForceRomain){
			//on frappe les romains car plus fort
			$ViePerdue = intval(($arAttaque['0'] + $arAttaque['1']) * 1.15);
			$this->Vie -= $ViePerdue;
			$txt .= " Ils ont perdu $ViePerdue pts ".AfficheIcone('vie');
		}else{$txt .= " Ils ont perdu aucun pts ".AfficheIcone('vie');}
		
		if(($arDefense['0'] + $arDefense['1']) < $ForceRomain){
			//On Perd quand meme un peu des pts de vie car les romains sont forts
			$ViePerdueJoueur = $ForceRomain - ($arDefense['0'] + $arDefense['1']);
			$Joueur->PerdreVie($ViePerdueJoueur,'quete');
			$txt .= " mais vous, vous avez perdu $ViePerdueJoueur pts ".AfficheIcone('vie');
		}
		
		if($this->Vie <= 0){
			$this->FinishQuete($Joueur);
			$txt .= " et vous les avez tués. Bravo!!!";
			$Joueur->UpdateScores(1, 0);
		}else{
			$this->DateCombat = strtotime('now');
			$txt .= ".";
		}
		
		return $txt.'</p>';
	}
	//on update la quete romain en la déplacant
	private function UpdateQueteRomains(){
		if(intval((strtotime('now') - $this->date_start) / 3600) > 0){
			for($i = 0; $i <= intval((strtotime('now') - $this->date_start) / 3600); $i++){
				$this->QueteSeDeplaceUneCase();
			}
			$this->date_start = strtotime('now');
		}
	}
	//La quete se deplace de 1 case dans n'importe direction MAIS jamais sur un batiment
	Private function QueteSeDeplaceUneCase(){
		global $nbLigneCarte, $nbColonneCarte;
		$arCarteNum = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y');
		
		foreach(array('up', 'down', 'left', 'right') as $direction){
			if($this->CheckMove($direction)){$arDirection[] = $direction;}
		}
		
		switch($arDirection[array_rand($arDirection)]){
			case 'up':
				if($this->Position['1'] == 0){
					$this->Position['1'] = $nbLigneCarte;
					$this->Position['0'] = $arCarteNum[(array_search($this->Position['0'], $arCarteNum) - 5)];
				}else{$this->Position['1']--;}
				break;
			case 'left':
				if($this->Position['2'] == 0){
					$this->Position['2'] = $nbColonneCarte;
					$this->Position['0'] = $arCarteNum[(array_search($this->Position['0'], $arCarteNum) - 1)];
				}else{$this->Position['2']--;}
				break;
			case 'down':
				if($this->Position['1'] == $nbLigneCarte){
					$this->Position['1'] = 0;
					$this->Position['0'] = $arCarteNum[(array_search($this->Position['0'], $arCarteNum) + 5)];
				}else{$this->Position['1']++;}
				break;
			case 'right':
				if($this->Position['2'] == $nbColonneCarte){
					$this->Position['2'] = 0;
					$this->Position['0'] = $arCarteNum[(array_search($this->Position['0'], $arCarteNum) + 1)];
				}else{$this->Position['2']++;}
				break;
		}
	}
	Private function CheckMove($direction){
		global $nbLigneCarte, $nbColonneCarte;
		$arCoteCarte = array(	'up'	=>array('a','b','c','d','e'),
								'down'	=>array('u','v','w','x','y'),
								'left'	=>array('a','f','k','p','u'),
								'right'	=>array('e','j','o','t','y'));
		$arPosition = array(	'up'	=>array('x'=>($this->Position[1] - 1), 'y'=>$this->Position[2]),
								'down'	=>array('x'=>($this->Position[1] + 1), 'y'=>$this->Position[2]),
								'left'	=>array('x'=>$this->Position[1], 'y'=>($this->Position[2] - 1)),
								'right'	=>array('x'=>$this->Position[1], 'y'=>($this->Position[2]) + 1));
		
		if(		($direction == 'up'		AND $this->Position[1] == 0)
			OR	($direction == 'down'	AND $this->Position[1] == $nbLigneCarte)
			OR	($direction == 'left'	AND $this->Position[2] == 0)
			OR	($direction == 'right'	AND $this->Position[2] == $nbColonneCarte)){
			if(	(in_array($direction, array('up', 'down'))
					AND ($this->Position[2] == 0
						OR $this->Position[2] == $nbColonneCarte)
				) OR (
				in_array($direction, array('left', 'right'))
					AND ($this->Position[1] == 0
						OR $this->Position[1] == $nbLigneCarte))){
						if(in_array($this->GetCarte(), $arCoteCarte[$direction])){return false;}
			}else{return false;}
		}
		if($this->chkIfBatiment(implode(',', array($this->GetCarte(), $arPosition[$direction]['x'], $arPosition[$direction]['y'])))){return false;}
		//Si non on peut bouger
		return true;
	}
	Private function chkIfBatiment($position){
		$sql = "SELECT coordonnee FROM table_carte WHERE id_type_batiment NOT IN (10, 11, 12, 13, 14, 15, 16, 17) AND detruit IS NULL;";
		$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if($row['coordonnee'] == $position){
				return true;
			}
		}
		return false;
	}
		
	Private function QueteRecherche(personnage &$joueur){
		if(!is_null($this->CodeObjet)){
			$joueur->AddInventaire($this->CodeObjet);
		}
		$_SESSION['message']['quete'] = $this->FinishQuete($joueur);
	}
	Private function QueteObjet(personnage &$joueur){
		$joueur->AddInventaire($this->CodeObjet);
		$_SESSION['message']['quete'] = $this->FinishQuete($joueur);
	}
	private function MonstreFuit(personnage &$joueur){
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
	}
	private function FinishQuete(personnage &$joueur){
		$txt = null;
		$this->date_end = strtotime('now');
		$this->Reussi = true;
		$joueur->AddOr($this->GainOr);
		$joueur->AddExperience($this->GainExperience);
		$joueur->UpdatePoints($this->GainPoints);
		$txt .= 'Bravo! Vous avez ';
		switch($this->Type){
			case 'recherche':
			case 'objet':		$txt .= 'trouvé'; break;
			case 'monstre':
			case 'romains':		$txt .= 'tué'; break;
		}
		$txt .= ' "'.$this->Nom.'" et gagné '.$this->GainOr.' '.AfficheIcone('or').', '.$this->GainExperience.' d\'expérience et '.$this->GainPoints.' points.';
		AddHistory($joueur->GetLogin(), $joueur->GetCarte(), $joueur->GetPosition(), 'quete', $this->Nom, NULL, $txt);
		return $txt;
	}
	//On annule la quete
	public function Cancel(){
		$this->date_end = strtotime('now');
		$this->Reussi = true;
	}
	
	//Création de l'objet
	public function hydrate(array $Quete, array $InfoQuete){
		date_default_timezone_set('Europe/Brussels');
		foreach ($Quete as $key => $value){
			switch ($key){
				case 'id_quete_en_cours':	$this->ID_quete = intval($value); break;
				case 'quete_login':			$this->Login = strval($value); break;
				case 'quete_position':		$this->Position = explode(',', $value); break;
				case 'quete_vie':			$this->Vie = intval($value); break;
				case 'quete_reussi':		$this->Reussi = (is_null($value)?NULL:$value); break;
				case 'date_start':			$this->$key = (is_null($value)?NULL:strtotime($value)); break;
				case 'date_end':			$this->$key = (is_null($value)?NULL:strtotime($value)); break;
				case 'last_combat':			$this->DateCombat = (is_null($value)?NULL:strtotime($value)); break;
			}
		}
		foreach ($InfoQuete as $key => $value){
			switch ($key){
				case 'quete_type':			$this->Type = strval($value); break;
				case 'quete_groupe':		$this->Groupe = (is_null($value)?NULL:strval($value)); break;
				case 'nom':					$this->Nom = strval($value); break;
				case 'description':			$this->Description = (is_null($value)?NULL:strval($value)); break;
				case 'niveau':				$this->Niveau = intval($value); break;
				case 'gain_or':				$this->GainOr = (is_null($value)?NULL:intval($value)); break;
				case 'gain_experience':		$this->GainExperience = (is_null($value)?NULL:intval($value)); break;
				case 'gain_points':			$this->GainPoints = (is_null($value)?NULL:intval($value)); break;
				case 'id_objet':			$this->CodeObjet = (is_null($value)?NULL:strval($value)); break;
				case 'quete_force':			$this->Force = (is_null($value)?NULL:intval($value)); break;
				case 'quete_duree':			$this->Duree = (is_null($value)?NULL:intval($value)); break;
				case 'quete_vie':			$this->VieMax = intval($value); break;
			}
		}
	}
	
	//renvoie les valeurs
	public function GetIDQuete(){			return $this->ID_quete;}
	public function GetVie(){				return $this->Vie;}
	public function GetVieMax(){			return $this->VieMax;}
	public function GetDateStart(){			return $this->date_start;}
	public function GetDateEnd(){			return $this->date_end;}
	public function GetStatus(){			return $this->Reussi;}
	public function GetTypeQuete(){			return $this->Type;}
	public function GetGroupe(){			return $this->Groupe;}
	public function GetNom(){				return $this->Nom;}
	public function GetDescription(){		return $this->Description;}
	public function GetNiveau(){			return $this->Niveau;}
	public function GetGainOr(){			return $this->GainOr;}
	public function GetGainExperience(){	return $this->GainExperience;}
	public function GetGainPoints(){		return $this->GainPoints;}
	public function GetCodeObjet(){			return $this->CodeObjet;}
	public function GetForce(){				return $this->Force;}
	public function GetDuree(){				return $this->Duree;}
	public function GetPosition(){			return array($this->Position['1'], $this->Position['2']);}
	public function GetCarte(){				return $this->Position['0'];}
	public function GetDateCombat(){		return $this->DateCombat;}
}
?>