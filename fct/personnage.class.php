<?php
class personnage{

	private	$id,
			$login,
			$mail,
			$position,
			$vie,
			$val_attaque, $val_defense,
			$argent,
			$experience,
			$niveau,
			$deplacement,
			$last_action,
			$last_combat, 
			$attaque_tour,
			$chk_chasse, 
			$chk_legion, 
			$chk_object, 
			$last_object, 
			$code_casque, $code_arme, $code_bouclier, $code_jambiere, $code_cuirasse, $code_sac,
			$LivreSorts, $LstSorts, 
			$nb_combats, $nb_victoire, $nb_vaincu,
			$arInventaire,
			$date_perf_attaque, $tmp_perf_attaque,
			$date_perf_defense, $tmp_perf_defense,
			$maison_installe,
			$clan,
			$DateLastMessageLu,
			$lstCompetences,
			$NotAttaque, $NotCombat,
			$NbPoints;
			
	Const TAILLE_MINIMUM_BOLGA	= 20;			//La taille minimum du bolga
	Const DUREE_SORT			= 432000;		// Limite de temp pour l'utilisation d'un sort (3600 * 24 *5).
	Const DEPLACEMENT_MAX		= 300;				// Limite le nombre déplacement maximum
	
	//Initialisation de l'objet
	public function __construct(array $donnees){
		global $temp_attente, $nbDeplacement;
		
		$this->hydrate($donnees);
		
		//on vérifie si il a droit à des déplacements
		if(	intval((strtotime('now') - $this->last_action) / $temp_attente) >= 1
			AND $this->deplacement < self::DEPLACEMENT_MAX){
			$this->AddDeplacement($nbDeplacement * intval((strtotime('now') - $this->last_action) / $temp_attente),'new');
		}
		
		//on vérifie si les sorts sont toujours d'actualité
		if(!is_null($this->LstSorts)){
			
			$tmpLst = null;
			
			Foreach($this->LstSorts as $Sort){
				$arSort = explode('=', $Sort);
				if((strtotime('now') - $arSort[1]) <= self::DUREE_SORT){$tmpLst[] = $arSort[0].'='.$arSort[1];}
			}
			$this->LstSorts = $tmpLst;
		}
	}
	//Frapper un autre joueur et les conséquances
	public function frapper(Personnage $persoCible){
		global $lstPoints;
		
		$arAttCible		= $persoCible->GetAttPerso();
		$arDefCible		= $persoCible->GetDefPerso();
		$ValeurCible	= $arAttCible['0'] + $arAttCible['1'] + $arDefCible['0'] + $arDefCible['1'];
		
		$arAtt			= $this->GetAttPerso();
		$arDef			= $this->GetDefPerso();
		$Valeur			= (($arAtt['0'] + $arAtt['1']) * 1.15)+($arDef['0'] + $arDef['1']);
		
		if($Valeur > $ValeurCible){
				//La cible à perdu
			$montant = $persoCible->GetArgent();
			if($persoCible->PerdreVie($Valeur-$ValeurCible,'combat')){
				$this->AddOr($montant);
			}else{
				$montant = intval($persoCible->GetArgent() / 10);
				$persoCible->AddExperience(1);
				$this->AddOr($montant);
				$persoCible->MindOr($montant);
			}
			
			$this->AddExperience(5);
			$this->UpdateScores(1,0);
			$this->UpdatePoints($lstPoints['CombatGagné'][0]);
			
			$persoCible->UpdateScores(0,1);
			$persoCible->UpdatePoints($lstPoints['CombatPerdu'][0]);
			
			return array('Vous avez gagné le combat (+'.$lstPoints['CombatGagné'][0].' points, +5pts d\'expérience et volé '.$montant.' '.AfficheIcone('or').').',
			'Vous avez perdu un combat ('.$lstPoints['CombatPerdu'][0].' points, -'.(intval($Valeur-$ValeurCible)).'pts '.AfficheIcone('vie').', -'.$montant.' '.AfficheIcone('or').' mais +1pt d\'expérience).');
		}elseif($Valeur == $ValeurCible){
				//Match Null
			$persoCible->AddExperience(5);
			$this->AddExperience(5);
			return array('Même valeur de combat donc personne ne gagne, personne ne perd.<br />Vous avez gagné 5 pts d`\'expérience.',
			'Vous avez gagné 5 pts d\'expérience grâce à un combat null.');
		}else{
				//La Cible a gagné
			$montant = $this->GetArgent();
			if($this->PerdreVie($ValeurCible-$Valeur,'combat')){
				$persoCible->AddOr($montant);
			}else{
				$montant = intval($this->GetArgent() / 10);
				$this->AddExperience(1);
				$persoCible->AddOr($montant);
				$this->MindOr($montant);
			}
			
			$persoCible->AddExperience(5);
			$persoCible->UpdateScores(1,0);
			$persoCible->UpdatePoints($lstPoints['CombatGagné'][0]);
			
			$this->UpdateScores(0,1);
			$this->UpdatePoints($lstPoints['CombatPerdu'][0]);
			
			return array('Vous avez perdu le combat ('.$lstPoints['CombatPerdu'][0].' points, -'.intval($ValeurCible-$Valeur).'pts '.AfficheIcone('vie').' et -'.$montant.' '.AfficheIcone('or').').',
			'Vous avez gagné un combat (+'.$lstPoints['CombatGagné'][0].' points, +5pts d\'expérience et volé '.$montant.' '.AfficheIcone('or').').');
		}
	}
	
