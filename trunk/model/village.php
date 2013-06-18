<?php
function CreateListBatiment(personnage &$oJoueur){
	global $lstNonBatiment, $lstBatimentsNonConstructible;
	
	$lstBatiment = null;
	
	$sql = "SELECT coordonnee, id_type_batiment FROM table_carte WHERE login='".$oJoueur->GetLogin()."' AND detruit IS NULL;";
	$requete = mysql_query($sql) or die ( mysql_error() );
	
	if(mysql_num_rows($requete) > 0){
	
		while($carte = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if(!in_array($carte['id_type_batiment'], array_merge($lstNonBatiment, $lstBatimentsNonConstructible))){
				$lstBatiment[] = AfficheBatiment(FoundBatiment(NULL, $oJoueur->GetLogin(), $carte['coordonnee']), $oJoueur);
			}
		}
	}

	return $lstBatiment;
}
/**
 * Retourne une <TR> pour un tableau contenant toutes les informations du batiment.
 * @param batiment $batiment
 * @param <i>personnage $oJoueur </i>
 * @return string
 */
function AfficheBatiment(batiment &$batiment, personnage &$oJoueur = NULL){
	$ImgSize = 'height';
	$txt = NULL;

	$contenu = 'Ne peut rien contenir';
	
	$chkPositionJoueur = false;
	$nbLigne = 3;
	
	if(!is_null($oJoueur)){	
		$chkPositionJoueur		= $oJoueur->GetCoordonnee() == $batiment->GetCoordonnee();
	}
	
	$chkMarche = false;
	
	$lstBatimentAvecEsclaves = array(ferme::ID_BATIMENT, potager::ID_BATIMENT, mine::ID_BATIMENT, carriere::ID_BATIMENT, scierie::ID_BATIMENT);

	switch($batiment->GetIDType()){
		case maison::ID_BATIMENT:
			$ImgSize = 'width';
			if($chkPositionJoueur){
				$contenu = '<p>Ne peut rien contenir.</p>';
				$chkOptions = false;
			}else{
				$contenu = '<p>Si ici que vous devez vous placer pour vous inscrire ou valider une quête.</p>';
			}
			break;
		/* case 'bank':
			$contenu = $batiment->AfficheContenu($oJoueur);
			break; */
		case scierie::ID_BATIMENT:
		case ferme::ID_BATIMENT:
		case mine::ID_BATIMENT :
		case potager::ID_BATIMENT:
		case carriere::ID_BATIMENT:
			$ImgSize = 'width';
			if(!is_null($oJoueur)){	$contenu = $batiment->AfficheContenu($oJoueur);}
			break;
		case marche::ID_BATIMENT:
			if($chkPositionJoueur){
				$chkMarche = true;
			}else{
				$contenu = '<p>Vous devez vous placez sur son emplacement pour afficher les transactions disponibles.</p>';
			}
			break;
	}
	if(in_array($batiment->GetIDType(), $lstBatimentAvecEsclaves))
	{
		$arLignes[3] = '
			<tr>
				<td>'
		.(!is_null($oJoueur)?
		$batiment->AfficheAchatEsclave($oJoueur)
		:'Possibilité d\'acheter des esclaves pour augmenter sa production')
		.'</td>
			</tr>';
		$nbLigne++;
	}
	
	if(!is_null($oJoueur))
	{
		$arLignes[2] = '
			<tr><td>'.$batiment->AfficheOptionAmeliorer($oJoueur).'</td></tr>';
		$arLignes[4] = '
			<tr>
				<td>'
					.'<img alt="Barre status" src="./fct/fct_image.php?type=statusetat&amp;value='.$batiment->GetEtat().'&amp;max='.$batiment->GetEtatMax().'" />'
					.'<br />'
					.$batiment->AfficheOptionReparer($oJoueur)
				.'</td>
			</tr>';
		$arLignes[7] = '
			<tr><td>'.$contenu.'</td></tr>';
	
		$nbLigne+=3;
		
		if($batiment->GetIDType() == marche::ID_BATIMENT)
		{
			$arLignes[8] = '
			<tr><td>'.$batiment->AfficheTransactions($oJoueur).'</td></tr>';
			
			$nbLigne++;
		}
	}
	
	$arLignes[5] = '
			<tr>
				<td>
					<ul style="list-style-type:none; padding:0px; text-align:center; margin:0px;">
						<li style="display:inline;">'.AfficheIcone(objArmement::TYPE_ATTAQUE).' : '.(is_null($batiment->GetAttaque())?'0':$batiment->GetAttaque()).'</li>
						<li style="display:inline; margin-left:40px;">'.AfficheIcone(objArmement::TYPE_DISTANCE).' : '.(is_null($batiment->GetDistance())?'0':$batiment->GetDistance())	.'</li>
						<li style="display:inline; margin-left:40px;">'.AfficheIcone(objArmement::TYPE_DEFENSE).' : '.(is_null($batiment->GetDefense())?'0':$batiment->GetDefense()).'</li>
					</ul>
				</td>
			</tr>';
	$arLignes[6] = '
			<tr><td>'.$batiment->GetDescription().'</td></tr>';
	$arLignes[1] = '
			<tr>
				<td rowspan="'.$nbLigne.'" style="width:400px;">
					<img alt="'.$batiment->GetType().'" src="./img/batiments/'.$batiment->GetType().'.png" width="400px"  onmouseover="montre(\''.CorrectDataInfoBulle($batiment->GetDescription()).'\');" onmouseout="cache();"/>
				</td>
				<th>'
					.(!is_null($oJoueur)?
						'<a name="'.str_replace(',', '_', $batiment->GetCoordonnee()).'">'
						:NULL)
					.$batiment->GetNom((!is_null($oJoueur)?$oJoueur->GetCivilisation():personnage::CIVILISATION_GAULOIS)).(!is_null($oJoueur)?' ('.$batiment->GetNiveau().' / '.$batiment->GetNiveauMax().')':NULL)
					.(!is_null($oJoueur)?
						'</a>'
						:NULL)
				.'</th>
			</tr>';
	
	//on trie par keys
	ksort($arLignes);
	
	return implode('', $arLignes);
}
function AddTransaction($vendeur, $achat=array('nourriture'=>'NULL', 'pierre'=>'NULL', 'bois'=>'NULL', 'or'=>'NULL'), $vente=array('nourriture'=>'NULL', 'pierre'=>'NULL', 'bois'=>'NULL', 'or'=>'NULL')){
	$sql="INSERT INTO `table_marche` (
	`ID_troc`, 
	`vendeur`, 
	`acheteur`, 
	`vente_nourriture`, `vente_bois`, `vente_pierre`, `vente_or`, 
	`achat_nourriture`, `achat_bois`, `achat_pierre`, `achat_or`, 
	`date_vente`, 
	`status_vente`) VALUES (
	NULL, 
	'$vendeur', 
	NULL, 
	".$vente['nourriture'].", ".$vente['bois'].", ".$vente['pierre'].", ".$vente['or'].", 
	".$achat['nourriture'].", ".$achat['bois'].", ".$achat['pierre'].", ".$achat['or'].", 
	'".date('Y-m-d H:i:s')."', 
	NULL);";
	mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);
}
function UpdateTransaction($id, $type='vendu') {
	$sql = "UPDATE table_marche SET
		acheteur=" . ($type == 'vendu' ? "'" . $_SESSION['joueur'] . "'" : 'NULL') . ", 
		status_vente=1 
		WHERE ID_troc=$id;";
	mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
}

