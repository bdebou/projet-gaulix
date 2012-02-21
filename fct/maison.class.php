<?php
class maison extends batiment{
	private $ResPierre,
			$ResBois,
			$ResNourriture;
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $carte, array $batiment){
		$this->Hydrate($carte, $batiment);
	}
	public function Hydrate(array $carte, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($carte, $batiment);
		
		foreach ($carte as $key => $value){
			switch ($key){
				case 'res_bois': 			$this->ResBois = (is_null($value)?NULL:intval($value)); break;
				case 'res_pierre':			$this->ResPierre = (is_null($value)?NULL:intval($value)); break;
				case 'res_nourriture':		$this->ResNourriture = (is_null($value)?NULL:intval($value)); break;
			}
		}
	}
	
	//--- Gestion des ressources ---
	// Nourriture dans le stock
	public function AddNourriture($nbNourriture){
		$this->ResNourriture += abs($nbNourriture);
	}
	public function MindNourriture($nbNourriture){
		$this->ResNourriture -= abs($nbNourriture);
	}
	
	// Bois
	public function AddBois($nbBois){
		$this->ResBois += abs($nbBois);
	}
	public function MindBois($nbBois){
		$this->ResBois -= abs($nbBois);
	}
	
	// Pierres
	public function AddPierre($nbPierre){
		$this->ResPierre += abs($nbPierre);
	}
	public function MindPierre($nbPierre){
		$this->ResPierre -= abs($nbPierre);
	}
	
	//Les Affichages
	//==============
	public function AfficheDruide(personnage &$oJoueur){
		//Si le niveau du joueur est inférieur à 1, pas de druide
		if($oJoueur->GetNiveau() < 1){return null;}
		
		$Key = 0;
		$txtOptionsDruide = '
			<table class="druide">';
		
		$sqlLstDruide = "SELECT * FROM table_competence_lst WHERE cmp_lst_type='druide' ORDER BY cmp_lst_niveau ASC;";
		$rqtLstDruide = mysql_query($sqlLstDruide) or die (mysql_error().'<br />'.$sqlLstDruide);
		
		if(mysql_num_rows($rqtLstDruide) == 0){return null;}
		
		while($SortDruide = mysql_fetch_array($rqtLstDruide, MYSQL_ASSOC)){
			if($SortDruide['cmp_lst_niveau'] <= $oJoueur->GetNiveau()){
				$chkSort = true;
				
				//On vérifie la nourriture
				if(	$chkSort
					AND (	$this->GetRessourceNourriture() >= abs($SortDruide['cmp_lst_prix_nourriture'])
							OR $SortDruide['cmp_lst_prix_nourriture'] > 0
							OR is_null($SortDruide['cmp_lst_prix_nourriture']))){
					$_SESSION['main']['druide'][$Key]['N'] = $SortDruide['cmp_lst_prix_nourriture'];
				}else{$chkSort = false;}
			
				//On vérifie le bois
				if(	$chkSort
					AND (	$this->GetRessourceBois() >= abs($SortDruide['cmp_lst_prix_bois'])
							OR $SortDruide['cmp_lst_prix_bois'] > 0
							OR is_null($SortDruide['cmp_lst_prix_bois']))){
					$_SESSION['main']['druide'][$Key]['B'] = $SortDruide['cmp_lst_prix_bois'];
				}else{$chkSort = false;}
			
				//On vérifie la pierre
				if(	$chkSort
					AND (	$this->GetRessourcePierre() >= abs($SortDruide['cmp_lst_prix_pierre'])
							OR $SortDruide['cmp_lst_prix_pierre'] > 0
							OR is_null($SortDruide['cmp_lst_prix_pierre']))){
					$_SESSION['main']['druide'][$Key]['P'] = $SortDruide['cmp_lst_prix_pierre'];
				}else{$chkSort = false;}
			
				//On vérifie l'hydromel
				if(	$chkSort
					AND (	$oJoueur->AssezElementDansBolga('Hydromel', abs($SortDruide['cmp_lst_prix_hydromel']))
							OR $SortDruide['cmp_lst_prix_hydromel'] > 0
							OR is_null($SortDruide['cmp_lst_prix_hydromel']))){
					$_SESSION['main']['druide'][$Key]['H'] = $SortDruide['cmp_lst_prix_hydromel'];
				}else{$chkSort = false;}
			
				//On vérifie l'or
				if(	$chkSort
					AND (	$oJoueur->GetArgent()	>= abs($SortDruide['cmp_lst_prix_or'])
							OR $SortDruide['cmp_lst_prix_or'] > 0
							OR is_null($SortDruide['cmp_lst_prix_or']))){
					$_SESSION['main']['druide'][$Key]['O'] = $SortDruide['cmp_lst_prix_or'];
				}else{$chkSort = false;}
			
				//On vérifie la vie
				if(	$chkSort
					AND (	$SortDruide['cmp_lst_prix_vie'] > 0
							OR is_null($SortDruide['cmp_lst_prix_vie']))
					AND (	$oJoueur->QuelCapaciteMonBolga() > count($oJoueur->GetLstInventaire()))){
					$_SESSION['main']['druide'][$Key]['V'] = $SortDruide['cmp_lst_prix_vie'];
				}else{$chkSort = false;}
			
				//On vérifie le déplacement
				if(	$chkSort
					AND (	$SortDruide['cmp_lst_prix_deplacement'] > 0
							OR is_null($SortDruide['cmp_lst_prix_deplacement']))
					AND (	$oJoueur->QuelCapaciteMonBolga() > count($oJoueur->GetLstInventaire()))){
					$_SESSION['main']['druide'][$Key]['D'] = $SortDruide['cmp_lst_prix_deplacement'];
				}else{$chkSort = false;}
				
				$arPrix = NULL;
				$arRessource = NULL;
				$lstType = array('nourriture', 'hydromel', 'bois', 'pierre', 'or', 'vie', 'deplacement');
				foreach($lstType as $Type){
					if(!is_null($SortDruide['cmp_lst_prix_'.$Type])){
						if($SortDruide['cmp_lst_prix_'.$Type] < 0){
							$arPrix[ucfirst($Type)] = abs($SortDruide['cmp_lst_prix_'.$Type]);
						}
						switch($Type){
							case 'nourriture':	$arRessource[ucfirst($Type)] = $this->GetRessourceNourriture();				break;
							case 'bois':		$arRessource[ucfirst($Type)] = $this->GetRessourceBois();						break;
							case 'pierre':		$arRessource[ucfirst($Type)] = $this->GetRessourcePierre();					break;
							case 'hydromel':	$arRessource[ucfirst($Type)] = $oJoueur->GetCombienElementDansBolga('Hydromel');break;
							case 'or':			$arRessource[ucfirst($Type)] = $oJoueur->GetArgent();							break;
							case 'vie':
							case 'deplacement':	$arRessource[ucfirst($Type)] = NULL;	break;
						}
						if($SortDruide['cmp_lst_prix_'.$Type] > 0){
							$txtContre = abs($SortDruide['cmp_lst_prix_'.$Type]).' '.AfficheIcone($Type);
						}
					}
				}
				
				$txtInfo = AfficheListePrix($arPrix, $arRessource).' pour ';
				
				if(!is_null($SortDruide['cmp_lst_nom'])){
					$_SESSION['main']['druide'][$Key]['type'] = $SortDruide['cmp_lst_nom'];
					$txtInfo .= '<span class="sort_druide">'.$SortDruide['cmp_lst_description'].'</span>';
				}else{
					$_SESSION['main']['druide'][$Key]['type'] = 'ressource';
					$txtInfo .= $txtContre;
				}
				
				$txtOptionsDruide .= '
				<tr>
					<td>'.$txtInfo.'</td>
					<td>
						<button 
							type="button" 
							onclick="window.location=\'index.php?page=village&action=druide&id='.$Key.'&anchor='.implode('_', array_merge(array(parent::GetCarte()), parent::GetCoordonnee())).'\'" 
							style="width:40px;" '
							.($chkSort?'':'disabled="disabled"')
							.' >'
							.($chkSort?AfficheIcone('accept'):AfficheIcone('attention'))
						.'</button>
					</td>
				</tr>';
				
				$Key++;
			}
		}
		$txtOptionsDruide .= '</table>';
		
		return '
			<tr>
				<td rowspan="3" style="width:300px;"><img src="./img/druide.png" width="300px" title="Votre Druide" /></td>
				<th colspan="4" style="background:LightGrey;">Votre Druide</th>
			</tr>
			<tr>
				<td colspan="4"><p>Votre Druide vous rendra toute sorte de services.</p></td>
			</tr>
			<tr>
				<td colspan="4">'.$txtOptionsDruide.'</td>
			</tr>';
	}
	//Les GETS
	//========
	public function GetRessourcePierre(){		return $this->ResPierre;}
	public function GetRessourceBois(){			return $this->ResBois;}
	public function GetRessourceNourriture(){	return $this->ResNourriture;}
}
?>