	//On met à jour les scores après le combat
	public function UpdateScores($Gagner, $Perdu){
		$this->nb_victoire+=$Gagner;
		$this->nb_vaincu+=$Perdu;
		$this->nb_combats++;
	}
	
	//on augment l'espérience
	public function AddExperience($nbExp){
		for($i=1;$i<=$nbExp;$i++){
			if($this->experience < $this->GetMaxExperience()){
				$this->experience++;
			}elseif($this->experience==$this->GetMaxExperience()){
				$this->UpNiveau();
				$this->experience=0;
			}
		}
	}
	private function UpNiveau(){
		global $VieMaximum, $lstPoints;
		if(($VieMaximum - $this->vie) >= 10){
			$tmp = 10;
		}else{
			$tmp = $VieMaximum - $this->vie;
		}
		$this->GagnerVie($tmp);
		$this->niveau++;
		$this->UpdatePoints($lstPoints['NivTerminé'][0]);
	}
	public function PerdreVie($nb, $type){
		switch($type){
			case 'combat':
				$this->vie -= $nb;
				$this->last_combat=strtotime('now');
				break;
			case 'tour': $this->attaque_tour = true;
			case 'chasse':
			case 'druide':
			case 'legionnaire':
			case 'quete':
				$this->vie -= $nb;
				break;
		}
		if($this->vie <= 0){
			$this->Ressuscite();
			return true;
		}else{
			return false;
		}
	}
	public function GagnerVie($nb){
		global $VieMaximum;
		$this->vie += $nb;
		if($this->vie > $VieMaximum){$this->vie = $VieMaximum;}
	}

	Public function DesequiperPerso($typeObject){
		switch($typeObject){
			case 'arme':
				$this->AddInventaire($this->code_arme, $typeObject, null, false);
				$this->code_arme = NULL;
				break;
			case 'bouclier':
				$this->AddInventaire($this->code_bouclier, $typeObject, null, false);
				$this->code_bouclier = NULL;
				break;
			case 'cuirasse':
				$this->AddInventaire($this->code_cuirasse, $typeObject, null, false);
				$this->code_cuirasse = NULL;
				break;
			case 'jambiere':
				$this->AddInventaire($this->code_jambiere, $typeObject, null, false);
				$this->code_jambiere = NULL;
				break;
			case 'casque':
				$this->AddInventaire($this->code_casque, $typeObject, null, false);
				$this->code_casque = NULL;
				break;
			case 'sac':
				$this->AddInventaire($this->code_sac, $typeObject, null, false);
				$this->code_sac = NULL;
				break;
		}
		
	}
	//La gestion de l'or
	public function AddOr($or){
		$this->argent += abs(intval($or));
	}
	public function MindOr($or){
		$this->argent -= abs(intval($or));
	}
	public function ArgentVole(){
		$this->argent = 0;
	}
	