//+---------------------------------+
//|				ACTIONS				|
//+---------------------------------+
function ActionAmeliorerBatiment(&$check, personnage &$oJoueur, &$objManager, $coordonnee){
	if(isset($_SESSION['main'][$coordonnee])){

		$maison = $oJoueur->GetObjSaMaison();

		if(!is_null($maison)){
				
			$batiment = FoundBatiment(null, null, str_replace('_', ',', $coordonnee));
				
			if(!is_null($batiment)){
				
				switch($batiment->GetStatusAmelioration()){
					case 'Go':
						$chkPrix = true;
						if(!is_null($_SESSION['main'][$coordonnee]['prixAmelioration']))
						{
							if(CheckCout($batiment->GetCoutAmelioration(), $oJoueur, $maison)){
								foreach($batiment->GetCoutAmelioration() as $Prix)
								{
									UtilisationRessource(explode('=', $Prix), $oJoueur, $maison);
								}
								if($batiment->GetIDType() == maison::ID_BATIMENT){
									$batiment = $maison;
								}
							}else{
								$check = false;
								echo 'Erreur GLX0005: Fonction ActionAmeliorerBatiment - Pas assez de ressource.';
							}
						}else{
							$check = false;
							echo 'Erreur GLX0006: Fonction ActionAmeliorerBatiment - Pas de prix.';
						}
					case 'Finish':
						$batiment->Amelioration($batiment->GetStatusAmelioration());
						break;
					default:
						$check = false;
						echo 'Erreur GLX0006: Fonction ActionAmeliorerBatiment.';
						break;
				}
				
				$objManager->UpdateBatiment($batiment);
				
				unset($batiment);
			}else{
				$check = false;
				echo 'Erreur GLX0004: Fonction ActionAmeliorerBatiment - Batiment Introuvable';
			}
			if($chk){
				$objManager->UpdateBatiment($maison);
			}
			unset($maison);
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionAmeliorerBatiment - Maison Introuvable';
		}
		unset($_SESSION['main'][$coordonnee]);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionAmeliorerBatiment';
	}
}
function ActionReparer(&$check, $qtePoint, personnage &$oJoueur, &$objManager){
	//if(isset($_SESSION['main']['Reparer'])){
		$batiment = FoundBatiment(null, $oJoueur->GetLogin(), $oJoueur->GetCoordonnee());
		
		if(!is_null($batiment)){
			$objMaison = $oJoueur->GetObjSaMaison();
			
			if(CheckCout($batiment->GetCoutReparation($qtePoint), $oJoueur, $objMaison))
			{
				$batiment->Reparer($qtePoint, $oJoueur);
				
				if($batiment->GetIDType() == maison::ID_BATIMENT)
				{
					foreach($batiment->GetCoutReparation($qtePoint) as $Prix)
					{
						UtilisationRessource(explode('=', $Prix), $oJoueur, $batiment);
					}
				}else{
				foreach($batiment->GetCoutReparation($qtePoint) as $Prix)
					{
						UtilisationRessource(explode('=', $Prix), $oJoueur, $objMaison);
					}
					$objManager->UpdateBatiment($objMaison);
				}
				//$oJoueur->MindOr(batiment::PRIX_REPARATION * $qtePoint);
				$objManager->UpdateBatiment($batiment);
				
			}
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionReparer - Batiment Introuvable';
		}
	/* 	unset($_SESSION['main']['Reparer']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionReparer';
	} */
}
function ActionDepot(&$check, &$oJoueur, &$objManager){
	if(isset($_POST['depot'])){
		if($_POST['depot'] <= $oJoueur->GetArgent()){
			$banque = FoundBatiment(5);
			if(!is_null($banque)){
				$oJoueur->MindOr($_POST['depot']);
				$banque->DepotBank($_POST['depot']);
				$objManager->UpdateBatiment($banque);
			}else{
				$check = false;
				echo 'Erreur GLX0003: Fonction ActionDepot - Banque Introuvable';
			}
			unset($_POST['depot']);
		}
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionDepot';
	}
}
function ActionRetrait(&$check, &$oJoueur, &$objManager){
	if(isset($_POST['retrait'])){
		$banque = FoundBatiment(5);
		if(!is_null($banque)){
			if($banque->GetContenu() >= $_POST['retrait']){
				$oJoueur->AddOr($_POST['retrait']);
				$banque->RetraitBank($_POST['retrait']);
				$objManager->UpdateBatiment($banque);
			}
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionRetrait - Banque Introuvable';
		}
		unset($_POST['retrait']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionRetrait';
	}
}
function ActionReprendre(&$check, $id, &$oJoueur, &$objManager){
	if(isset($_SESSION['main'][$id]['reprendre'])){

		$entrepot = FoundBatiment(4);

		if(!is_null($entrepot)){
				
			if(($oJoueur->QuelCapaciteMonBolga() < count($oJoueur->GetLstInventaire()))){
				$check = false;
				echo 'Erreur GLX0004: Fonction ActionReprendre : Bolga pein';
			}elseif ($_GET['qte'] > $entrepot->GetCombienElement($_SESSION['main'][$id]['reprendre'])){
				$_GET['qte'] = $entrepot->GetCombienElement($_SESSION['main'][$id]['reprendre']);
			}
				
			if($check){
				for ($i = 1; $i <= $_GET['qte']; $i++) {
					$entrepot->RemoveContenu($_SESSION['main'][$id]['reprendre']);
					$oJoueur->AddInventaire($_SESSION['main'][$id]['reprendre'], 1, false);
				}
					
				$objManager->UpdateBatiment($entrepot);
			}
				
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionReprendre - Entrepot Introuvable';
		}
		unset($_SESSION['main'][$id]['reprendre']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionReprendre';
	}
}
function ActionViderStock(&$check, $id, $type, personnage &$oJoueur, &$objManager){
	$batiment = FoundBatiment($id, null, $oJoueur->GetCoordonnee());
	
	if(!is_null($batiment))
	{
		//On vide le stock du batiment
		$batiment->ViderStock($oJoueur);
		
		$objManager->UpdateBatiment($batiment);
		unset($batiment);
	}else{
		$check = false;
		echo 'Erreur GLX0003: Fonction ActionViderStock - '.ucfirst(strtolower($type)).' Introuvable';
	}
	
}
function ActionProduction(&$check, $id, $NomBatiment, $type, personnage &$oJoueur, &$objManager){
	$batiment = FoundBatiment($id, null, $oJoueur->GetCoordonnee());
		
	if(!is_null($batiment))
	{
		//On vide d'abord le stock du batiment
		$batiment->ViderStock($oJoueur);

		//Et puis on change le type de production
		$batiment->ChangerProductionBatiment($type);
		
		$objManager->UpdateBatiment($batiment);
		unset($batiment);
	}else{
		$check = false;
		echo 'Erreur GLX0003: Fonction ActionProduction - '.ucfirst(strtolower($NomBatiment)).' Introuvable';
	}
	
}
function ActionDruide(&$chkErr, $id, personnage &$oJoueur, &$objManager){
	if(isset($_SESSION['main']['druide'])){
		$maison = $oJoueur->GetObjSaMaison();
		foreach($_SESSION['main']['druide'][$id] as $key=>$value){
			switch($key){
				case 'N' :
					if($value < 0){
						$maison->MindRessource(maison::TYPE_RES_NOURRITURE, abs($value));
					}
					elseif($value > 0){
						$maison->AddRessource(maison::TYPE_RES_NOURRITURE, abs($value));
					}
					break;
				case 'B':
					if($value < 0){
						$maison->MindRessource(maison::TYPE_RES_BOIS, abs($value));
					}
					elseif($value > 0){
						$maison->AddRessource(maison::TYPE_RES_BOIS, abs($value));
					}
					break;
				case 'P':
					if($value < 0){
						$maison->MindRessource(maison::TYPE_RES_PIERRE, abs($value));
					}
					elseif($value > 0){
						$maison->AddRessource(maison::TYPE_RES_PIERRE, abs($value));
					}
					break;
				case 'H':
					if($value < 0){
						$oJoueur->CleanInventaire('Hydromel', NULL, abs($value));
					}
					elseif($value > 0){
						$oJoueur->AddInventaire('Hydromel', abs($value), false);
					}
					break;
				case 'O':
					if($value < 0){
						$oJoueur->MindOr(abs($value));
					}
					elseif($value > 0){
						$oJoueur->AddOr(abs($value));
					}
					break;
				case 'V':
					if($value < 0){
						$oJoueur->PerdreVie(abs($value));
					}
					elseif($value > 0){
						$oJoueur->AddInventaire('ResVie'.abs($value), 1, false);
					}
					break;
				case 'D':
					if($value > 0){
						$oJoueur->AddInventaire('ResDep'.abs($value), 1, false);
					}
					break;
			}
		}

		//on recoit le parchemin du sort
		if($_SESSION['main']['druide'][$id]['type'] != 'ressource'){
			$oJoueur->AddInventaire($_SESSION['main']['druide'][$id]['type'], 1, false);
		}

		$objManager->UpdateBatiment($maison);
		unset($maison);
		unset($_SESSION['main']['druide']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionDruide';
	}
}
function ActionVenteMarche(&$check, $id, &$oJoueur, &$objManager){
	if(isset($_SESSION['main']['vente'])){
		$sql = "SELECT contenu_vendeur FROM table_marche WHERE type_vendeur='marchant'";
		$requete = mysql_query($sql) or die (mysql_error());

		$objMarche = new marchant(mysql_fetch_array($requete, MYSQL_ASSOC));

		for ($i = 1; $i <= $_GET['qte']; $i++) {
			if($oJoueur->GetArgent() < $_SESSION['main']['vente'][$id]['prix']){
				break;
			}
				
			$oJoueur->AddInventaire($_SESSION['main']['vente'][$id]['code'], 1, false);
			$oJoueur->MindOr($_SESSION['main']['vente'][$id]['prix']);
				
			$objMarche->RemoveMarchandise($_SESSION['main']['vente'][$id]['code']);
		}


		$objManager->UpdateMarche($objMarche);
		unset($_SESSION['main']['vente']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionVenteMarché';
	}
}
function ActionAnnulerTransaction(&$chkErr, $id, personnage &$oJoueur, &$objManager){
	if(isset($_GET['action']) and $_GET['action'] == 'annulertransaction'){
		UpdateTransaction($_SESSION['main']['transaction'][$id]['annuler'], 'annule');

		$sql = "SELECT vente_or, vente_nourriture, vente_bois, vente_pierre FROM table_marche WHERE ID_troc=".$_SESSION['main']['transaction'][$id]['annuler'].";";
		$requete = mysql_query($sql) or die (mysql_error());
		$row = mysql_fetch_array($requete, MYSQL_ASSOC);

		$maison = $oJoueur->GetObjSaMaison();

		$maison->AddRessource(maison::TYPE_RES_NOURRITURE, $row['vente_nourriture']);
		$maison->AddRessource(maison::TYPE_RES_PIERRE, $row['vente_pierre']);
		$maison->AddRessource(maison::TYPE_RES_BOIS, $row['vente_bois']);
		$oJoueur->AddOr($row['vente_or']);

		$objManager->UpdateBatiment($maison);

		unset($maison);
		unset($_GET['action']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionAnnulerTransaction';
	}
}
function ActionAccepterTransaction(&$chkErr, $id, personnage &$oJoueur, &$objManager){
	if(isset($_GET['action']) and $_GET['action'] == 'acceptertransaction'){
		$sql = "SELECT * FROM table_marche WHERE ID_troc=".$_SESSION['main']['transaction'][$id]['accepter'].";";
		$requete = mysql_query($sql) or die (mysql_error());
		$row = mysql_fetch_array($requete, MYSQL_ASSOC);

		$maisonA = $oJoueur->GetObjSaMaison();
		$maisonV = FoundBatiment(1, $row['vendeur']);
		$vendeur = $objManager->GetPersoLogin($row['vendeur']);

		if($maisonA->GetRessource(maison::TYPE_RES_NOURRITURE)	>= $row['achat_nourriture']){
			$checka = true;
		}else{$checka = false;
		}
		if($maisonA->GetRessource(maison::TYPE_RES_BOIS)		>= $row['achat_bois']){
			$checkb = true;
		}else{$checkb = false;
		}
		if($maisonA->GetRessource(maison::TYPE_RES_PIERRE)		>= $row['achat_pierre']){
			$checkc = true;
		}else{$checkc = false;
		}
		if($oJoueur->GetArgent()				>= $row['achat_or']){
			$checke = true;
		}else{$checke = false;
		}

		if($checka AND $checkb AND $checkc AND $checke){
			UpdateTransaction($_SESSION['main']['transaction'][$id]['accepter']);
			//l'acheteur recoit son dû
			$maisonA->AddRessource(maison::TYPE_RES_NOURRITURE, $row['vente_nourriture']);
			$maisonA->AddRessource(maison::TYPE_RES_BOIS, $row['vente_bois']);
			$maisonA->AddRessource(maison::TYPE_RES_PIERRE, $row['vente_pierre']);
			$oJoueur->AddOr($row['vente_or']);
			//l'acheteur paie
			$maisonA->MindRessource(maison::TYPE_RES_NOURRITURE, $row['achat_nourriture']);
			$maisonA->MindRessource(maison::TYPE_RES_BOIS, $row['achat_bois']);
			$maisonA->MindRessource(maison::TYPE_RES_PIERRE, $row['achat_pierre']);
			$oJoueur->MindOr($row['achat_or']);
			//le vendeur recoit son dû
			$maisonV->AddRessource(maison::TYPE_RES_NOURRITURE, $row['achat_nourriture']);
			$maisonV->AddRessource(maison::TYPE_RES_BOIS, $row['achat_bois']);
			$maisonV->AddRessource(maison::TYPE_RES_PIERRE, $row['achat_pierre']);
			$vendeur->AddOr($row['achat_or']);
				
			$objManager->update($vendeur);
			$objManager->UpdateBatiment($maisonA);
			$objManager->UpdateBatiment($maisonV);
		}

		unset($maison);
		unset($_GET['action']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionAccepterTransaction';
	}
}
function ActionTransactionMarche(&$chkErr, personnage &$oJoueur, &$objManager){
	if(isset($_POST['transaction'])){
		$_SESSION['transaction']['acceptation'] = null;

		$maison = $oJoueur->GetObjSaMaison();

		if($_POST['VOr']			> $oJoueur->GetArgent()){
			$_SESSION['transaction']['acceptation'] .= 'Transaction annulée : Pas assez d\'or<br />';
		}
		if($_POST['VBois']			> $maison->GetRessource(maison::TYPE_RES_BOIS)){
			$_SESSION['transaction']['acceptation'] .= 'Transaction annulée : Pas assez de bois<br />';
		}
		if($_POST['VPierre']		> $maison->GetRessource(maison::TYPE_RES_PIERRE)){
			$_SESSION['transaction']['acceptation'] .= 'Transaction annulée : Pas assez de pierre<br />';
		}
		if($_POST['VNourriture']	> $maison->GetRessource(maison::TYPE_RES_NOURRITURE)){
			$_SESSION['transaction']['acceptation'] .= 'Transaction annulée : Pas assez de nourriture<br />';
		}

		if(is_null($_SESSION['transaction']['acceptation'])){
			AddTransaction(	$oJoueur->GetLogin(),
							array('nourriture'=>$_POST['ANourriture'], 'pierre'=>$_POST['APierre'], 'bois'=>$_POST['ABois'], 'or'=>$_POST['AOr']),
							array('nourriture'=>$_POST['VNourriture'], 'pierre'=>$_POST['VPierre'], 'bois'=>$_POST['VBois'], 'or'=>$_POST['VOr']));
			
			$maison->MindRessource(maison::TYPE_RES_NOURRITURE, $_POST['VNourriture']);
			$maison->MindRessource(maison::TYPE_RES_PIERRE, $_POST['VPierre']);
			$maison->MindRessource(maison::TYPE_RES_BOIS, $_POST['VBois']);
			$oJoueur->MindOr($_POST['VOr']);
			$objManager->UpdateBatiment($maison);
		}

		unset($maison);
		unset($_POST['transaction']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionTransactionMarché';
	}
}

?>