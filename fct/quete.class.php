<?php
abstract class quete{
	
	private $ID_quete;
	private $Login;
	Private $Position;
	Private $Vie;
	Private $VieMax;
	Private $Reussi; 
	Private $date_start;
	Private $date_end;
	Private $Type;
	Private $IDTypeQuete;
	Private $Groupe;
	Private $Nom;
	Private $Description;
	Private $Niveau;
	Private $Gain;
	Private $GainExperience, $GainPoints;
	Private $CodeObjet;
	Private $Force;
	Private $Duree;
	Private $DateCombat;
	private $Cout;
	private $TxtFinish;
	private $Civilisation;
	Private $Defense;
	private $Status;
	private $Visibilite;
	private $chkCartes;
	
	const NB_QUETE_MAX			= 3;					// Nombre maximum de quete autorisée en  meme temp
	Const MAX_DEPLACEMENT		= 20;					// Nombre maximum de déplacement pour les quetes ROMAIN
	
	Const TYPE_GAIN_POINTS		= 'Points';
	Const TYPE_GAIN_EXPERIENCE	= personnage::TYPE_EXPERIENCE;
	Const TYPE_GAIN_CMP			= 'Competence';
	
	Const TYPE_QUETE			= 'QUEST';
	
	Const IMG_QUETE				= 'romains';
	
	public function __construct(array $Quete, array $InfoQuete){
		date_default_timezone_set('Europe/Brussels');
		
		$this->hydrate($Quete, $InfoQuete);
		
		if(in_array($this->Type, array('romains'))){$this->UpdateQueteRomains();}
		
	}
		