	//les Compétences
	private function CreateListCompetence($list, $lstStatus){
		foreach($list as $cmp){
			$this->lstCompetences[ucfirst($cmp['cmp_lst_nom'])] = null;
		}
		foreach($lstStatus as $cmp){
			if(is_null($cmp['cmp_niveau'])){
				$this->lstCompetences[ucfirst($cmp['cmp_nom'])] = NULL;
			}elseif($this->lstCompetences[ucfirst($cmp['cmp_nom'])] < $cmp['cmp_niveau']){
				$this->lstCompetences[ucfirst($cmp['cmp_nom'])] = $cmp['cmp_niveau'];
			}
		}
	}
	
	public function EquiperPerso($numObject, $typeObject){
		$chk = true;
		switch($typeObject){
			case 'arme':
				if(!is_null($this->code_arme)){$this->AddInventaire($this->code_arme, $typeObject);}
				$this->code_arme = $numObject;
				break;
			case 'bouclier':
				if(!is_null($this->code_bouclier)){$this->AddInventaire($this->code_bouclier, $typeObject);}
				$this->code_bouclier = $numObject;
				break;
			case 'cuirasse':
				if(!is_null($this->code_cuirasse)){$this->AddInventaire($this->code_cuirasse, $typeObject);}
				$this->code_cuirasse = $numObject;
				break;
			case 'jambiere':
				if(!is_null($this->code_jambiere)){$this->AddInventaire($this->code_jambiere, $typeObject);}
				$this->code_jambiere = $numObject;
				break;
			case 'casque':
				if(!is_null($this->code_casque)){$this->AddInventaire($this->code_casque, $typeObject);}
				$this->code_casque = $numObject;
				break;
			case 'sort':
				//if(!is_null($this->code_divers)){$this->AddInventaire($this->code_divers, $typeObject);}
				if($this->LivreSorts == 'NoBook'){
					unset($this->LstSorts);
					$this->LstSorts[] = $numObject.'='.strtotime('now');
				}else{
					$sql = "SELECT objet_nb FROM table_objets WHERE objet_code='".strval($this->LivreSorts)."';";
					$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
					$row = mysql_fetch_array($requete, MYSQL_ASSOC);
					if(count($this->LstSorts) < $row['objet_nb']){
						$this->LstSorts[] = $numObject.'='.strtotime('now');
					}else{
						$chk = false;
					}
				}
				break;
			case 'livre':
				if(!is_null($this->LivreSorts) AND $this->LivreSorts != 'NoBook'){$this->AddInventaire($this->LivreSorts, $typeObject);}
				$this->LivreSorts = $numObject;
				break;
			case 'sac':
				if(!is_null($this->code_sac)){$this->AddInventaire($this->code_sac, $typeObject);}
				$this->code_sac = $numObject;
				break;
		}
		if($chk){$this->CleanInventaire($numObject);}
	}
	public function ReduireTempDeplacement($nbTime){
		$this->last_action -= $nbTime;
	}
	
	public function InscriptionClan($nom){
		$this->clan = $nom;
	}
	public function DesinscriptionClan(){
		$this->clan = null;
	}
	
	public function VendreObjet($NumObject, $PrixObject){
		$this->argent+=$PrixObject;
		$this->CleanInventaire($NumObject);
	}
	
	public function UpdatePoints($nb){
		$this->NbPoints += $nb;
	}
	
