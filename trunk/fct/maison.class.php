<?php
class maison extends batiment{
	private $ResNourriture;
	private $ResEau;
	
	const COUT_AMELIORATION_NIVEAU_1	= 'Sesterce=10,ResMinF=150';
	const COUT_AMELIORATION_NIVEAU_2	= 'Sesterce=1000';
	const COUT_AMELIORATION_NIVEAU_3	= 'Sesterce=150000';
	
	Const PRIX_REPARATION				= 'Sesterce=20,BC=10';		// Prix des réparation par point de vie
	
	const ID_BATIMENT					= 1;
	
	const TYPE_RES_NOURRITURE			= 'Nourriture';
	const TYPE_RES_EAU_POTABLE			= 'Eau';
	
	const NOM_ROMAIN					= 'Villa';
	const NOM_GAULOIS					= 'Oppidum';
		
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $carte = NULL, array $batiment){
		$this->Hydrate($carte, $batiment);
	}
	public function Hydrate(array $carte = NULL, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($carte, $batiment);
		
		if(!is_null($carte))
		{
			foreach ($carte as $key => $value){
				switch ($key)
				{
					case 'res_nourriture':		$this->ResNourriture = (is_null($value)?0:intval($value)); break;
					case 'res_eau':				$this->ResEau = (is_null($value)?0:intval($value)); break;
					case 'contenu_batiment':	$this->Contenu = (is_null($value)?NULL:$value); break;
				}
			}
		}
	}
	
	//--- Gestion des ressources ---
	public function AddRessource($Type, $Quantite){
		switch($Type)
		{
			case self::TYPE_RES_NOURRITURE:
				$this->ResNourriture += abs($Quantite);
				break;
			case self::TYPE_RES_EAU_POTABLE:
				$this->ResEau += abs($Quantite);
				break;
		}
	}
	Public function MindRessource($Type, $Quantite){
		switch($Type)
		{
			case self::TYPE_RES_NOURRITURE:
				$this->ResNourriture -= abs($Quantite);
				break;
			case self::TYPE_RES_EAU_POTABLE:
				$this->ResEau -= abs($Quantite);
				break;
		}
	}
		
	//Les Affichages
	//==============
	public function AfficheOptions(personnage &$oJoueur){
		$Key = 0;
		$txtOptions = '
			<table class="options_maison">';
		
		$sqlLstDruide = "SELECT * FROM table_competence_lst WHERE cmp_lst_type='druide' ORDER BY cmp_lst_niveau ASC;";
		$rqtLstDruide = $this->DB->Query($sqlLstDruide);
		
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
					//$txtInfo .= '<span class="sort_druide">'.$SortDruide['cmp_lst_description'].'</span>';
					$txtInfo .= AfficheInfoObjet($SortDruide['cmp_lst_nom'], 40);
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
	public function GetRessource($Type){
		switch(QuelTypeRessource($Type))
		{
			case self::TYPE_RES_NOURRITURE:
				return $this->ResNourriture;
			case self::TYPE_RES_EAU_POTABLE:
				return $this->ResEau;
		}
		return NULL;
	}
	public function GetNom($Civilisation = NULL){
		switch($Civilisation)
		{
			case personnage::CIVILISATION_GAULOIS:	return self::NOM_GAULOIS;	break;
			case personnage::CIVILISATION_ROMAIN:	return self::NOM_ROMAIN;	break;
		}
		return parent::GetNom();
	}
	
}
?>