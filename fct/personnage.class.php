<?php
class personnage{

	private	$id;
	private $login;
	private $mail;
	private $position,
			$vie,
			$civilisation,
			$village,
			$carriere,
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
			$nb_combats, $nb_victoire, $nb_vaincu, $nb_mort, 
			$arInventaire,
			$date_perf_attaque, $tmp_perf_attaque,
			$date_perf_defense, $tmp_perf_defense,
			$maison_installe,
			$clan,
			$DateLastMessageLu,
			$lstCompetences, 
			$lstTypeCompetences, 
			$not_attaque, $not_combat,
			$nb_points;
			
	Const TAILLE_MINIMUM_BOLGA		= 20;			//La taille minimum du bolga
	Const DUREE_SORT				= 432000;		// Limite de temp pour l'utilisation d'un sort (3600 * 24 *5).
	Const DEPLACEMENT_MAX			= 300;			// Limite le nombre d�placement maximum
	Const TEMP_DEPLACEMENT_SUP		= 3600;			// Temp d'attente pour avoir de nouveau du d�placement
	Const NB_DEPLACEMENT_SUP		= 1;			// Nombre de point de d�placement gagn� tout les x temp
	Const VIE_MAX					= 300;			// Limite de vie maximum
	Const TAUX_ATTAQUANT			= 1.15;			// Taux d'augmentation de l'attaquant
	Const TAUX_VOL_ARGENT			= 0.10;			// Taux pour le montant de vol d'argent lors d'un combat perdu
	
	const TYPE_RES_MONNAIE			= 'Sesterce';
	const TYPE_COMPETENCE			= 'Comp�tence';
	
	Const TYPE_PERFECT_ATTAQUE		= 'Attaque';
	Const TYPE_PERFECT_DEFENSE		= 'Defense';
	
	const CIVILISATION_ROMAIN		= 'Romains';
	const CIVILISATION_GAULOIS		= 'Gaulois';
	
	//Les points
	const POINT_COMBAT				= 10;
	const POINT_NIVEAU_TERMINE		= 15;
	const POINT_COMPETENCE_TERMINE	= 5;
	const POINT_OBJET_FABRIQUE		= 1;
	const POINT_PERSO_TUE			= 150;
	
	//Initialisation de l'objet
	public function __construct(array $donnees){
		$this->hydrate($donnees);
		
		//on v�rifie si il a droit � des d�placements
		if(	intval((strtotime('now') - $this->last_action) / self::TEMP_DEPLACEMENT_SUP) >= 1
			AND $this->deplacement < self::DEPLACEMENT_MAX){
			$this->AddDeplacement(self::NB_DEPLACEMENT_SUP * intval((strtotime('now') - $this->last_action) / self::TEMP_DEPLACEMENT_SUP),'new');
		}
		
		//on v�rifie si les sorts sont toujours d'actualit�
		if(!is_null($this->LstSorts)){
			
			$tmpLst = null;
			
			Foreach($this->LstSorts as $Sort){
				$arSort = explode('=', $Sort);
				if((strtotime('now') - $arSort[1]) <= self::DUREE_SORT){$tmpLst[] = $arSort[0].'='.$arSort[1];}
			}
			$this->LstSorts = $tmpLst;
		}
		
	}
	//Frapper un autre joueur et les cons�quances
	public function frapper(Personnage $persoCible){
		
		$arAttCible		= $persoCible->GetAttPerso();
		$arDefCible		= $persoCible->GetDefPerso();
		$ValeurCible	= $arAttCible['0'] + $arAttCible['1'] + $arDefCible['0'] + $arDefCible['1'];
		
		$arAtt			= $this->GetAttPerso();
		$arDef			= $this->GetDefPerso();
		$Valeur			= (($arAtt['0'] + $arAtt['1']) * self::TAUX_ATTAQUANT)+($arDef['0'] + $arDef['1']);
		
		if($Valeur > $ValeurCible){
				//La cible � perdu
			$montant = $persoCible->GetArgent();
			
			if($persoCible->PerdreVie($Valeur-$ValeurCible,'combat')){
				$this->AddOr($montant);
			}else{
				$montant = intval($this->GetTauxVolArgent() * $persoCible->GetArgent());
				$persoCible->AddExperience(1);
				$this->AddOr($montant);
				$persoCible->MindOr($montant);
			}
			
			$this->AddExperience(5);
			$this->UpdateScores(1,0);
			$this->UpdatePoints(abs(self::POINT_COMBAT));
			
			$persoCible->UpdateScores(0,1);
			$persoCible->UpdatePoints((abs(self::POINT_COMBAT) * -1));
			
			return array('Vous avez gagn� le combat (+'.abs(self::POINT_COMBAT).' points, +5pts d\'exp�rience et vol� '.$montant.' '.AfficheIcone(self::TYPE_RES_MONNAIE).').',
			'Vous avez perdu un combat ('.(abs(self::POINT_COMBAT) * -1).' points, -'.(intval($Valeur-$ValeurCible)).'pts '.AfficheIcone('vie').', -'.$montant.' '.AfficheIcone(self::TYPE_RES_MONNAIE).' mais +1pt d\'exp�rience).');
		}elseif($Valeur == $ValeurCible){
				//Match Null
			$persoCible->AddExperience(5);
			$this->AddExperience(5);
			return array('M�me valeur de combat donc personne ne gagne, personne ne perd.<br />Vous avez gagn� 5 pts d`\'exp�rience.',
			'Vous avez gagn� 5 pts d\'exp�rience gr�ce � un combat null.');
		}else{
				//La Cible a gagn�
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
			$persoCible->UpdatePoints(abs(self::POINT_COMBAT));
			
			$this->UpdateScores(0,1);
			$this->UpdatePoints((abs(self::POINT_COMBAT) * -1));
			
			return array('Vous avez perdu le combat ('.(abs(self::POINT_COMBAT) * -1).' points, -'.intval($ValeurCible-$Valeur).'pts '.AfficheIcone('vie').' et -'.$montant.' '.AfficheIcone(self::TYPE_RES_MONNAIE).').',
			'Vous avez gagn� un combat (+'.abs(self::POINT_COMBAT).' points, +5pts d\'exp�rience et vol� '.$montant.' '.AfficheIcone(self::TYPE_RES_MONNAIE).').');
		}
	}
	