	//Gestion du bolga
	public function AddInventaire($codeObjet, $typeObjet = 'ObjetPouvantEtreGroupé', $nbObjet = 1, $chkLast = true){
		$chk = false;
		//$lstRes = array('ResBoi', 'ResNou', 'ResPie', 'ResVie', 'ResDep');
		//$lstBri = array('sac', 'arme', 'bouclier', 'jambiere', 'casque', 'cuirasse');
		
		//la structure est type1=nb1,type2=nb2 (exemple : cuir=6,longbaton=3)
		if(!is_null($this->arInventaire)){
			foreach($this->arInventaire as $key=>$element){
				$arTemp = explode('=', $element);
				if(	$arTemp['0'] == $codeObjet AND $this->CheckSiObjetPeutEtreGroupe($codeObjet, $typeObjet)){
					$arTemp['1'] += $nbObjet;
					$this->arInventaire[$key] = implode('=', $arTemp);
					$chk = true;
					break;
				}
			}
		}
		if(!$chk){$this->arInventaire[] = $codeObjet.'='.$nbObjet;}
		if($chkLast){$this->SetLastObject(true,null);}
	}
	public function CleanInventaire($CodeObject, $full = false, $nb = 1){
		$chk = true;
		$temp = null;
		if(!is_null($this->arInventaire)){
			foreach($this->arInventaire as $objet){
				$arObjet = explode('=', $objet);
				if($arObjet['0']==$CodeObject and $chk){
					if($full){
						$arObjet['1'] = 0;
					}else{
						$arObjet['1'] -= $nb;
					}
					if($arObjet['1'] > 0){$temp[] = implode('=', $arObjet);}
					$chk = false;
				}else{
					$temp[] = implode('=', $arObjet);
				}
			}
			$this->arInventaire=$temp;
		}
	}
	public function AssezElementDansBolga($CodeResBesoin, $NbResBesoin) {
		if (!is_null($this->arInventaire)) {
			foreach ($this->arInventaire as $Element) {
				$arElement = explode('=', $Element);
				if ($arElement[0] == $CodeResBesoin AND $arElement[1] >= $NbResBesoin) {
					return true;
				}
			}
		}
		return false;
	}
	public function CheckSiObjetPeutEtreGroupe($code = null, $type = null) {
		//les  ressource ne peuvent pas etre groupées
		if (!is_null($code) AND in_array(substr($code, 0, 6), array('ResBoi', 'ResNou', 'ResPie', 'ResVie', 'ResDep'))) {
			return false;
		}
	
		//les objets de type armes et autre de combats ne peuvents pas etre grouppés
		if (!is_null($type) AND in_array($type, array('sac', 'arme', 'bouclier', 'jambiere', 'casque', 'cuirasse', 'livre', 'sort', 'potion'))) {
			return false;
		}
		//Autrement oui
		return true;
	}
	public function QuelCapaciteMonBolga() {
	    //Est ce que le joueur possède un sac?
	    if (!is_null($this->code_sac)) {

	        $sql = "SELECT objet_attaque FROM table_objets WHERE objet_code='" . strval($this->code_sac) . "';";
	        $requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	        $result = mysql_fetch_array($requete, MYSQL_ASSOC);
	        
	        return $result['objet_attaque'];
	        
	    } else {
	    	
	        return self::TAILLE_MINIMUM_BOLGA;
	    }
	}

	public function LaunchPerfAttaque($tmp, $prix, $step){
		switch($step){
			case '1':
				$this->date_perf_attaque=strtotime('now');
				$this->argent -= abs($prix);
				break;
			case '2':
				$this->date_perf_attaque=null;
				$this->val_attaque++;
				break;
		}
		$this->tmp_perf_attaque=$tmp;
	}
	public function LaunchPerfDefense($tmp, $prix, $step){
		switch($step){
			case '1':
				$this->date_perf_defense=strtotime('now');
				$this->argent -= abs($prix);
				break;
			case '2':
				$this->date_perf_defense=null;
				$this->val_defense++;
				break;
		}
		$this->tmp_perf_defense=$tmp;
	}
	public function AddDeplacement($nbDep, $type){
		
		if($nbDep > 1){
			for($i=1; $i<=$nbDep; $i++){
				if($this->deplacement == self::DEPLACEMENT_MAX){break;}
				$this->deplacement++;
			}
		}elseif($nbDep == 1 AND $this->deplacement < self::DEPLACEMENT_MAX){
			$this->deplacement++;
		}
		
		if($type != 'objet'){$this->last_action=strtotime('now');}
	}
	public function MaisonInstalle($coordonnee){
		$this->maison_installe = explode(',', $coordonnee);
	}
	public function MaisonDetruit(){
		$this->maison_installe = null;
	}
	
