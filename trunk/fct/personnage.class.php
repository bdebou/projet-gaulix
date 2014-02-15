<?php
/**
 * Class de personnage Gaulois et Romain
 * @author bruno.deboubers@gmail.com
 *
 */
class personnage{

	private	$id;
	private $login;
	private $mail;
	private $position;
	private $vie;
	private $civilisation;
	private $village;
	private $carriere;
	private $val_attaque, $val_defense;
	private $argent;
	private $experience;
	private $niveau;
	private $deplacement;
	private $last_action;
	private $last_combat; 
	private $attaque_tour;
	private $chk_chasse;
	private $chk_legion; 
	private $chk_object; 
	private $last_object; 
	private $code_casque, $code_arme, $code_bouclier, $code_jambiere, $code_cuirasse, $code_sac;
	private $LivreSorts, $LstSorts;
	private $nb_combats, $nb_victoire, $nb_vaincu, $nb_mort; 
	private $arInventaire;
	private $date_perf_attaque, $tmp_perf_attaque;
	private $date_perf_defense, $tmp_perf_defense;
	private $maison_installe;
	private $clan;
	private $DateLastMessageLu;
	private $lstCompetences;
	private $lstTypeCompetences; 
	private $not_attaque, $not_combat;
	private $nb_points;
	Private $ListQuetesTerminees;
	Private $ListQuetesEnCours;
			
	Const TAILLE_MINIMUM_BOLGA		= 20;			//La taille minimum du bolga
	Const DUREE_SORT				= 432000;		// Limite de temp pour l'utilisation d'un sort (3600 * 24 *5).
	Const DEPLACEMENT_MAX			= 300;			// Limite le nombre déplacement maximum
	Const TEMP_DEPLACEMENT_SUP		= 3600;			// Temp d'attente pour avoir de nouveau du déplacement
	Const NB_DEPLACEMENT_SUP		= 1;			// Nombre de point de déplacement gagné tout les x temp
	Const VIE_MAX					= 300;			// Limite de vie maximum
	Const TAUX_ATTAQUANT			= 1.15;			// Taux d'augmentation de l'attaquant
	Const TAUX_VOL_ARGENT			= 0.10;			// Taux pour le montant de vol d'argent lors d'un combat perdu
	
	const TYPE_RES_MONNAIE			= 'Sesterce';
	const TYPE_COMPETENCE			= 'Compétence';
	
	Const TYPE_COMBAT				= 'Combat';
	Const TYPE_PERFECT_ATTAQUE		= objArmement::TYPE_ATTAQUE;
	Const TYPE_PERFECT_DEFENSE		= objArmement::TYPE_DEFENSE;
	
	Const TYPE_EXPERIENCE			= 'Experience';
	Const TYPE_VIE					= 'Vie';
	
	const CIVILISATION_ROMAIN		= 'Romains';
	const CIVILISATION_GAULOIS		= 'Gaulois';
	
	Const CARRIERE_CLASS_GUERRIER	= 'Guerrier';
	Const CARRIERE_CLASS_DRUIDE		= 'Druide';
	Const CARRIERE_CLASS_ARTISAN	= 'Artisan';
	
	//Les points
	const POINT_COMBAT				= 10;
	const POINT_NIVEAU_TERMINE		= 15;
	const POINT_COMPETENCE_TERMINE	= 5;
	const POINT_OBJET_FABRIQUE		= 1;
	const POINT_PERSO_TUE			= 150;
	
	//Initialisation de l'objet
	public function __construct(array $donnees){
		$this->hydrate($donnees);
		
		//on vérifie si il a droit à des déplacements
		if(	intval((strtotime('now') - $this->last_action) / self::TEMP_DEPLACEMENT_SUP) >= 1
			AND $this->deplacement < self::DEPLACEMENT_MAX){
			$this->AddDeplacement(self::NB_DEPLACEMENT_SUP * intval((strtotime('now') - $this->last_action) / self::TEMP_DEPLACEMENT_SUP),'new');
		}
		
		//On crée la liste des compétences
		$this->CreateListCompetence();
		
		//On crée la liste des quêtes terminées
		$this->CreateListQuete();
		
		//on vérifie si les sorts sont toujours d'actualité
		if(!is_null($this->LstSorts)){
			
			$tmpLst = null;
			
			Foreach($this->LstSorts as $Sort){
				$arSort = explode('=', $Sort);
				if((strtotime('now') - $arSort[1]) <= self::DUREE_SORT){$tmpLst[] = $arSort[0].'='.$arSort[1];}
			}
			$this->LstSorts = $tmpLst;
		}
		
		//on vérifie si on passee à la carrière suivante
		if($this->CheckCarriere())
		{
			$oDB->InsertHistory($this->GetLogin(), $this->GetCarte(), $this->GetPosition(), 'Carrière', NULL, NULL, 'Vous êtes passé à la carrière: '.GetInfoCarriere($this->carriere, 'carriere_nom'));
		}
	}
	