	//On met � jour les scores apr�s le combat
	public function UpdateScores($Gagner, $Perdu){
		$this->nb_victoire+=$Gagner;
		$this->nb_vaincu+=$Perdu;
		$this->nb_combats++;
	}
	
	//on augment l'esp�rience
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
		if((self::VIE_MAX - $this->vie) >= 10){
			$tmp = 10;
		}else{
			$tmp = self::VIE_MAX - $this->vie;
		}
		$this->GagnerVie($tmp);
		$this->niveau++;
		$this->UpdatePoints(abs(self::POINT_NIVEAU_TERMINE));
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
		$this->vie += $nb;
		if($this->vie > self::VIE_MAX){$this->vie = self::VIE_MAX;}
	}

	Public function DesequiperPerso($typeObject){
		switch($typeObject){
			case 'arme':
				$this->AddInventaire($this->code_arme, 1, false);
				$this->code_arme = NULL;
				break;
			case 'bouclier':
				$this->AddInventaire($this->code_bouclier, 1, false);
				$this->code_bouclier = NULL;
				break;
			case 'cuirasse':
				$this->AddInventaire($this->code_cuirasse, 1, false);
				$this->code_cuirasse = NULL;
				break;
			case 'jambiere':
				$this->AddInventaire($this->code_jambiere, 1, false);
				$this->code_jambiere = NULL;
				break;
			case 'casque':
				$this->AddInventaire($this->code_casque, 1, false);
				$this->code_casque = NULL;
				break;
			case 'sac':
				$this->AddInventaire($this->code_sac, 1, false);
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
	
	//les Comp�tences
	private function CreateListCompetence($list, $lstStatus = array(NULL)){
		
		foreach($list as $cmp){
			if(in_array($cmp[0], $lstStatus))
			{
				$this->lstCompetences[$cmp[0]] = true;
			}else
			{
				$this->lstCompetences[$cmp[0]] = false;
			}
			
			$this->lstTypeCompetences[$cmp[1]][] = $cmp[0];
			
		}
		
		/* foreach($lstStatus as $cmp){
			$this->lstCompetences[$cmp[1]][$cmp[0]] = true;
		} */
	}
	
	public function EquiperPerso($numObject, $typeObject){
		$chk = true;
		switch($typeObject){
			case objArmement::TYPE_ARME:
				if(!is_null($this->code_arme)){$this->AddInventaire($this->code_arme, 1, false);}
				$this->code_arme = $numObject;
				break;
			case objArmement::TYPE_BOUCLIER:
				if(!is_null($this->code_bouclier)){$this->AddInventaire($this->code_bouclier, 1, false);}
				$this->code_bouclier = $numObject;
				break;
			case objArmement::TYPE_CUIRASSE:
				if(!is_null($this->code_cuirasse)){$this->AddInventaire($this->code_cuirasse, 1, false);}
				$this->code_cuirasse = $numObject;
				break;
			case objArmement::TYPE_JAMBIERE:
				if(!is_null($this->code_jambiere)){$this->AddInventaire($this->code_jambiere, 1, false);}
				$this->code_jambiere = $numObject;
				break;
			case objArmement::TYPE_CASQUE:
				if(!is_null($this->code_casque)){$this->AddInventaire($this->code_casque, 1, false);}
				$this->code_casque = $numObject;
				break;
			case objDivers::TYPE_SORT:
				//if(!is_null($this->code_divers)){$this->AddInventaire($this->code_divers, 1, false);}
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
			case objDivers::TYPE_LIVRE:
				if(!is_null($this->LivreSorts) AND $this->LivreSorts != 'NoBook'){$this->AddInventaire($this->LivreSorts, 1, false);}
				$this->LivreSorts = $numObject;
				break;
			case objDivers::TYPE_SAC:
				if(!is_null($this->code_sac)){$this->AddInventaire($this->code_sac, 1, false);}
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
		$this->nb_points += $nb;
	}
	
	//Gestion du bolga
	public function AddInventaire($codeObjet, $nbObjet = 1, $chkLast = true){
		$chk = false;
		
		if(strtolower($codeObjet) == strtolower(personnage::TYPE_RES_MONNAIE))
		{
			$this->AddOr($nbObjet);
		}else{
			//la structure est type1=nb1,type2=nb2 (exemple : cuir=6,longbaton=3)
			if(!is_null($this->arInventaire))
			{
				foreach($this->arInventaire as $key=>$element)
				{
					$arTemp = explode('=', $element);
					if(	$arTemp['0'] == $codeObjet/*  AND $this->CheckSiObjetPeutEtreGroupe($codeObjet, $typeObjet) */)
					{
						$arTemp['1'] += $nbObjet;
						$this->arInventaire[$key] = implode('=', $arTemp);
						$chk = true;
						break;
					}
				}
			}
			
			if(!$chk)
			{
				$this->arInventaire[] = $codeObjet.'='.$nbObjet;
			}
		}
		
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
		//les  ressource ne peuvent pas etre group�es
		if (!is_null($code) AND in_array(substr($code, 0, 6), array('ResBoi', 'ResNou', 'ResPie', 'ResVie', 'ResDep'))) {
			return false;
		}
	
		//les objets de type armes et autre de combats ne peuvents pas etre groupp�s
		if (!is_null($type) AND in_array($type, array('sac', 'arme', 'bouclier', 'jambiere', 'casque', 'cuirasse', 'livre', 'sort', 'potion'))) {
			return false;
		}
		//Autrement oui
		return true;
	}
	public function QuelCapaciteMonBolga() {
	    //Est ce que le joueur poss�de un sac?
	    if (!is_null($this->code_sac)) {

	        $sql = "SELECT objet_attaque FROM table_objets WHERE objet_code='" . strval($this->code_sac) . "';";
	        $requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
	        $result = mysql_fetch_array($requete, MYSQL_ASSOC);
	        
	        return $result['objet_attaque'];
	        
	    } else {
	    	
	        return self::TAILLE_MINIMUM_BOLGA;
	    }
	}

	public function LaunchPerfectionnement($type, $tmp, $prix, $step){
		switch($step){
			case '1':
				switch($type)
				{
					case self::TYPE_PERFECT_ATTAQUE:
						$this->date_perf_attaque=strtotime('now');
						break;
					case self::TYPE_PERFECT_DEFENSE:
						$this->date_perf_defense=strtotime('now');
						break;
				}
				$this->argent -= abs($prix);
				break;
			case '2':
				switch($type)
				{
					case self::TYPE_PERFECT_ATTAQUE:
						$this->date_perf_attaque=null;
						$this->val_attaque++;
						break;
					case self::TYPE_PERFECT_DEFENSE:
						$this->date_perf_defense=null;
						$this->val_defense++;
						break;
				}
				break;
		}
		switch($type)
		{
			case self::TYPE_PERFECT_ATTAQUE:
				$this->tmp_perf_attaque = $tmp;
				break;
			case self::TYPE_PERFECT_DEFENSE:
				$this->tmp_perf_defense = $tmp;
				break;
		}
				
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
			OR	($direction == 'right'	AND $this->position[2] == $nbColonneCarte))
			{
				if(in_array($this->GetCarte(), $arCoteCarte[$direction]))
				{
					return false;
				}
			}
		
		//a t on encore assez de d�placement?
		if($this->deplacement <= 0)
		{
			return false;
		}
		
		$PositionAVerifier = implode(',', array($this->GetCarte(), $arPosition[$direction]['x'], $arPosition[$direction]['y']));

		//Y a t il un mur ou une tour?
		if($this->chkIfBatimentBloquant($PositionAVerifier)){return false;}
		
		//On v�rifie si on peut aller sur la mer
		if($this->CheckIfCaseMer($PositionAVerifier)){return false;}
		
		//Si non on peut bouger
		return true;
	}
	private function CheckIfCaseMer($position){
		$sql = "SELECT id_case_carte
				FROM table_carte 
				WHERE 
					coordonnee = '".$position."' 
					AND id_type_batiment = 11;";
		
		$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
		
		if(mysql_num_rows($requete) > 0){
			return true;
		}
		/* while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if($row['coordonnee'] == $position)
			{
				return true;
			}
		} */
		return false;
	}
	Private function chkIfBatimentBloquant($position){
		$TypeBatimentBloquant = array(mur::ID_BATIMENT, tour::ID_BATIMENT);
		$sql = "SELECT id_case_carte 
				FROM table_carte 
				WHERE 
					login NOT IN ('".implode("', '", ListeMembreClan($this->clan))."') 
					AND coordonnee = '".$position."'
					AND id_type_batiment IN (".implode(', ', $TypeBatimentBloquant).") 
					AND detruit IS NULL;";
		
		$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
		
		if(mysql_num_rows($requete) > 0){
			
			return true;
		}
		/* while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if($row['coordonnee'] == $position)
			{
				return true;
			}
		} */
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
		$this->LivreSorts = null;
		$this->LstSorts = null;
		$this->arInventaire = null;
		$this->date_perf_attaque = null;
		$this->tmp_perf_attaque = null;
		$this->date_perf_defense = null;
		$this->tmp_perf_defense = null;
		$this->UpdatePoints((abs(personnage::POINT_PERSO_TUE) * -1));
		$this->nb_mort++;
		
		ResetListeQuetes($this->GetLogin());
		
		AddHistory($this->GetLogin(), $this->GetCarte(), $this->GetPosition(), 'ressucite', '', NULL, 'Vous �tes mort. Retour � la maison.');
	}
	
	Private function ListeCodesEquipement(){
		$tmp = null;
		foreach (array('code_arme', 'code_bouclier', 'code_cuirasse', 'code_jambiere', 'code_casque') as $Type)
		{
			if(!is_null($this->$Type))
			{
				$tmp[] = $this->$Type;
			}
		}
		
		return $tmp;
	}
	Private function ValeurDesSorts($type){
			//on initialise la valeur des �quipements
		$ValSort = 0;
		
		if(!is_null($this->LstSorts))
		{
			foreach($this->LstSorts as $Sort)
			{
				$arSort = explode('=', $Sort);
		
				$sql = "SELECT ".$type." FROM table_objets WHERE objet_code='".$arSort[0]."';";
				$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
		
				while($row = mysql_fetch_array($requete, MYSQL_ASSOC))
				{
					$ValSort += intval($row[$type]);
				}
			}
		}
		return $ValSort;
	}
	Private function ValeurEquipements($type){
			//on cr�e la liste des codes des �quipements
		$lstCodes = $this->ListeCodesEquipement();
		
			//on initialise la valeur des �quipements
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
			case 'combat':	$this->not_combat = $value;	break;
			case 'attaque':	$this->not_attaque = $value;	break;
		}
	}
	public function SetLastMessageLu(){
		$this->DateLastMessageLu = strtotime('now');
	}
	
	//Remplir l'objet joueur
	public function hydrate(array $donnees){
		foreach ($donnees as $key => $value){
			switch ($key){
				case 'login':
				case 'mail':
				case 'civilisation':
				case 'village':
				case 'carriere':
					$this->$key = strval($value);
					break;
				case 'position':
					$this->$key = explode(',', $value);
					break;
				case 'id':
				case 'nb_points':
				case 'argent':
				case 'nb_combats':
				case 'nb_victoire':
				case 'nb_vaincu':
				case 'nb_mort':
				case 'val_attaque':
				case 'val_defense':
				case 'experience':
				case 'niveau':
				case 'deplacement':
				case 'vie':
					$this->$key = intval($value);
					break;
				case 'last_action':
					$this->$key = strtotime($value);
					break;
				case 'date_last_combat':
					$this->last_combat = strtotime($value);
					break;
				case 'not_attaque':
				case 'not_combat':
				case 'attaque_tour':
				case 'chk_chasse':
				case 'chk_object':
				case 'chk_legion':
					$this->$key = (is_null($value)?false:true);
					break;
				case 'last_object':
				case 'code_casque':
				case 'code_arme':
				case 'code_bouclier':
				case 'code_jambiere':
				case 'code_cuirasse':
				case 'code_sac':
					$this->$key = (is_null($value)?NULL:strval($value));
					break;
				case 'inventaire':
					$this->arInventaire = (is_null($value)?null:explode(',', $value));
					break;
				case 'date_perf_defense':
				case 'date_perf_attaque':
					$this->$key = (is_null($value)?NULL:strtotime($value));
					break;
				case 'tmp_perf_attaque':
				case 'tmp_perf_defense':
					$this->$key = (is_null($value)?NULL:intval($value));
					break;
				case 'maison_installe':
					$this->$key = (is_null($value)?NULL:explode(',', $value));
					break;
				case 'clan':
					$this->$key = (is_null($value)?NULL:htmlspecialchars_decode($value, ENT_QUOTES));
					break;
				case 'date_last_msg_lu':
					$this->DateLastMessageLu = strtotime($value);
					break;
				
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
		
		//on cr�e la liste des comp�tences possible
		//$sqlLstCmp = "SELECT cmp_lst_code FROM `table_competence_lst` WHERE cmp_lst_acces IN ('".GetInfoCarriere($this->GetCodeCarriere(), 'carriere_class')."', 'Tous') ORDER BY cmp_lst_code ASC;";
		$sqlLstCmp = "SELECT cmp_lst_code, cmp_lst_type 
						FROM `table_competence_lst` 
						ORDER BY cmp_lst_type, cmp_lst_code ASC;";
		$rqtLstCmp = mysql_query($sqlLstCmp) or die (mysql_error().'<br />'.$sqlLstCmp);
		
		if(mysql_num_rows($rqtLstCmp) > 0){
			while($item = mysql_fetch_array($rqtLstCmp, MYSQL_ASSOC)){
				$lst[] = array($item['cmp_lst_code'], $item['cmp_lst_type']);
			}
		}
		
		//On r�cup�re les infos � propos des comp�tences
		$sqlCmp = "SELECT cmp_code 
					FROM table_competence 
					WHERE cmp_login='".$this->login."' AND cmp_finish=1;";
		$rqtCmp = mysql_query($sqlCmp) or die (mysql_error().'<br />'.$sqlCmp);
		
		$status[] = NULL;
		if(mysql_num_rows($rqtCmp) > 0){
			while($cmp = mysql_fetch_array($rqtCmp, MYSQL_ASSOC)){
				$status[] = $cmp['cmp_code'];
			}
			//$this->UpdateCompetences($competences);
		}
		unset($status[0]);
		if(mysql_num_rows($rqtLstCmp) > 0){
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
			//on initialise la valeur d'attaque des �quipements		
		$val_attaque_objet = 0;
		
			//on ajoute la valeur des �quipements
		$val_attaque_objet += $this->ValeurEquipements('objet_attaque');
		
			//on ajoute la valeur des sorts
		$val_attaque_objet += $this->ValeurDesSorts('objet_attaque');
		
		return array($this->val_attaque, $val_attaque_objet);
	}
	public function GetDefPerso(){
			//on initialise la valeur d'attaque des �quipements
		$val_defense_objet = 0;
		
			//on ajoute la valeur des �quipements
		$val_defense_objet += $this->ValeurEquipements('objet_defense');
		
			//on ajoute la valeur des sorts
		$val_defense_objet += $this->ValeurDesSorts('objet_defense');
		
		return array($this->val_defense, $val_defense_objet);
	}
	public function GetDisPerso(){
			//on initialise la valeur d'attaque des �quipements		
		$val_distance_objet = 0;
		
			//on ajoute la valeur des �quipements
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
	public function GetNbMort(){			return $this->nb_mort;}
	public function GetId(){				return $this->id;}
	public function GetArgent(){			return $this->argent;}
	public function GetDatePerfect($type){
		switch($type)
		{
			case self::TYPE_PERFECT_ATTAQUE:	return $this->date_perf_attaque;
			case self::TYPE_PERFECT_DEFENSE:	return $this->date_perf_defense;
		}
		return NULL;
	}
	public function GetTmpPerfect($type){
		switch($type)
		{
			case self::TYPE_PERFECT_ATTAQUE:	return $this->tmp_perf_attaque;
			case self::TYPE_PERFECT_DEFENSE:	return $this->tmp_perf_defense;
		}
		return NULL;
	}
	public function GetLastAction(){		return $this->last_action;}
	public function GetLastCombat(){		return $this->last_combat;}
	public function GetChkChasse(){			return $this->chk_chasse;}
	public function GetChkObject(){			return $this->chk_object;}
	public function GetChkLegionnaire(){	return $this->chk_legion;}
	public function GetLastObject(){		return $this->last_object;}
	public function GetLogin(){				return $this->login;}
	public function GetMail(){				return $this->mail;}
	public function GetVie(){				return $this->vie;}
	public function GetCivilisation(){		return $this->civilisation;}
	public function GetVillage(){			return $this->village;}
	public function GetCodeCarriere(){		return $this->carriere;}
	public function GetMaisonInstalle(){	return $this->maison_installe;}
	public function GetAttaqueTour(){		return $this->attaque_tour;}
	public function GetClan(){				return $this->clan;}
	public function GetPosition(){			return array($this->position['1'], $this->position['2']);}
	public function GetCarte(){				return $this->position['0'];}
	public function GetCoordonnee(){		return implode(',', $this->position);}
	public function GetMaxExperience(){		return (($this->niveau + 1) * 100);}
	public function GetNotifCombat(){		return $this->not_combat;}
	public function GetNotifAttaque(){		return $this->not_attaque;}
	public function GetNbPoints(){			return $this->nb_points;}
	public function GetDateLasMessageLu(){	return $this->DateLastMessageLu;}
	public function GetObjSaMaison(){		return FoundBatiment(1, $this->login);}
		//Les Comp�tences
	public function GetNiveauCompetence($TypeCompetence){
		
		if(isset($this->lstTypeCompetences[$TypeCompetence]))
		{
			$Niveau = 0;
			
			foreach($this->lstTypeCompetences[$TypeCompetence] as $CodeCompetence)
			{
				if($this->CheckCompetence($CodeCompetence))
				{
					$Niveau++;
				}
			}
			
			return $Niveau;
		}
		
		return NULL;
	}
	public function CheckCompetence($codeCompetence){
		if(isset($this->lstCompetences[$codeCompetence]))
		{
			return $this->lstCompetences[$codeCompetence];
		}
		return false;
	}
	public function GetTypeCompetence($codeCompetence){
		foreach ($this->lstTypeCompetences as $Key=>$Type)
		{
			foreach($Type as $Code)
			{
				if($Code == $codeCompetence)
				{
					return $Key;
				}
			}
			
		}
		
		return NULL;
	}
	public function CheckTypeCompetence($typeCompetence){
		if(isset($this->lstTypeCompetences[$typeCompetence]))
		{
			foreach($this->lstTypeCompetences[$typeCompetence] as $Competence)
			{
				if($this->CheckCompetence($Competence))
				{
					return true;
				}
			}
		}
		
		return false;
	}
	public function GetLastCompetenceFinish($typeCompetence){
		$last = NULL;
		if(isset($this->lstTypeCompetences[$typeCompetence]))
		{
			foreach($this->lstTypeCompetences[$typeCompetence] as $Competence)
			{
				if($this->CheckCompetence($Competence))
				{
					$last = $Competence;
				}else{
					return $last;
				}
			}
		}
	}
	public function CheckIfSurMaison(){
		if($this->GetCoordonnee() == implode(',', $this->maison_installe))
		{
			return true;
		}
		
		return false;
	}
	public function GetTauxVolArgent(){
		
		return self::TAUX_VOL_ARGENT;
	}
}
?>