	public function SetLastObject($valueChk,$valueLastObject){
		$this->chk_object=$valueChk;
		$this->last_object=$valueLastObject;
	}
	Public function SetChasse($ValueChk){
		$this->chk_chasse = $ValueChk;
	}
	public function SetLegionnaire($ValueChk){
		$this->chk_legion = $ValueChk;
	}
	
	//Les mouvements
	public function UpdatePosition(array $NewPosition){
		$this->position = $NewPosition;
	}
	public function deplacer($direction){
		global $nbLigneCarte, $nbColonneCarte;
		$arCarteNum = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y');
		switch($direction){
			case 'up':
				if($this->position['1'] == 0){
					$this->position['1'] = $nbLigneCarte;
					$this->position['0'] = $arCarteNum[(array_search($this->position['0'], $arCarteNum) - 5)];
				}else{$this->position['1']--;}
				break;
			case 'left':
				if($this->position['2'] == 0){
					$this->position['2'] = $nbColonneCarte;
					$this->position['0'] = $arCarteNum[(array_search($this->position['0'], $arCarteNum) - 1)];
				}else{$this->position['2']--;}
				break;
			case 'down':
				if($this->position['1'] == $nbLigneCarte){
					$this->position['1'] = 0;
					$this->position['0'] = $arCarteNum[(array_search($this->position['0'], $arCarteNum) + 5)];
				}else{$this->position['1']++;}
				break;
			case 'right':
				if($this->position['2'] == $nbColonneCarte){
					$this->position['2'] = 0;
					$this->position['0'] = $arCarteNum[(array_search($this->position['0'], $arCarteNum) + 1)];
				}else{$this->position['2']++;}
				break;
		}
		$this->deplacement--;
		$this->SetLastObject(false, null);
		$this->SetChasse(false);
		$this->SetLegionnaire(false);
		$this->attaque_tour = false;
		$this->AddExperience(1);
	}
	
