<?php
class entrepot extends batiment{
	private $Contenu;
	
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $carte, array $batiment){
		$this->Hydrate($carte, $batiment);
	}
	
	//--- on ajoute un objet au contenu ---
	public function AddContenu($numObjet){
		//$this->Contenu[]=$numObjet;
	
		$chk = false;
	
		//la structure est type1=nb1,type2=nb2 (exemple : cuir=6,longbaton=3)
		if(!is_null($this->Contenu)){
			foreach($this->Contenu as $key=>$element){
				$arTemp = explode('=', $element);
				if(	$arTemp['0'] == $numObjet){
					$arTemp['1']++;
					$this->Contenu[$key] = implode('=', $arTemp);
					$chk = true;
					break;
				}
			}
		}
		if(!$chk){
			$this->Contenu[] = $numObjet.'=1';
		}
	}
	//--- on enlève un objet au contenu ---
	public function RemoveContenu($numObject){
		$chk = true;
		$temp = null;
		foreach($this->Contenu as $objet){
			$arObjet = explode('=', $objet);
			if($arObjet['0']==$numObject and $chk){
				$arObjet['1']--;
				if($arObjet['1'] > 0){
					$temp[] = implode('=', $arObjet);
				}
				$chk = false;
			}else{
				$temp[] = implode('=', $arObjet);
			}
		}
		$this->Contenu=$temp;
	}
	
	public function Hydrate(array $carte, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($carte, $batiment);
		
		foreach ($carte as $key => $value){
			switch ($key){
				case 'contenu_batiment':	$this->Contenu = (is_null($value)?NULL:explode(',', $value)); break;
			}
		}
	}
	
	//Les affichages
	//==============
	Public function AfficheContenu(personnage &$oJoueur){
		$PositionBatiment	= implode(',', array_merge(array(parent::GetCarte()), parent::GetCoordonnee()));
		$PositionJoueur		= implode(',', array_merge(array($oJoueur->GetCarte()), $oJoueur->GetPosition()));

		if($PositionBatiment == $PositionJoueur){
			
			//Si le contenu est vide
			if(is_null($this->Contenu)){return '<p>Vide</p>';}
			
			//si non, on affiche le contenu	
			$txt = '
			<table>';
			
			$numC = 0;
			$nbObjet = 0;
			$NbColonneMax = 7;
			
			//Pour chaque objet
			foreach($this->Contenu as $objet){
				
				//on extrait le nombre d'objet (0 = code, 1 = nb)
				$arObjet = explode('=', $objet);
				
				//on récupère les info de l'objet
				$sql = "SELECT * FROM table_objets WHERE objet_code='".strval($arObjet[0])."';";
				$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
				$result = mysql_fetch_array($requete, MYSQL_ASSOC);

				//on stocke le code de l'objet à reprendre dans $_SESSION
				$_SESSION['main'][$nbObjet]['reprendre'] = strval($arObjet[0]);
				//$_SESSION['main'][$nbObjet]['coordonnee'] = $PositionBatiment;
				
				//Le nom de l'icone est le type d'objet
				$IconeName = $result['objet_type'];
				
				//Si le type d'objet fait partie  d'un ensemble d'éléments, son nom sera le code de l'objet
				if(in_array($result['objet_type'], array('divers', 'objet'))){$IconeName = $result['objet_code'];}
				
				//Si le bolga est plein, on affiche l'info dans l'infobulle
				if(count($oJoueur->GetLstInventaire()) >= $oJoueur->QuelCapaciteMonBolga()){
					$InfoBulle = '<p>'.AfficheIcone('attention').' Votre Bolga est plein!</p>';
				}else{
					$InfoBulle = null;
				}
				
				//Si le type d'objet fait partie des armes, boucliers, ... on affiche les infos de l'arme, .. dans l'infobulle
				if(	in_array($result['objet_type'], array('casque', 'bouclier', 'cuirasse', 'jambiere', 'arme'))){
					$InfoBulle .= '<table class="equipement">'
						.'<tr><th colspan="3">'.$arObjet[1].'x '.$result['objet_nom'].'</th></tr>'
						.'<tr>'
							.'<td>'.AfficheIcone('attaque').' : '.$result['objet_attaque'].'</td>'
							.'<td>'.AfficheIcone('defense').' : '.$result['objet_defense'].'</td>'
							.'<td>'.AfficheIcone('distance').' : '.$result['objet_distance'].'</td>'
						.'</tr>'
						.'</table>';
				}elseif(in_array($result['objet_type'], array('sort'))){	//Mais si c'est un sort, on affiche la description
					$InfoBulle = '<table class="equipement">'
						.'<tr><th>'.$arObjet[1].'x '.$result['objet_nom'].'</th></tr>'
						.'<tr><td>'.$result['objet_description'].'</td></tr>'
						.'</table>';
				}else{			//Autrement on indique uniquement le nom
					$InfoBulle .= '<table class="equipement">'
						.'<tr><th>'.$arObjet[1].'x '.$result['objet_nom'].'</th></tr>'
						.'</table>';
				}
				
				if(	$PositionBatiment == $PositionJoueur
					AND count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga()){
					$checkA = true;
				}else{
					$checkA = false;
				}
				
				if($numC == 0){
					$txt .= '<tr style="vertical-align:top;">';
				}
				
				$txt .= '<td>'.$this->AfficheUnElement(	($PositionBatiment == $PositionJoueur),
														($oJoueur->QuelCapaciteMonBolga() > count($oJoueur->GetLstInventaire())),
														$nbObjet,
														$result['objet_nom'],
														$result['objet_code'],
														$InfoBulle,
														$arObjet[1])
						.'</td>';
				$numC++;
				
				if($numC == $NbColonneMax){
					$txt .= '</tr>';
					$numC = 0;
				}
				
				$nbObjet++;
			}
			
			if($numC < $NbColonneMax AND $numC != 0){
				$txt .= '</tr>';
			}
			
			$txt .= '</table>';
			
			return $txt;
			
		}else{return '<p>Vous devez vous placez sur son emplacement pour en afficher le contenu.</p>';}
	}
	private function AfficheUnElement(	$ChkVillage,
										$chkBolga,
										$id,
										$NomObjet,
										$CodeObjet,
										$InfoBulle,
										$nbObjet){
	
		$txtBt = null;
	
		Foreach(array(1, 10, 100) as $StepReprise){
			if(	$chkBolga
			AND $StepReprise <= $nbObjet){
				$chkReprise = true;
			}else{
				$chkReprise = false;
			}
				
			if($StepReprise == 1){
				$chkFirst = $chkReprise;
			}
				
			$InfoBulleReprise = '<table class="InfoBulle">'
									.'<tr>'
										.'<td>Reprendre '.$StepReprise.'x de '.$NomObjet.'</td>'
									.'</tr>'
								.'</table>';
				
			$txtBt .= '
				<button type="button" '
					.'class="entrepot" '
					.($chkReprise?'':'disabled="disabled" ')
					.'onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulleReprise).'\');" '
					.'onmouseout="cache();" '
					.'onclick="window.location=\'index.php?page=village&action=reprendre&amp;id='.$id.'&amp;qte='.$StepReprise.'&amp;anchor='.implode('_', array_merge(array(parent::GetCarte()), parent::GetCoordonnee())).'\'">'
						.'Reprendre '.$StepReprise.'x'
				.'</button>' ;
		}
		
		return '<p>'.$nbObjet.'x '
					.'<img src="./img/objets/'.$CodeObjet.'.png" 
						height="50px" '
						.'style="border: 1px black solid; margin:2px; padding:2px; vertical-align:middle;" '
						.'onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" '
						.'onmouseout="cache();" '
					.'/>'
					.'<br />'
					.($ChkVillage?$txtBt:'Placez vous sur sa case pour récupérer cet objet.')
				.'</p>';
		
	}
	//Les GETS
	//========
	public function GetContenu(){				return $this->Contenu;}
	public Function GetCombienElement($CodeObjet) {
		if (!is_null($this->Contenu)){
			foreach ($this->Contenu as $Element) {
				$arElement = explode('=', $Element);
				if ($arElement['0'] == $CodeObjet) {
					return $arElement['1'];
				}
			}
		}
		return 0;
	}
}

?>