	public static function Cast(personnage &$object=NULL){
		return $object;
	}
	
	/**
	 * Crée la liste des quêtes terminées
	 */
	Private function CreateListQuete(){
		Global $oDB;
		$sqlLst = "SELECT quete_id
						FROM `table_quetes`
						WHERE quete_login='".$this->GetLogin()."'
							AND quete_reussi IS NOT NULL;";
		$rqtLst = $oDB->Query($sqlLst);
		
		if(mysql_num_rows($rqtLst) > 0){
			while($arQuete = mysql_fetch_array($rqtLst, MYSQL_ASSOC)){
				$this->ListQuetesTerminees[] = $arQuete['quete_id'];
			}
		}else{
			$this->ListQuetesTerminees = NULL;
		}
	}
	/**
	 * Vérifie et passe à la carrière suivante
	 * @return boolean
	 */
	Private function CheckCarriere(){
		$arCarriereInfo = GetInfoCarriere($this->GetCodeCarriere());
		
		if(!is_null($arCarriereInfo['carriere_debouchees']))
		{
			$lstCout = explode(',', $arCarriereInfo['carriere_competences']);
			
			if(CheckCout($lstCout, $this, $this->GetObjSaMaison()))
			{
				$this->carriere = $arCarriereInfo['carriere_debouchees'];
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Combat entre le joueur et le $persoCible.
	 * @param personnage $persoCible
	 * @return array[2 string] <ol><li>string >> text résultat combat attaquant</li><li>string >> text résultat combat cible</li></ol> 
	 */
	public function frapper(personnage $persoCible){
		
		$arAttCible		= $persoCible->GetAttPerso();
		$arDefCible		= $persoCible->GetDefPerso();
		$ValeurCible	= $arAttCible['0'] + $arAttCible['1'] + $arDefCible['0'] + $arDefCible['1'];
		
		$arAtt			= $this->GetAttPerso();
		$arDef			= $this->GetDefPerso();
		$Valeur			= (($arAtt['0'] + $arAtt['1']) * self::TAUX_ATTAQUANT)+($arDef['0'] + $arDef['1']);
		
		if($Valeur > $ValeurCible){
				//La cible à perdu
			$montant = $persoCible->GetArgent();
			
			if($persoCible->PerdreVie($Valeur-$ValeurCible, self::TYPE_COMBAT)){
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
			
			return array('Vous avez gagné le combat (+'.abs(self::POINT_COMBAT).' points, +5pts d\'expérience et volé '.$montant.' '.AfficheIcone(self::TYPE_RES_MONNAIE).').',
			'Vous avez perdu un combat ('.(abs(self::POINT_COMBAT) * -1).' points, -'.(intval($Valeur-$ValeurCible)).'pts '.AfficheIcone('vie').', -'.$montant.' '.AfficheIcone(self::TYPE_RES_MONNAIE).' mais +1pt d\'expérience).');
		}elseif($Valeur == $ValeurCible){
				//Match Null
			$persoCible->AddExperience(5);
			$this->AddExperience(5);
			return array('Même valeur de combat donc personne ne gagne, personne ne perd.<br />Vous avez gagné 5 pts d`\'expérience.',
			'Vous avez gagné 5 pts d\'expérience grâce à un combat null.');
		}else{
				//La Cible a gagné
			$montant = $this->GetArgent();
			if($this->PerdreVie($ValeurCible-$Valeur, self::TYPE_COMBAT)){
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
			'Vous avez gagné un combat (+'.abs(self::POINT_COMBAT).' points, +5pts d\'expérience et volé '.$montant.' '.AfficheIcone(self::TYPE_RES_MONNAIE).').');
		}
	}
	
	private function CheckQueteCombatEnCours(){
		if(!is_null($_SESSION['QueteEnCours']))
		{
			foreach($_SESSION['QueteEnCours'] as $oQuete)
			{
				if(get_class($oQuete) == 'qteCombat')
				{
					
				}
			}
		}
	}

	/**
	 * On met à jour les scores après le combat
	 * @param integer $Gagner
	 * @param integer $Perdu
	 */
	public function UpdateScores($Gagner, $Perdu){
		$this->nb_victoire+=$Gagner;
		$this->nb_vaincu+=$Perdu;
		$this->nb_combats++;
	}
	
	//on augment l'espérience
	/**
	 * Augmente l'expérience du joueur de $nbExp
	 * Et augment le niveau du joueur si le GetMaxExperience est atteint
	 * @param integer $nbExp
	 */
	public function AddExperience($nbExp){
		for($i=1;$i<=$nbExp;$i++){
			if($this->experience < $this->GetMaxExperience()){
				$this->experience++;
			}elseif($this->experience==$this->GetMaxExperience()){
				$this->UpNiveau();
				//$this->experience=0;
			}
		}
	}
	/**
	 * Augmente le niveau de 1 et met à jour quelques autres valeurs comme la vie augmente de 10 et ajoute des points 
	 */
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
	/**
	 * On diminue la valeur de la vie du joueur de $nb. Et selon le $type, on affect certains autres paramètres comme la date du dernier combat.
	 * Si la vie du joueur est égale à 0 ou moins, on return <b>True</b> pour signaler qu'il est mort.
	 * @param integer $nb
	 * @param string $type
	 * @return boolean
	 */
	public function PerdreVie($nb, $type){
		switch($type){
			case self::TYPE_COMBAT:
				$this->vie -= $nb;
				$this->last_combat=strtotime('now');
				break;
			case 'tour': $this->attaque_tour = true;
			case 'chasse':
			case 'druide':
			case 'legionnaire':
			case quete::TYPE_QUETE:
				$this->vie -= $nb;
				break;
		}
		if($this->vie <= 0){
			$this->Ressuscite();
			return true;
		}
		
		return false;
	}
	
	/**
	 * On augmente la valeur Vie de son joueur de $nb jusqu'au MAX pas plus.
	 * @param integer $nb
	 */
	public function GagnerVie($nb){
		$this->vie += $nb;
		if($this->vie > self::VIE_MAX){$this->vie = self::VIE_MAX;}
	}

	Public function DesequiperPerso($CodeObject){
		$lstType = array(objArmement::TYPE_ARME, objArmement::TYPE_BOUCLIER, objArmement::TYPE_CASQUE, objArmement::TYPE_CUIRASSE, objArmement::TYPE_JAMBIERE, objDivers::TYPE_SAC);
		
		foreach($lstType as $Type)
		{
			switch($Type)
			{
				case objArmement::TYPE_ARME:
					if($this->code_arme === $CodeObject)
					{
						$this->Desequiper($this->code_arme);
						break(2);
					}
					break;
				case objArmement::TYPE_BOUCLIER:
					if($this->code_bouclier === $CodeObject)
					{
						$this->Desequiper($this->code_bouclier);
						break(2);
					}
					break;
				case objArmement::TYPE_CUIRASSE:
					if($this->code_cuirasse === $CodeObject)
					{
						$this->Desequiper($this->code_cuirasse);
						break(2);
					}
					break;
				case objArmement::TYPE_JAMBIERE:
					if($this->code_jambiere === $CodeObject)
					{
						$this->Desequiper($this->code_jambiere);
						break(2);
					}
					break;
				case objArmement::TYPE_CASQUE:
					if($this->code_casque === $CodeObject)
					{
						$this->Desequiper($this->code_casque);
						break(2);
					}
					break;
				case objDivers::TYPE_SAC:
					if($this->code_sac === $CodeObject)
					{
						$this->Desequiper($this->code_sac);
						break(2);
					}
					break;
			}
		}
	}
	private function Desequiper(&$Code){
		$this->AddInventaire($Code, 1, false);
		$Code = NULL;
	}
	//La gestion de l'or
	/**
	 * On augmente son argent de $or
	 * @param integer $or
	 */
	public function AddOr($or){
		$this->argent += abs(intval($or));
	}
	/**
	 * On diminue son argent de $or
	 * @param integer $or
	 */
	public function MindOr($or){
		$this->argent -= abs(intval($or));
	}
	public function ArgentVole(){
		$this->argent = 0;
	}
	
	//les Compétences
	private function CreateListCompetence(){
		Global $oDB;
		
		//on crée la liste des compétences possible
		$CarriereClass = GetInfoCarriere($this->GetCodeCarriere(), 'carriere_class');
		
		$sqlLstCmp = "SELECT cmp_lst_code, cmp_lst_type
										FROM `table_competence_lst`
										WHERE (cmp_lst_acces IN ('".$CarriereClass."', 'Tous') 
												OR cmp_lst_acces LIKE '%".$CarriereClass."%')  
										ORDER BY cmp_lst_type, cmp_lst_code ASC;";
		$rqtLstCmp = $oDB->Query($sqlLstCmp);
		
		if(mysql_num_rows($rqtLstCmp) > 0){
			while($item = mysql_fetch_array($rqtLstCmp, MYSQL_ASSOC)){
				$lst[] = array($item['cmp_lst_code'], $item['cmp_lst_type']);
			}
		}
		
		//On récupère les infos à propos des compétences
		$sqlCmp = "SELECT cmp_code
									FROM table_competence 
									WHERE cmp_login='".$this->login."' AND cmp_finish=1;";
		$rqtCmp = $oDB->Query($sqlCmp);
		
		$status[] = NULL;
		if(mysql_num_rows($rqtCmp) > 0){
			while($cmp = mysql_fetch_array($rqtCmp, MYSQL_ASSOC)){
				$status[] = $cmp['cmp_code'];
			}
			//$this->UpdateCompetences($competences);
		}
		
		unset($status[0]);
		
		if(mysql_num_rows($rqtLstCmp) > 0)
		{
			foreach($lst as $cmp)
			{
				if(in_array($cmp[0], $status))
				{
					$this->lstCompetences[$cmp[0]] = true;
				}else
				{
					$this->lstCompetences[$cmp[0]] = false;
				}
				
				$this->lstTypeCompetences[$cmp[1]][] = $cmp[0];
				
			}
		}
	}
	
	public function EquiperPerso($numObject, $typeObject){
		Global $oDB;
		
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
					$requete = $oDB->Query($sql);
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
	/**
	 * Ajout dans l'inventaire l'objet par son $codeObjet avec un nombre d'unité de $nbObjet.
	 * On bloque ou non la possibilité de trouver un autre objet.
	 * @param string $codeObjet <p>Le code de l'objet</p>
	 * @param integer $nbObjet <p>Default = 1</p>
	 * @param boorlean $chkLast <p>Default = true. Set la valeur bloquant la possibilité de trouver un autre objet</p>
	 */
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
					if(	$arTemp['0'] == $codeObjet)
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
	/**
	 * On vérifie si on a un plus grand bolga que celui par défaut de 20 objets.
	 * @return integer
	 */
	public function QuelCapaciteMonBolga() {
		Global $oDB;
		
	    //Est ce que le joueur possède un sac?
	    if (!is_null($this->code_sac)) {

	        $sql = "SELECT objet_attaque FROM table_objets WHERE objet_code='" . strval($this->code_sac) . "';";
	        $requete = $oDB->Query($sql);
	        $result = mysql_fetch_array($requete, MYSQL_ASSOC);
	        
	        return $result['objet_attaque'];
	        
	    }
	    
	    return self::TAILLE_MINIMUM_BOLGA;	    
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
	public function SetMaisonInstalle($coordonnee){
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
		Global $arTailleCarte;
		$arCarteNum = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y');
		switch($direction){
			case 'up':
				if($this->position['1'] == 0){
					$this->position['1'] = $arTailleCarte['NbLigne'];
					$this->position['0'] = $arCarteNum[(array_search($this->position['0'], $arCarteNum) - 5)];
				}else{$this->position['1']--;}
				break;
			case 'left':
				if($this->position['2'] == 0){
					$this->position['2'] = $arTailleCarte['NbColonne'];
					$this->position['0'] = $arCarteNum[(array_search($this->position['0'], $arCarteNum) - 1)];
				}else{$this->position['2']--;}
				break;
			case 'down':
				if($this->position['1'] == $arTailleCarte['NbLigne']){
					$this->position['1'] = 0;
					$this->position['0'] = $arCarteNum[(array_search($this->position['0'], $arCarteNum) + 5)];
				}else{$this->position['1']++;}
				break;
			case 'right':
				if($this->position['2'] == $arTailleCarte['NbColonne']){
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
		Global $arTailleCarte;
		$arCoteCarte = array(	'up'	=>array('a','b','c','d','e'),
								'down'	=>array('u','v','w','x','y'),
								'left'	=>array('a','f','k','p','u'),
								'right'	=>array('e','j','o','t','y'));
		
		$arPosition = array(	'up'	=>array('x'=>($this->position[1] - 1), 'y'=>$this->position[2]),
								'down'	=>array('x'=>($this->position[1] + 1), 'y'=>$this->position[2]),
								'left'	=>array('x'=>$this->position[1], 'y'=>($this->position[2] - 1)),
								'right'	=>array('x'=>$this->position[1], 'y'=>($this->position[2]) + 1));
		
		if(		($direction == 'up'		AND $this->position[1] == 0)
			OR	($direction == 'down'	AND $this->position[1] == $arTailleCarte['NbLigne'])
			OR	($direction == 'left'	AND $this->position[2] == 0)
			OR	($direction == 'right'	AND $this->position[2] == $arTailleCarte['NbColonne']))
			{
				if(in_array($this->GetCarte(), $arCoteCarte[$direction]))
				{
					return false;
				}
			}
		
		//a t on encore assez de déplacement?
		if($this->deplacement <= 0)
		{
			return false;
		}
		
		$PositionAVerifier = implode(',', array($this->GetCarte(), $arPosition[$direction]['x'], $arPosition[$direction]['y']));

		//Y a t il un mur ou une tour?
		if($this->chkIfBatimentBloquant($PositionAVerifier)){return false;}
		
		//On vérifie si on peut aller sur la mer
		if($this->CheckIfCaseMer($PositionAVerifier)){return false;}
		
		//Si non on peut bouger
		return true;
	}
	private function CheckIfCaseMer($position){
		Global $oDB;
		
		$sql = "SELECT id_case_carte
				FROM table_carte 
				WHERE 
					coordonnee = '".$position."' 
					AND id_type_batiment = 11;";
		
		$requete = $oDB->Query($sql);
		
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
		Global $oDB;
		
		$TypeBatimentBloquant = array(mur::ID_BATIMENT, tour::ID_BATIMENT);
		$sql = "SELECT id_case_carte 
				FROM table_carte 
				WHERE 
					login NOT IN ('".implode("', '", ListeMembreClan($this->clan))."') 
					AND coordonnee = '".$position."'
					AND id_type_batiment IN (".implode(', ', $TypeBatimentBloquant).") 
					AND detruit IS NULL;";
		
		$requete = $oDB->Query($sql);
		
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
		Global $oDB;
		
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
		
		$oDB->InsertHistory($this->GetLogin(), $this->GetCarte(), $this->GetPosition(), 'ressucite', '', NULL, 'Vous êtes mort. Retour à la maison.');
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
		Global $oDB;
			//on initialise la valeur des équipements
		$ValSort = 0;
		
		if(!is_null($this->LstSorts))
		{
			foreach($this->LstSorts as $Sort)
			{
				$arSort = explode('=', $Sort);
		
				$sql = "SELECT ".$type." FROM table_objets WHERE objet_code='".$arSort[0]."';";
				$requete = $oDB->Query($sql);
		
				while($row = mysql_fetch_array($requete, MYSQL_ASSOC))
				{
					$ValSort += intval($row[$type]);
				}
			}
		}
		return $ValSort;
	}
	Private function ValeurEquipements($type){
		Global $oDB;
			//on crée la liste des codes des équipements
		$lstCodes = $this->ListeCodesEquipement();
		
			//on initialise la valeur des équipements
		$ValeurEquipement = 0;
		
		if(count($lstCodes) > 0){
			$sql = "SELECT ".$type." FROM table_objets WHERE objet_code IN ('".implode("', '", $lstCodes)."');";
			$requete = $oDB->Query($sql);
				
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
	/**
	 * Retourne une array avec les valeurs de combats grace à l'entrainement et les valeurs d'attaque grace aux équipements
	 * @return array <li>Valeur de defense</li><li>Valeur des objets</li> 
	 */
	public function GetAttPerso(){
			//on initialise la valeur d'attaque des équipements		
		$val_attaque_objet = 0;
		
			//on ajoute la valeur des équipements
		$val_attaque_objet += $this->ValeurEquipements('objet_attaque');
		
			//on ajoute la valeur des sorts
		$val_attaque_objet += $this->ValeurDesSorts('objet_attaque');
		
		return array($this->val_attaque, $val_attaque_objet);
	}
	/**
	 * Retourne une array avec les valeurs de défense grace à l'entrainement et les valeurs de défense grace aux équipements
	 * @return array <li>Valeur de defense</li><li>Valeur des objets</li> 
	 */
	 public function GetDefPerso(){
			//on initialise la valeur d'attaque des équipements
		$val_defense_objet = 0;
		
			//on ajoute la valeur des équipements
		$val_defense_objet += $this->ValeurEquipements('objet_defense');
		
			//on ajoute la valeur des sorts
		$val_defense_objet += $this->ValeurDesSorts('objet_defense');
		
		return array($this->val_defense, $val_defense_objet);
	}
	/**
	 * Retourne une array avec la distance utile
	 * @return integer <p>Valeur de la distance</p>
	 */
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
	/**
	 * Retourne les coordonnées de la maison si installée sinon retourn NULL
	 * @return <b>Array</b>
	 */
	public function GetMaisonInstalle(){	return $this->maison_installe;}
	public function GetAttaqueTour(){		return $this->attaque_tour;}
	public function GetClan(){				return $this->clan;}
	public function GetPosition(){			return array($this->position['1'], $this->position['2']);}
	public function GetCarte(){				return $this->position['0'];}
	public function GetCoordonnee(){		return implode(',', $this->position);}
	public function GetMaxExperience(){
		//return (($this->niveau + 1) * 100);
		$nb = 0;
		for ($i = 0; $i <= $this->niveau; $i++)
		{
			$nb += ($i + 1) * 100;
		}
		return $nb;
	}
	public function GetNotifCombat(){		return $this->not_combat;}
	public function GetNotifAttaque(){		return $this->not_attaque;}
	public function GetNbPoints(){			return $this->nb_points;}
	public function GetDateLasMessageLu(){	return $this->DateLastMessageLu;}
	public function GetObjSaMaison(){		return FoundBatiment(maison::ID_BATIMENT, $this->login);}
		//Les Compétences
	
	/**
	 * Retourne le niveau maximum atteint dans un type de compétence donnée par $TypeCompetence
	 * @param string $TypeCompetence <p>Nom du type de compétence</p>
	 * @return <b><i>Integer</i></b> or NULL
	 */
	public function GetNiveauCompetence( $TypeCompetence){
		
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
	/**
	 * Vérifie si la compétence est terminée
	 * @param string $codeCompetence <p>Le code de la compétence à vérifier.</p>
	 * @return boolean
	 */
	public function CheckCompetence($codeCompetence){
		if(isset($this->lstCompetences[$codeCompetence]))
		{
			return $this->lstCompetences[$codeCompetence];
		}
		return false;
	}
	/**
	 * Retourne le type de compétence d'une compétence donnée
	 * @param string $codeCompetence <p> le code de la compétence</p>
	 * @return string|NULL
	 */
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
	/**
	 * Vérifie si pour un type de compétence, il y a déja au moins une compétence de terminée.
	 * @param string $typeCompetence <p>Type de compétence</p>
	 * @return boolean
	 */
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
	/**
	 * Retourne le code de la dernière compétence terminée dans un type de compétence donnée.
	 * @param string $typeCompetence
	 * @return string|NULL
	 */
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
					break;
				}
			}
		}
		return $last;
	}
	/**
	 * Vérifie si la compétence donnée est disponnible dans la carrière du joueur actuel
	 * @param string $CodeCompetence <p>Le code de la compétence à vérifier</p>
	 * @return boolean
	 */
	public function CheckIfCompetenceAvailable($CodeCompetence){
		return array_key_exists($CodeCompetence, $this->lstCompetences);
	}
	/**
	 * Vérifie si le perso actuel est bien sur sa maison
	 * @return boolean
	 */
	public function CheckIfSurMaison(){
		if(	!is_null($this->maison_installe)
			AND $this->GetCoordonnee() == implode(',', $this->maison_installe))
		{
			return true;
		}
		
		return false;
	}
	public function GetTauxVolArgent(){
		
		return self::TAUX_VOL_ARGENT;
	}
	/**
	 * Retourne TRUE si la quête ID est trouvée dans la liste des quêtes terminées
	 * @param integer $idQuete
	 * @return boolean
	 */
	public function CheckIfQueteTerminee($idQuete){
		if(is_null($this->ListQuetesTerminees))
		{
			return false;
		}
		return in_array($idQuete, $this->ListQuetesTerminees);
	}
}
?>