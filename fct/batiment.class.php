<?php
abstract class batiment{
	private $IDCaseCarte;
	private $Login;
	private $Type;
	private $Nom;
	private $Description;
	private $Coordonnee;
	private $Attaque;
	private $Defense;
	private $Distance;
	private $Etat;
	private $EtatMax;
	private $DateLastAction;
	private $Detruit;
	private $Niveau;
	private $DateAction;
	private $LstPrix;
	private $DateAmelioration;
	private $TmpAmelioration;
	private $NbPoints;
	private $Contenu;
	private $IDType;
	
	Const PRIX_REPARATION			= 5;		// Prix des réparation 5or/pts de vie
	Const NIVEAU_MAX				= 4;		// Niveau Maximum pour chaque batiment.
	
	const CODE_ESCLAVE				= 'Esclave';
	
	//Les points
	const POINT_BATIMENT_ATTAQUE	= 3;
	const POINT_BATIMENT_DETRUIT	= 20;
	const POINT_BATIMENT_REPARE		= 8;
	
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $carte, array $batiment){
		$this->Hydrate($carte, $batiment);
	}
	
	//--- L'attaque du batiment de $persoCible par $persoAttaquant ---
	public function AttaquerBatiment(personnage $persoCible, personnage $persoAttaquant){
			//on initialise la valeur de retour attaque pour persoAttaquant
		$txt['0'] = null;
			//on initialise la valeur de retour attaque pour persoCible
		$txt['1'] = null;
		
			//On recupère les valeur d'attaque et de défense de persoAttaquant
		$arAttAttaquant = $persoAttaquant->GetAttPerso();	
		$arDefAttaquant = $persoAttaquant->GetDefPerso();
		
			//on calcule la valeur de défense de persoAttaquant
		$DefAttaquant = $arDefAttaquant['0'] + $arDefAttaquant['1'];
			
			//on calcule la valeur d'attaque de persoAttaquant
		$ValeurAttaquant = intval( ( $arAttAttaquant['0'] + $arAttAttaquant['1'] ) * 1.15 ) + $DefAttaquant ;
		
			//on calcule la valeur d'attaque du batiment
		$Valeur = intval( $this->GetAttaque() * 1.15 ) + $this->GetDefense();
		
			//on calcule les pts de dégat au batiment
		$ptsDegats = $ValeurAttaquant - $this->GetDefense();
			
		if($ptsDegats > 0){		//si le batiment perd des points
				//on crée les messages de retour
			$txt['0'] = 'Vous avez fait '.$ptsDegats.'pts de dégats au batiment ('.$this->Nom.').';
			$txt['1'] = 'Vous avez eu '.$ptsDegats.'pts de dégats au batiment ('.$this->Nom.').';
				//on abime le batiment
			$this->BatimentAbime($persoCible, $ptsDegats, $persoAttaquant);
		}else{
			$txt['0'] = 'Vous avez fait aucun dégat au batiment ('.$this->Nom.') car il est bien solide.';
			$txt['1'] = 'Votre batiment ('.$this->Nom.') a été attaqué mais a bien résister.';
		}
		
			//on calcule les points de vie perdus par l'attaquant
		$ptsVie = intval($this->GetAttaque() * 1.15) - $DefAttaquant;
		
		if($ptsVie > 0){		//Si l'attaquant perd des points de vie
				//on lui retire des points de vie
			$persoAttaquant->PerdreVie($ptsVie,'combat');
				//on lui signale qu'il a perdu des points de vie
			$txt['0'] .= ' Mais vous avez perdu '.$ptsVie.'pts '.AfficheIcone('vie');
		}
		
			//attaquant gagane de l'espérience
		$persoAttaquant->AddExperience(5);
		
			//On protege le batiment d'une autre attaque
		$this->DateLastAction = strtotime('now');
		//on envoie un mail
		if($persoCible->GetNotifAttaque()){NotificationMail($persoCible->GetMail(), 'attaque', $this->Nom, $txt['1']);}
		//on ajoute un historique
		AddHistory($persoAttaquant->GetLogin(), $this->GetCarte(), $this->GetCoordonnee(), 'attaque', $this->Login, NULL, $txt['0']);
		AddHistory($this->Login, $this->GetCarte(), $this->GetCoordonnee(), 'attaque', $persoAttaquant->GetLogin(), NULL, $txt['1']);
		return $txt;
	}
		
	//--- Fonction pour abimer le batiment ---
	private function BatimentAbime(personnage $persoC, $degat, personnage $persoA){
			//on déduit les dégats
		$this->Etat -= $degat;
			//on gère les points gagnés ou perdus
		$persoC->UpdatePoints((abs(self::POINT_BATIMENT_ATTAQUE) * -1));
		$persoA->UpdatePoints(abs(self::POINT_BATIMENT_ATTAQUE));
			//on vérifie si le batiment est détruit
		if($this->Etat <= 0){$this->BatimentDetruit($persoC, $persoA);}
	}
	
	
	//--- Destruction du batiment ---
	private function BatimentDetruit(personnage &$persoCible, personnage &$persoAttaquant){
			//le batiment est détruit donc on le supprime.
		$this->Detruit = true;
			//on gère les points gagnés et perdus
		$persoCible->UpdatePoints(-$this->NbPoints);
		$persoAttaquant->UpdatePoints(self::POINT_BATIMENT_DETRUIT);
			//Différente actions pour certain type de batiment quand détruit.
		switch($this->Type){
				//Quand la maison est détruite, le joueur va devoir reconstruire sa maison quelque part sur la carte
			case 'maison': $persoCible->MaisonDetruit(); break;
				//Quand l'entrepot est détruit, l'attaquantrécupère tout le contenu
			case 'entrepot':
				foreach($this->Contenu as $item){
					$arItem = explode('=', $item);
					$persoAttaquant->AddInventaire($arItem['0'], null, $arItem['1']);
				}
				break;
				//Quand la banque est détruite, l'attaquant récupère l'or
			case 'bank': $persoAttaquant->AddOr(intval($this->Contenu)); break;
			case 'ferme': 
			case 'mine':
				global $objManager;
				//$objManager = new PersonnagesManager($db);
				
				$arContenu = explode(',', $this->Contenu);
				$nomClass = get_class($this);
				$this->AddStock($persoCible->GetNiveauCompetence($nomClass::TYPE_COMPETENCE));
				$arContenu = explode(',', $this->Contenu);
				//$maison = FoundBatiment(1, $persoAttaquant->GetLogin());
				$this->ViderStock($arContenu['1'], $persoAttaquant);
				//$objManager->UpdateBatiment($maison);
				//unset($maison);
				//unset($objManager);
				break;
		}
		AddHistory($persoAttaquant->GetLogin(), $this->GetCarte(), $this->GetCoordonnee(), 'attaque', $this->Login, strtotime('now') +3, $this->Nom.' détruit');
		AddHistory($this->Login, $this->GetCarte(), $this->GetCoordonnee(), 'attaque', $persoAttaquant->GetLogin(), strtotime('now') +3, $this->Nom.' détruit');
	}
	
	
	//--- on répare le batiment ---
	public function Reparer($etat, personnage $Perso){
		$this->Etat += $etat;
		if($this->Etat == $this->GetEtatMax()){
			global $lstPoints;
				//on gère les points gagnés ou perdus
			$Perso->UpdatePoints(self::POINT_BATIMENT_REPARE);
		}
	}
	
	
	//on améliore le batiment
	public function Amelioration($status){
		switch($status){
			case 'Finish':
				$this->Etat += 50;
				$this->Niveau++;
				$this->DateAmelioration = null;
				$this->TmpAmelioration = null;
				break;
			case 'Go':
				$this->DateAmelioration = strtotime('now');
				$this->TmpAmelioration = intval(3600 * exp($this->Niveau));
				break;
		}
	}
	
	//--- on rempli l'objet avec les valeurs correspondant. ---
	public function Hydrate(array $carte, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		foreach ($batiment as $key => $value){
			switch ($key){
				case 'batiment_type':			$this->Type = strval($value); break;
				case 'batiment_nom':			$this->Nom = strval($value); break;
				case 'batiment_description':	$this->Description = strval($value); break;
				case 'batiment_attaque':		$this->Attaque = (is_null($value)?NULL:intval($value)); break;
				case 'batiment_defense':		$this->Defense = (is_null($value)?NULL:intval($value)); break;
				case 'batiment_distance':		$this->Distance = (is_null($value)?NULL:intval($value)); break;
				case 'batiment_vie':			$this->EtatMax = (is_null($value)?NULL:intval($value)); break;
				case 'batiment_prix':			$this->LstPrix = (is_null($value)?NULL:explode(',', $value)); break;
				case 'batiment_points':			$this->NbPoints = intval($value); break;
				case 'id_batiment':				$this->IDType = (int)$value; break;
			}
		}
		
		foreach ($carte as $key => $value){
			switch ($key){
				case 'coordonnee':			$this->Coordonnee = strval($value); break;
				case 'login':				$this->Login = strval($value); break;
				case 'id_case_carte':		$this->IDCaseCarte = intval($value); break;
				case 'etat_batiment':		$this->Etat = (is_null($value)?NULL:intval($value)); break;
				case 'date_last_attaque':	$this->DateLastAction = (is_null($value)?NULL:strtotime($value)); break;
				case 'date_action_batiment':$this->DateAction = (is_null($value)?NULL:strtotime($value)); break;
				case 'detruit':				$this->Detruit = (is_null($value)?false:true); break;
				case 'contenu_batiment':	$this->Contenu = (is_null($value)?NULL:$value); break;
				case 'niveau_batiment':		$this->Niveau = (is_null($value)?NULL:intval($value)); break;
				case 'date_amelioration':	$this->DateAmelioration = (is_null($value)?NULL:strtotime($value)); break;
				case 'tmp_amelioration':	$this->TmpAmelioration = (is_null($value)?NULL:intval($value)); break;
			}
		}
		
	}
	
	//--- Les modules d'affichage ---
	public function GetInfoBulle($AllCartes = false, $Civilisation = NULL){
		return '<table>'
					.'<tr>'
						.($AllCartes?
						'<td rowspan="2">'
							.'<img src="./img/carte/'.$this->GetImgName().'.png" alt="'.$this->GetNom().'" title="'.$this->GetNom().'" />'
						.'</td>'
						:'')
						.'<th>'
							.$this->GetNom($Civilisation).(!is_null($this->GetLogin())?' de '.$this->GetLogin():'')
						.'</th>'
					.'</tr>'
					.'<tr>'
						.'<td>'
							.'<img alt="'.self::GetNom().'" src="./fct/fct_image.php?type=etatcarte&amp;value='.$this->GetEtat().'&amp;max='.$this->GetEtatMax().'" />'
						.'</td>'
					.'</tr>'
				.'</table>';
	}
	public function AfficheOptionAmeliorer(personnage &$oJoueur){
		
		$id = str_replace(',', '_', $this->Coordonnee);
		
		if($this->GetNiveau() >= $this->GetNiveauMax()){return '<p>Niveau Maximum atteint.</p>';}
		
		if(!is_null($this->DateAmelioration) AND !is_null($this->TmpAmelioration)){
			
			$_SESSION['main'][$id] = true;
			
			if((strtotime('now') - $this->DateAmelioration) < $this->TmpAmelioration){
				
				return '<br />Amélioration en cours : <div style="display:inline;" id="TimeToWaitAmelioration_'.$id.'"></div>'
				.AfficheCompteurTemp('Amelioration_'.$id, 'index.php?page=village&action=ameliorer&id='.$id, ($this->GetTmpAmelioration()-(strtotime('now')-$this->GetDateAmelioration())));
				
			}else{
				
				return '<script type="text/javascript">window.location=\'index.php?page=village&action=ameliorer&id='.$id.'\';</script>';
				
			}
			
		}else{
			
			$prixAmelioration = $this->GetCoutAmelioration();
			$maison = $oJoueur->GetObjSaMaison();
			
			$chkPrix = true;
			if(!is_null($prixAmelioration))
			{
				foreach($prixAmelioration as $Prix)
				{
					if(!CheckIfAssezRessource(explode('=', $Prix), $oJoueur, $maison))
					{
						$chkPrix = false;
						break;
					}
				}
			}
			
			$_SESSION['main'][$id]['prixAmelioration'] = $prixAmelioration;
			
			if(!$chkPrix OR $this->GetCoordonnee() != $oJoueur->GetPosition())
			{
				return '<p>Prix de l\'amélioration : <br />'.AfficheListePrix($prixAmelioration, $oJoueur, $maison).'</p>';
			}
			
			return '<br /><a href="index.php?page=village&action=ameliorer&id='.$id.'&anchor='.str_replace(',', '_', $this->Coordonnee).'" title="Or = '.$prixAmelioration['Or'].'&#13;Bois = '.$prixAmelioration['Bois'].'&#13;Pierre = '.$prixAmelioration['Pierre'].'&#13;Nourriture = '.$prixAmelioration['Nourriture'].'&#13;'.AfficheTempPhrase(DecoupeTemp(intval(3600*exp($this->Niveau)))).'">Améliorer</a> pour '.AfficheListePrix($prixAmelioration, $oJoueur, $maison);
		}
	}
	public function AfficheOptionReparer(&$oJoueur){
		
		$txt = null;
		$chkA = true;
		$txt_Options = null;
	
		$PositionJoueur		= implode(',', array_merge(array($oJoueur->GetCarte()),$oJoueur->GetPosition()));
	
		if($this->Etat < $this->GetEtatMax()){
			if($this->Coordonnee != $PositionJoueur){
				return 'Placez-vous sur la case pour réparer.';
			}
	
			//$_SESSION['main']/*[$nbReparer]*/['reparer'] = $id;
			$PtsAReparer = $this->GetEtatMax() - $this->Etat;
			
			if(intval($PtsAReparer/100) > 0 and $oJoueur->GetArgent() > (100 * self::PRIX_REPARATION)){
				$chkA = false;
				$_SESSION['main']['Reparer']['0']['pts'] = 100;
				$_SESSION['main']['Reparer']['0']['montant'] = 100 * self::PRIX_REPARATION;
				$txt_Options .= '<option value="0">Réparer 100pts de vie pour '.(100 * self::PRIX_REPARATION).' pièces</option>';
			}
			if($PtsAReparer >= 10 and $oJoueur->GetArgent() > (10 * self::PRIX_REPARATION)){
				$chkA = false;
				$_SESSION['main']['Reparer']['1']['pts'] = 10;
				$_SESSION['main']['Reparer']['1']['montant'] = 10 * self::PRIX_REPARATION;
				$txt_Options .= '<option value="1">Réparer 10pts de vie pour '.(10 * self::PRIX_REPARATION).' pièces</option>';
			}
			if(	$oJoueur->GetArgent() >= ($PtsAReparer * self::PRIX_REPARATION)){
				$chkA = false;
				$_SESSION['main']['Reparer']['2']['pts'] = $PtsAReparer;
				$_SESSION['main']['Reparer']['2']['montant'] = $PtsAReparer * self::PRIX_REPARATION;
				$txt_Options .= '<option value="2">Réparer '.$PtsAReparer.'pts de vie pour '.($PtsAReparer * self::PRIX_REPARATION).' pièces</option>';
			}elseif(intval($oJoueur->GetArgent() / self::PRIX_REPARATION) != 0
			AND intval($oJoueur->GetArgent() / self::PRIX_REPARATION) <=  $PtsAReparer){
				$chkA = false;
				$_SESSION['main']['Reparer']['3']['pts'] = intval($oJoueur->GetArgent() / self::PRIX_REPARATION);
				$_SESSION['main']['Reparer']['3']['montant'] = intval($oJoueur->GetArgent() / self::PRIX_REPARATION) * self::PRIX_REPARATION;
				$txt_Options .= '<option value="3">Réparer '.intval($oJoueur->GetArgent() / self::PRIX_REPARATION).'pts de vie pour '.(intval($oJoueur->GetArgent() / self::PRIX_REPARATION) * self::PRIX_REPARATION).' pièces</option>';
			}
			if(intval($oJoueur->GetArgent() / self::PRIX_REPARATION) > 0){
				$chkA = false;
				$_SESSION['main']['Reparer']['4']['pts'] = 1;
				$_SESSION['main']['Reparer']['4']['montant'] = self::PRIX_REPARATION;
				$txt_Options .= '<option value="4">Réparer 1pt de vie pour '.self::PRIX_REPARATION.' pièces</option>';
			}
	
	
			if($chkA){
				return 'Pas assez d\'argent';
			}
			return '
		<form method="get" action="index.php">
			<input type="hidden" name="page" value="village" />
			<input type="hidden" name="anchor" value="'.str_replace(',', '_', $this->Coordonnee).'" />
			<input type="hidden" name="action" value="reparer" />
			<input type="hidden" name="num" value="'./*$nbReparer*/'1'.'" />
			<select name="id">
				'.$txt_Options.'
			</select>
			<br />
			<input type="submit" value="Réparer" />
		</form>';
		}else{
			return 'Pas de réparation';
		}
	}
	
	//--- Renvoie de valeur ---
	public function GetIDType(){				return $this->IDType;}
	public function GetIDCase(){				return $this->IDCaseCarte;}
	public function GetLogin(){					return $this->Login;}
	public function GetType(){					return $this->Type;}
	public function GetNom(){					return $this->Nom;}
	public function GetDescription(){			return $this->Description;}
	public function GetRessource($Type){		return NULL;}
	public function GetAttaque(){
		if(is_null($this->Attaque) AND $this->Niveau <= 1){return 0;}
		else{return ($this->Attaque + (5 * ($this->Niveau - 1)));}
	}
	public function GetDefense(){
		if(is_null($this->Defense) AND $this->Niveau <= 1){return 0;}
		else{return ($this->Defense + (5 * ($this->Niveau - 1)));}
	}
	public function GetDistance(){
		if($this->Distance==0){return $this->Distance;}
		else{return ($this->Distance + $this->Niveau);}
	}
	public function GetEtat(){					return $this->Etat;}
	public function GetEtatMax(){				return ($this->EtatMax + (50 * ($this->Niveau - 1)));}
	public function GetDateLastAction(){		return $this->DateLastAction;}
	public function GetDetruit(){				return $this->Detruit;}
	public function GetNiveau(){				return $this->Niveau;}
	public function GetNiveauMax(){				return self::NIVEAU_MAX;}
	public function GetDateAction(){			return $this->DateAction;}
	public function GetContenu(){				return $this->Contenu;}
	public function GetListePrix(){				return $this->LstPrix;}
	public function GetDateAmelioration(){		return $this->DateAmelioration;}
	public function GetTmpAmelioration(){		return $this->TmpAmelioration;}
	public function GetImgName(){				return get_class($this).'-'.($this->Login == $_SESSION['joueur']?'a':'b');}
	public function GetCoordonnee(){
		$arPosition = explode(',', $this->Coordonnee);
		return array($arPosition[1], $arPosition[2]);
	}
	public function GetCarte(){
		$arPosition = explode(',', $this->Coordonnee);
		return $arPosition['0'];
	}
	public function GetStatusAmelioration(){
		if(is_null($this->DateAmelioration) OR is_null($this->TmpAmelioration)){
			return 'Go';
		}elseif((strtotime('now') - $this->DateAmelioration) < $this->TmpAmelioration){
			return 'InProgress';
		}else{
			return 'Finish';
		}
	}
	public function GetCoutAmelioration(){
		$classBatiment = get_class($this);
		
		switch(self::GetNiveau() + 1)
		{
			case 2:		return explode(',', $classBatiment::COUT_AMELIORATION_NIVEAU_2);
			case 3:		return explode(',', $classBatiment::COUT_AMELIORATION_NIVEAU_3);
			case 4:		return explode(',', $classBatiment::COUT_AMELIORATION_NIVEAU_4);
		}
	}
}

?>