	public function ActionSurQuete(personnage &$joueur){
		return NULL;
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
		if($this->checkIfBatiment(implode(',', array($this->GetCarte(), $arPosition[$direction]['x'], $arPosition[$direction]['y'])))){return false;}
		//Si non on peut bouger
		return true;
	}
	Private function checkIfBatiment($position){
		Global $lstBatimentConstructible, $lstNonBatiment;
		
		$arListBatiments = array_merge($lstBatimentConstructible, $lstNonBatiment);
		//$arListBatiments[] = mur::ID_BATIMENT;
		//$arListBatiments[] = tour::ID_BATIMENT;
		
		$sql = "SELECT coordonnee 
				FROM table_carte 
				WHERE id_type_batiment NOT IN ('".implode("', '", $arListBatiments)."') 
					AND detruit IS NULL;";
		
		$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if($row['coordonnee'] == $position){
				return true;
			}
		}
		return false;
	}
		
	
	
	/**
	 * On récupère les gains de la quete réussie
	 * @param personnage $oJoueur
	 */
	public function RecupereGains(personnage &$oJoueur){
		if(!is_null($this->GetGain()))
		{
			foreach($this->GetGain() as $Gain)
			{
				$arGain = explode('=', $Gain);
				switch($arGain[0])
				{
					case self::TYPE_GAIN_CMP:
						break;
					case self::TYPE_GAIN_EXPERIENCE:
						$oJoueur->AddExperience($arGain[1]);
						break;
					case self::TYPE_GAIN_POINTS:
						$oJoueur->UpdatePoints($arGain[1]);
						break;
					case personnage::TYPE_RES_MONNAIE:
						$oJoueur->AddOr($arGain[1]);
						break;
					default:
						$oJoueur->AddInventaire($arGain[0], $arGain[1], false);
						break;
				}
				
			}
		}
	}
	public function FinishQuete(){
		$this->date_end = strtotime('now');
		$this->Reussi = true;
	}
	Private function SelectionPosition(personnage &$oJoueur){
		$carte = null;
		if($oJoueur->GetNiveau() <= 3)
		{
			if(!is_null($oJoueur->GetMaisonInstalle()))
			{
				$arcarte = $oJoueur->GetMaisonInstalle();
				$carte = $arcarte['0'];
			}else{
				$carte = $oJoueur->GetCarte();
			}
		}
		
		$free = FreeCaseCarte($carte);
		
		return $free[array_rand($free)];
	}
	
	public function Inscription(personnage &$oJoueur, maison &$oMaison){
		if(CheckCout($this->CreateListCout(), $oJoueur, $oMaison))
		{
			if(!is_null($this->CreateListCout()))
			{
				foreach($this->CreateListCout() as $Prix)
				{
					$arPrix = explode('=', $Prix);
					UtilisationRessource($arPrix, $oJoueur, $oMaison);
				}
			}
			
			$this->Login = $oJoueur->GetLogin();
			$this->ID_quete = 'New';
			$this->Position = explode(',', $this->SelectionPosition($oJoueur));
			$this->Vie = $this->VieMax;
			$this->Reussi = NULL;
			$this->date_start = strtotime('now');
			$this->date_end = NULL;
			
			return true;
		}
		return false;
	}
	/**
	 * Retourne la liste des objet pour son achat ou validation
	 * @return array|NULL
	 */
	Protected function CreateListCout(){
		if(!is_null($this->Cout))
		{
			foreach($this->Cout as $Cout)
			{
				$tmpCout = explode('=', $Cout);
				
				switch(QuelTypeObjet($tmpCout[0])){
					case personnage::TYPE_RES_MONNAIE:	
					case personnage::TYPE_COMPETENCE:
					case quete::TYPE_QUETE:
					case qteCombat::TYPE_QUETE_ENNEMI:
					case qteCombat::TYPE_QUETE_MONSTRE:
						$lstCout[] = implode('=', $tmpCout);
						break;
				}
				
			}
			
			if(isset($lstCout))
			{
				return $lstCout;
			}
		}
		return NULL;
	}
	public function CreateListObjectNeed(){
		return NULL;
	}
	//Création de l'objet
	public function hydrate(array $Quete, array $InfoQuete){
		
		foreach ($Quete as $key => $value){
			switch ($key){
				case 'id_quete_en_cours':	$this->ID_quete		= intval($value);									break;
				case 'quete_login':			$this->Login		= strval($value);									break;
				case 'quete_position':		$this->Position		= explode(',', $value);								break;
				//case 'quete_vie':			$this->Vie			= intval($value);									break;
				case 'quete_reussi':		$this->Reussi		= (is_null($value)?false:true);						break;
				case 'date_start':			$this->$key			= (is_null($value)?NULL:strtotime($value));			break;
				case 'date_end':			$this->$key			= (is_null($value)?NULL:strtotime($value));			break;
				case 'last_combat':			$this->DateCombat	= (is_null($value)?NULL:strtotime($value));			break;
				case 'status':				$this->Status		= (is_null($value)?NULL:explode(',', $value));		break;
			}
		}
		foreach ($InfoQuete as $key => $value){
			switch ($key){
				case 'id_quete':			$this->IDTypeQuete		= intval($value);								break;
				case 'quete_type':			$this->Type				= strval($value);								break;
				case 'quete_groupe':		$this->Groupe			= (is_null($value)?false:true);					break;
				case 'quete_civilisation':	$this->Civilisation		= (is_null($value)?NULL:strval($value));		break;
				case 'quete_nom':			$this->Nom				= strval($value);								break;
				case 'quete_description':	$this->Description		= (is_null($value)?NULL:strval($value));		break;
				case 'quete_txt_finish':	$this->TxtFinish		= (is_null($value)?NULL:strval($value));		break;
				case 'quete_niveau':		$this->Niveau			= intval($value);								break;
				case 'quete_gain':			$this->Gain				= (is_null($value)?NULL:explode(',', $value));	break;
				case 'quete_cartes':		$this->chkCartes		= (is_null($value)?false:true);					break;
				case 'quete_cout':			$this->Cout				= (is_null($value)?NULL:explode(',', $value));	break;
				case 'quete_visible':		$this->Visibilite		= (is_null($value)?false:true);					break;
				case 'quete_vie':			$this->VieMax			= intval($value);								break;
				//case 'quete_attaque':		$this->Force			= (is_null($value)?NULL:intval($value));		break;
				//case 'quete_defense':		$this->Defense			= (is_null($value)?NULL:intval($value));		break;
				case 'quete_duree':			$this->Duree			= (is_null($value)?NULL:intval($value));		break;
				//case 'gain_points':		$this->GainPoints		= (is_null($value)?NULL:intval($value));		break;
				//case 'id_objet':			$this->CodeObjet		= (is_null($value)?NULL:strval($value));		break;
			}
		}
	}
	
	//--- Les modules d'affichage ---
	public function AfficheDescriptif(personnage &$oJoueur, maison &$oMaison, $bAvancement/* , $bSurMaison */){
		$_SESSION['quete'][$this->GetIDTypeQuete()] = $bAvancement;
		//On ajoute l'entete de la fiche
		$txt = '
				<div class="fiche_quete">
					<table class="fiche_quete">
						<tr style="background:'.$this::COLOR.';">
							<th>'.$this->Nom.'</th>
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
	
	//--- Les checks ---
	/**
	 * Vérifie si $Login a bien terminé la quête
	 * @param string $Login
	 * @return boolean
	 */
	public function CheckIfDejaTermine($Login){
		if(	isset($this->Login)
			AND $this->Login == $Login
			AND isset($this->Reussi)
			AND $this->Reussi)
		{
			return true;
		}
		return false;
	}
	/**
	 * Vérifie si $Login est bien inscrit à la quête
	 * @param string $Login
	 * @return boolean
	 */
	public function CheckIfEnCours($Login){
		if(	isset($this->Login)
			AND $this->Login == $Login
			AND !$this->Reussi)
		{
			return true;
		}
		return false;
	}
	/**
	 * Vérifie si la quete est de type monstre ou pas.
	 * @return boolean
	 */
	public function CheckQueteMonstre(){
		return false;
	}
	/**
	 * Vérifie si la quête est de type Ennemi ou pas
	 * @return boolean
	 */
	public function CheckQueteEnnemi(){
		return false;
	}
	//--- Les Sets ---
	public function SetVie($value){			$this->Vie = $value;}
	/**
	 * Pour changer les coordonnées de la quête.
	 * @param array $value
	 */
	public function SetPosition($value){	$this->Position = $value;}
	
	//--- Renvoie de valeur ---
	/**
	 * Retourne la liste des gains ou un type de gain en particulier 
	 * @param string $type <p>Type de gain recherché ou NULL pour toute la liste
	 * @return
	 * <ul>
	 * <li>string si $type est spécifié et trouvé</li>
	 * <li>array si $type est NULL</li>
	 * <li>NULL si rien trouvé</li>
	 * </ul> 
	 */
	public function GetGain($type = NULL){
		if(is_null($this->Gain))
		{
			return NULL;
		}
		
		if(is_null($type))
		{
			foreach($this->Gain as $Gain)
			{
				$arGain = explode('=', $Gain);
				switch($arGain[0])
				{
					case self::TYPE_GAIN_CMP:
						break;
					default:
						$lstTemp[] = $Gain;
						break;
				}
			}
			return $lstTemp;
		}else{
			foreach($this->Gain as $gain)
			{
				$arTemp = explode('=', $gain);
				if($arTemp[0] == $type)
				{
					return $arTemp[1];
				}
			} 
		}
		return NULL;
	}
	public function GetLogin(){				return $this->Login;}
	public function GetCout(){
		return $this->CreateListCout();
	}
	public function GetIDQuete(){			return $this->ID_quete;}
	public function GetIDTypeQuete(){		return $this->IDTypeQuete;}
	public function GetVie(){				return $this->Vie;}
	public function GetVieMax(){			return $this->VieMax;}
	public function GetDateStart(){			return $this->date_start;}
	public function GetDateEnd(){			return $this->date_end;}
	public function GetFinish(){			return $this->Reussi;}
	public function GetTypeQuete(){			return $this->Type;}
	public function GetGroupe(){			return $this->Groupe;}
	public function GetNom(){				return $this->Nom;}
	public function GetDescription(){		return $this->Description;}
	public function GetNiveau(){			return $this->Niveau;}
	/* public function GetGainOr(){
		//return $this->GainOr;
		return null;
	} */
	//public function GetGainExperience(){	return $this->GainExperience;}
	//public function GetGainPoints(){		return $this->GainPoints;}
	//public function GetCodeObjet(){			return $this->CodeObjet;}
	public function GetForce(){				return $this->Force;}
	public function GetDuree(){				return $this->Duree;}
	public function GetPosition(){			return array($this->Position['1'], $this->Position['2']);}
	public function GetCarte(){				return $this->Position['0'];}
	public function GetDateCombat(){		return $this->DateCombat;}
	public function GetCoordonnee(){		return implode(',', $this->Position);}
	public function GetTextFinish(){		return $this->TxtFinish;}
	public function GetStatus(){			return $this->Status;}
	Public function GetCodeCmpQuete(){		return $this->GetGain(self::TYPE_GAIN_CMP);}
	public function GetVisibilite(){		return $this->Visibilite;}
	public function GetImgNom(){			return $this::IMG_QUETE;}
	public function GetCheckCartes(){		return $this->chkCartes;}
}
?>