	public function CheckMove($direction){
		global $nbLigneCarte, $nbColonneCarte;
		$arCoteCarte = array(	'up'	=>array('a','b','c','d','e'),
								'down'	=>array('u','v','w','x','y'),
								'left'	=>array('a','f','k','p','u'),
								'right'	=>array('e','j','o','t','y'));
		$arPosition = array(	'up'	=>array('x'=>($this->position[1] - 1), 'y'=>$this->position[2]),
								'down'	=>array('x'=>($this->position[1] + 1), 'y'=>$this->position[2]),
								'left'	=>array('x'=>$this->position[1], 'y'=>($this->position[2] - 1)),
								'right'	=>array('x'=>$this->position[1], 'y'=>($this->position[2]) + 1));
		
		if(		($direction == 'up'		AND $this->position[1] == 0)
			OR	($direction == 'down'	AND $this->position[1] == $nbLigneCarte)
			OR	($direction == 'left'	AND $this->position[2] == 0)
			OR	($direction == 'right'	AND $this->position[2] == $nbColonneCarte)){
			if(	(in_array($direction, array('up', 'down'))
					AND ($this->position[2] == 0
						OR $this->position[2] == $nbColonneCarte)
				) OR (
				in_array($direction, array('left', 'right'))
					AND ($this->position[1] == 0
						OR $this->position[1] == $nbLigneCarte))){
						if(in_array($this->GetCarte(), $arCoteCarte[$direction])){return false;}
			}else{return false;}
		}
		
		//a t on encore assez de déplacement?
		if($this->deplacement<=0){return false;}
		//Y a t il un mur ou une tour?
		if($this->chkIfMur(implode(',', array($this->GetCarte(), $arPosition[$direction]['x'], $arPosition[$direction]['y'])))){return false;}
		//Si non on peut bouger
		return true;
	}
	Private function chkIfMur($position){
		$TypeBatimentBloquant = array('mur'=>2, 'tour'=>3);
		$sql = "SELECT coordonnee, login 
				FROM table_carte 
				WHERE 
					login NOT IN ('".implode("', '", ListeMembreClan($this->clan))."') 
					AND id_type_batiment IN (".implode(', ', $TypeBatimentBloquant).") 
					AND detruit IS NULL;";
		$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			//if($row['coordonnee'] == $position AND $row['login'] != $this->login){
			if($row['coordonnee'] == $position){
				return true;
			}
		}
		return false;
	}
	//Ressuscite le joueur
	private function Ressuscite(){
		global $lstPoints;
		
		$this->position = $this->maison_installe;
		$this->vie = 100;
		$this->val_attaque = 10;
		$this->val_defense = 10;
		$this->argent = 0;
		$this->experience = 0;
		$this->niveau = 0;
		$this->deplacement = 10;
		$this->last_action = strtotime('now');
		$this->last_combat = null;
		$this->attaque_tour = false;
		$this->chk_chasse = null;
		$this->chk_object = null;
		$this->last_object = null;
		$this->code_casque = null;
		$this->code_arme = null;
		$this->code_bouclier = null;
		$this->code_jambiere = null;
		$this->code_cuirasse = null;
		$this->code_sac = null;
		$this->code_divers = null;
		$this->arInventaire = null;
		$this->date_perf_attaque = null;
		$this->tmp_perf_attaque = null;
		$this->date_perf_defense = null;
		$this->tmp_perf_defense = null;
		$this->UpdatePoints($lstPoints['PersoTué'][0]);
		
		ResetListeQuetes($this->GetLogin());
		
		AddHistory($this->GetLogin(), $this->GetCarte(), $this->GetPosition(), 'ressucite', '', NULL, 'Vous êtes mort. Retour à la maison.');
	}
	
	Private function ListeCodesEquipement(){
		$tmp = null;
		foreach (array('code_arme', 'code_bouclier', 'code_cuirasse', 'code_jambiere', 'code_casque') as $Type){
			if(!is_null($this->$Type)){
				$tmp[] = $this->$Type;
			}
		}
		
		return $tmp;
	}
	Private function ValeurDesSorts($type){
			//on initialise la valeur des équipements
		$ValSort = 0;
		
		if(!is_null($this->LstSorts)){
			foreach($this->LstSorts as $Sort){
				$arSort = explode('=', $Sort);
		
				$sql = "SELECT ".$type." FROM table_objets WHERE objet_code='".$arSort[0]."';";
				$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
		
				while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
					$ValSort += intval($row[$type]);
				}
			}
		}
		return $ValSort;
	}
	Private function ValeurEquipements($type){
			//on crée la liste des codes des équipements
		$lstCodes = $this->ListeCodesEquipement();
		
			//on initialise la valeur des équipements
		$ValeurEquipement = 0;
		
		if(count($lstCodes) > 0){
			$sql = "SELECT ".$type." FROM table_objets WHERE objet_code IN ('".implode("', '", $lstCodes)."');";
			$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
				
			while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
				$ValeurEquipement += intval($row[$type]);
			}
		}
		return $ValeurEquipement;
	} 
	//Notifications
	public function SetNotification($type, $value){
		switch($type){
			case 'combat':	$this->NotCombat = $value;	break;
			case 'attaque':	$this->NotAttaque = $value;	break;
		}
	}
	public function SetLastMessageLu(){
		$this->DateLastMessageLu = strtotime('now');
	}
	
	//Remplir l'objet joueur
	public function hydrate(array $donnees){
		date_default_timezone_set('Europe/Brussels');
		foreach ($donnees as $key => $value){
			switch ($key){
				case 'login':				$this->$key = strval($value); break;
				case 'mail':				$this->$key = strval($value); break;
				case 'position':			$this->$key = explode(',', $value); break;
				case 'vie':					$this->$key = intval($value); break;
				case 'val_attaque':			$this->$key = intval($value); break;
				case 'val_defense':			$this->$key = intval($value); break;
				case 'experience':			$this->$key = intval($value); break;
				case 'niveau':				$this->$key = intval($value); break;
				case 'deplacement':			$this->$key = intval($value); break;
				case 'last_action':			$this->$key = strtotime($value); break;
				case 'date_last_combat':	$this->last_combat = strtotime($value); break;
				case 'attaque_tour':		$this->$key = (is_null($value)?false:true); break;
				case 'chk_chasse':			$this->$key = (is_null($value)?false:true); break;
				case 'chk_object':			$this->$key = (is_null($value)?false:true); break;
				case 'chk_legion':			$this->$key = (is_null($value)?false:true); break;
				case 'last_object':			$this->$key = (is_null($value)?NULL:strval($value)); break;
				case 'code_casque':			$this->$key = (is_null($value)?NULL:strval($value)); break;
				case 'code_arme':			$this->$key = (is_null($value)?NULL:strval($value)); break;
				case 'code_bouclier':		$this->$key = (is_null($value)?NULL:strval($value)); break;
				case 'code_jambiere':		$this->$key = (is_null($value)?NULL:strval($value)); break;
				case 'code_cuirasse':		$this->$key = (is_null($value)?NULL:strval($value)); break;
				case 'code_sac':			$this->$key = (is_null($value)?NULL:strval($value)); break;
				case 'nb_combats':			$this->$key = intval($value); break;
				case 'nb_victoire':			$this->$key = intval($value); break;
				case 'nb_vaincu':			$this->$key = intval($value); break;
				case 'inventaire':			$this->arInventaire = (is_null($value)?null:explode(',', $value)); break;
				case 'id':					$this->$key = intval($value); break;
				case 'argent':				$this->$key = intval($value); break;
				case 'date_perf_attaque':	$this->$key = (is_null($value)?NULL:strtotime($value)); break;
				case 'tmp_perf_attaque':	$this->$key = (is_null($value)?NULL:intval($value)); break;
				case 'date_perf_defense':	$this->$key = (is_null($value)?NULL:strtotime($value)); break;
				case 'tmp_perf_defense':	$this->$key = (is_null($value)?NULL:intval($value)); break;
				case 'maison_installe':		$this->$key = (is_null($value)?NULL:explode(',', $value)); break;
				case 'clan':				$this->$key = (is_null($value)?NULL:htmlspecialchars_decode($value, ENT_QUOTES)); break;
				case 'date_last_msg_lu':	$this->DateLastMessageLu = strtotime($value); break;
				case 'not_attaque':			$this->NotAttaque = (is_null($value)?false:true); break;
				case 'not_combat':			$this->NotCombat = (is_null($value)?false:true); break;
				case 'nb_points':			$this->NbPoints = intval($value); break;
				case 'livre_sorts':
					if(is_null($value)){
						$this->LivreSorts = 'NoBook';
						$this->LstSorts = null;
					}else{
						$artmp = explode(',', $value);
						$this->LivreSorts = $artmp[0];
						unset($artmp[0]);
						if(count($artmp) > 0){
							$this->LstSorts = $artmp;
						}else{
							$this->LstSorts = null;
						}
					}
					break;
			}
		}
		
		//on crée la liste des compétences
		$sqlLstCmp = "SELECT cmp_lst_nom FROM `table_competence_lst` WHERE `cmp_lst_type` = 'competence' ORDER BY cmp_lst_nom ASC;";
		$rqtLstCmp = mysql_query($sqlLstCmp) or die (mysql_error().'<br />'.$sqlLstCmp);
		if(mysql_num_rows($rqtLstCmp) > 0){
			while($item = mysql_fetch_array($rqtLstCmp, MYSQL_ASSOC)){
				$lst[] = $item;
			}
		}
		//On récupère les infos à propos des compétences
		$sqlCmp = "SELECT cmp_nom, cmp_niveau FROM table_competence WHERE cmp_login='".$this->login."' AND cmp_finish=1;";
		$rqtCmp = mysql_query($sqlCmp) or die (mysql_error().'<br />'.$sqlCmp);
		if(mysql_num_rows($rqtCmp) > 0){
			while($cmp = mysql_fetch_array($rqtCmp, MYSQL_ASSOC)){
				$status[] = $cmp;
			}
			//$this->UpdateCompetences($competences);
		}
		if(mysql_num_rows($rqtLstCmp) > 0 AND mysql_num_rows($rqtCmp) > 0){
			$this->CreateListCompetence($lst, $status);
		}
		
		
	}
	// -------------------- GET info ----------------------
	public Function GetCombienElementDansBolga($CodeResBesoin) {
		if (!is_null($this->arInventaire)){
			foreach ($this->arInventaire as $Element) {
				$arElement = explode('=', $Element);
				if ($arElement['0'] == $CodeResBesoin) {
					return $arElement['1'];
				}
			}
		}
		return 0;
	}
	public function GetLstInventaire(){
		if(!is_null($this->arInventaire)){return $this->arInventaire;}
		else{return NULL;}
	}
	public function GetAttPerso(){
			//on initialise la valeur d'attaque des équipements		
		$val_attaque_objet = 0;
		
			//on ajoute la valeur des équipements
		$val_attaque_objet += $this->ValeurEquipements('objet_attaque');
		
			//on ajoute la valeur des sorts
		$val_attaque_objet += $this->ValeurDesSorts('objet_attaque');
		
		return array($this->val_attaque, $val_attaque_objet);
	}
	public function GetDefPerso(){
			//on initialise la valeur d'attaque des équipements
		$val_defense_objet = 0;
		
			//on ajoute la valeur des équipements
		$val_defense_objet += $this->ValeurEquipements('objet_defense');
		
			//on ajoute la valeur des sorts
		$val_defense_objet += $this->ValeurDesSorts('objet_defense');
		
		return array($this->val_defense, $val_defense_objet);
	}
	public function GetDisPerso(){
			//on initialise la valeur d'attaque des équipements		
		$val_distance_objet = 0;
		
			//on ajoute la valeur des équipements
		$val_distance_objet += $this->ValeurEquipements('objet_distance');
		
			//on ajoute la valeur des sorts
		$val_distance_objet += $this->ValeurDesSorts('objet_distance');
		
		return $val_distance_objet;
	}
	public function GetNbCombats(){			return $this->nb_combats;}
	public function GetExpPerso(){			return $this->experience;}
	public function GetNiveau(){			return $this->niveau;}
	public function GetDepDispo(){			return $this->deplacement;}
	public function GetCasque(){			return $this->code_casque;}
	public function GetArme(){				return $this->code_arme;}
	public function GetBouclier(){			return $this->code_bouclier;}
	public function GetJambiere(){			return $this->code_jambiere;}
	public function GetCuirasse(){			return $this->code_cuirasse;}
	public function GetSac(){				return $this->code_sac;}
	public function GetLivre(){				return $this->LivreSorts;}
	public function GetLstSorts(){			return $this->LstSorts;}
	public function GetNbVictoire(){		return $this->nb_victoire;}
	public function GetNbVaincu(){			return $this->nb_vaincu;}
	public function GetId(){				return $this->id;}
	public function GetArgent(){			return $this->argent;}
	public function GetDatePerfAttaque(){	return $this->date_perf_attaque;}
	public function GetTmpPerfAttaque(){	return $this->tmp_perf_attaque;}
	public function GetDatePerfDefense(){	return $this->date_perf_defense;}
	public function GetTmpPerfDefense(){	return $this->tmp_perf_defense;}
	public function GetLastAction(){		return $this->last_action;}
	public function GetLastCombat(){		return $this->last_combat;}
	public function GetChkChasse(){			return $this->chk_chasse;}
	public function GetChkObject(){			return $this->chk_object;}
	public function GetChkLegionnaire(){	return $this->chk_legion;}
	public function GetLastObject(){		return $this->last_object;}
	public function GetLogin(){				return $this->login;}
	public function GetMail(){				return $this->mail;}
	public function GetVie(){				return $this->vie;}
	public function GetMaisonInstalle(){	return $this->maison_installe;}
	public function GetAttaqueTour(){		return $this->attaque_tour;}
	public function GetClan(){				return $this->clan;}
	public function GetPosition(){			return array($this->position['1'], $this->position['2']);}
	public function GetCarte(){				return $this->position['0'];}
	public function GetCoordonnee(){		return implode(',', $this->position);}
	public function GetMaxExperience(){		return (($this->niveau + 1) * 100);}
	public function GetNotifCombat(){		return $this->NotCombat;}
	public function GetNotifAttaque(){		return $this->NotAttaque;}
	public function GetNbPoints(){			return $this->NbPoints;}
	//Les Compétences
	public function GetNiveauCompetence($competence){
		return $this->lstCompetences[ucfirst($competence)];
	}
	public function GetDateLasMessageLu(){	return $this->DateLastMessageLu;}

}
?>