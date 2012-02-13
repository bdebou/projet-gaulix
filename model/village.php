<?php
function CreateListBatiment(){
	global $objManager, $lstNonBatiment;
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	$lstBatiment = null;
	
	$sql = "SELECT * FROM table_carte WHERE login='".$_SESSION['joueur']."' AND detruit IS NULL;";
	$requete = mysql_query($sql) or die ( mysql_error() );
	
	if(mysql_num_rows($requete) > 0){
	
		while($carte = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if(!in_array($carte['id_type_batiment'], $lstNonBatiment)){
				$lstBatiment[] = AfficheBatiment(FoundBatiment(NULL, NULL, $carte['coordonnee']), $oJoueur, true);
			}
		}
	}
	
	$objManager->update($oJoueur);
	unset($oJoueur);
	
	return $lstBatiment;
}
function AfficheBatiment(&$batiment, &$oJoueur, $PageVillage = false){
	$ImgSize = 'height';
	$txt = '
	<table class="village">';

	$contenu = 'Ne peut rien contenir';
	$PositionBatiment	= implode(',', array_merge(array($batiment->GetCarte()),$batiment->GetCoordonnee()));
	$PositionJoueur		= implode(',', array_merge(array($oJoueur->GetCarte()),$oJoueur->GetPosition()));
	$chkDruide = false;
	$chkMarcher = false;

	switch($batiment->GetType()){
		case 'maison':
			$ImgSize = 'width';
			if(!$PageVillage OR $PositionBatiment == $PositionJoueur){
				$contenu = '<p>Ne peut rien contenir.</p>';
				$chkDruide = true;
			}else{
				$contenu = '<p>Vous devez vous placez sur son emplacement pour afficher les options.</p>';
			}
			break;
		case 'bank':
			$contenu = $batiment->AfficheContenu($PageVillage, $oJoueur);
			break;
		case 'entrepot':
		case 'ferme':
		case 'mine' :
			$ImgSize = 'width';
			$contenu = $batiment->AfficheContenu($PageVillage, $oJoueur);
			break;
		case 'marcher':
			if($PositionBatiment == $PositionJoueur){
				$chkMarcher = true;
			}else{
				$contenu = '<p>Vous devez vous placez sur son emplacement pour afficher les transactions disponibles.</p>';
			}
			break;
	}
	$txt .= '
		<tr>
			<td rowspan="'.($batiment->GetType() == 'entrepot'?'5':'6').'" style="width:400px;">
				<img alt="'.$batiment->GetType().'" src="./img/batiments/'.$batiment->GetType().'-'.$batiment->GetNiveau().'.png" width="400px" />
			</td>
			<th colspan="4"><a name="'.str_replace(',', '_', $PositionBatiment).'">'.$batiment->GetNom().' ('.$batiment->GetNiveau().' / '.batiment::NIVEAU_MAX.')</a></th>
		</tr>
		<tr>
			<td colspan="4">'.$batiment->AfficheOptionAmeliorer($oJoueur, $PageVillage).'</td>
		</tr>
		<tr>
			<td colspan="4">'.$batiment->GetDescription().'</td>
		</tr>
		<tr>
			<td colspan="4">'
	.'<img alt="Barre status" src="./fct/fct_image.php?type=statusetat&amp;value='.$batiment->GetEtat().'&amp;max='.$batiment->GetEtatMax().'" />'
	.'<br />'
	.$batiment->AfficheOptionReparer($oJoueur, $PageVillage)
	.'</td>
		</tr>
		<tr>
			<td colspan="4">
				<ul style="list-style-type:none; padding:0px; text-align:center; margin:0px;">'
	.'<li style="display:inline;">'.AfficheIcone('attaque').' : '.(is_null($batiment->GetAttaque())?'0':$batiment->GetAttaque()).'</li>'
	.'<li style="display:inline; margin-left:40px;">'.AfficheIcone('distance').' : '.(is_null($batiment->GetDistance())?'0':$batiment->GetDistance())	.'</li>'
	.'<li style="display:inline; margin-left:40px;">'.AfficheIcone('defense').' : '.(is_null($batiment->GetDefense())?'0':$batiment->GetDefense()).'</li>'
	.'</ul>
			</td>
		</tr>
		<tr>
			<td colspan="'.($batiment->GetType() == 'entrepot'?'5':'4').'">'.$contenu.'</td>
		</tr>'
	.($chkDruide?$batiment->AfficheDruide($oJoueur):'')
	.($chkMarcher?$batiment->AfficheTransactions($oJoueur):'')
	.'<tr style="background:lightgrey;"><td colspan="5">&nbsp;</td></tr>'
	.'</table>';
	return $txt;
}
function CheckRessource(&$oJoueur, &$batiment, &$maison){
	$lstRessource = array(	'ResBoi'	=> $batiment->GetPrixBois()			+ intval(($batiment->GetNiveau() / 2) * $batiment->GetPrixBois()),
							'ResPie'	=> $batiment->GetPrixPierre()		+ intval(($batiment->GetNiveau() / 2) * $batiment->GetPrixPierre()),
							'ResNou'	=> $batiment->GetPrixNourriture()	+ intval(($batiment->GetNiveau() / 2) * $batiment->GetPrixNourriture()),
							'ResOr'		=> $batiment->GetPrixOr()			+ intval(($batiment->GetNiveau() / 2) * $batiment->GetPrixOr()));

	foreach($lstRessource as $type=>$Valeur){
		if(!CheckIfAssezRessource(array($type, $Valeur), $oJoueur, $maison)){
			return  false;
		}
	}

	return true;
}
function AddTransaction($vendeur, $achat=array('nourriture'=>'NULL', 'pierre'=>'NULL', 'bois'=>'NULL', 'or'=>'NULL'), $vente=array('nourriture'=>'NULL', 'pierre'=>'NULL', 'bois'=>'NULL', 'or'=>'NULL')){
	$sql="INSERT INTO `table_marcher` (
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
	$sql = "UPDATE table_marcher SET
		acheteur=" . ($type == 'vendu' ? "'" . $_SESSION['joueur'] . "'" : 'NULL') . ", 
		status_vente=1 
		WHERE ID_troc=$id;";
	mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
}

//+---------------------------------+
//|				ACTIONS				|
//+---------------------------------+
function ActionAmeliorerBatiment(&$check, &$oJoueur, &$objManager, $coordonnee){
	if(isset($_SESSION['main'][$coordonnee])){

		$maison = FoundBatiment(1);
		$chk = true;

		if(!is_null($maison)){
				
			$batiment = FoundBatiment(null, null, str_replace('_', ',', $coordonnee));
				
			if(!is_null($batiment)){

				if($batiment->GetType() == 'maison'){
					$chk = false;
				}
				
				switch($batiment->GetStatusAmelioration()){
					case 'Go':
						if(CheckRessource($oJoueur, $batiment, $maison)){
							$oJoueur->MindOr($_SESSION['main'][$coordonnee]['prixAmelioration']['Or']);
							$maison->MindBois($_SESSION['main'][$coordonnee]['prixAmelioration']['Bois']);
							$maison->MindPierre($_SESSION['main'][$coordonnee]['prixAmelioration']['Pierre']);
							$maison->MindNourriture($_SESSION['main'][$coordonnee]['prixAmelioration']['Nourriture']);
							if($batiment->GetType() == 'maison'){
								$batiment = $maison;
							}
						}else{
							$check = false;
							echo 'Erreur GLX0005: Fonction ActionAmeliorerBatiment - Pas assez de ressource.';
						}
					case 'Finish':
						$batiment->Amelioration($batiment->GetStatusAmelioration());
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
function ActionReparer(&$check, $id, $num, &$oJoueur, &$objManager){
	if(isset($_SESSION['main']['Reparer'])){
		$batiment = FoundBatiment(null, $oJoueur->GetLogin(), $oJoueur->GetCoordonnee());
		
		if(!is_null($batiment)){
			if(CheckIfAssezRessource(array('ResOr', $_SESSION['main']['Reparer'][$id]['montant']), $oJoueur, FoundBatiment(1))){
				$batiment->Reparer($_SESSION['main']['Reparer'][$id]['pts'], $oJoueur);
				$oJoueur->MindOr($_SESSION['main']['Reparer'][$id]['montant']);
				$objManager->UpdateBatiment($batiment);
			}
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionReparer - Batiment Introuvable';
		}
		unset($_SESSION['main']['Reparer']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionReparer';
	}
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
					$oJoueur->AddInventaire($_SESSION['main'][$id]['reprendre']);
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
function ActionViderStock(&$check, $id, $type, &$oJoueur, &$objManager){
	if(isset($_SESSION['main'][$type]['vider'])){
		$maison = FoundBatiment(1);
		if(!is_null($maison)){
			$batiment = FoundBatiment($id, null, $oJoueur->GetCoordonnee());
			if(!is_null($batiment)){
				$batiment->ViderStock($_SESSION['main'][$type]['vider'], $maison, $oJoueur);
				$objManager->UpdateBatiment($batiment);
				unset($batiment);
			}else{
				$check = false;
				echo 'Erreur GLX0003: Fonction ActionViderStock - '.ucfirst(strtolower($type)).' Introuvable';
			}
			$objManager->UpdateBatiment($maison);
			unset($maison);
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionViderStock - Maison Introuvable';
		}
		unset($_SESSION['main'][$type]['vider']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionViderStock';
	}
}
function ActionProduction(&$check, $id, $NomBatiment, $type, &$oJoueur, &$objManager){
	$maison = FoundBatiment(1);
	if(!is_null($maison)){
		$batiment = FoundBatiment($id, null, $oJoueur->GetCoordonnee());
		if(!is_null($batiment)){
			$batiment->ViderStock($_SESSION['main'][$NomBatiment]['vider'], $maison, $oJoueur);
			//$batiment->ChangerProductionBatiment($_SESSION['main'][$NomBatiment]['production']);
			$batiment->ChangerProductionBatiment($type);
			$objManager->UpdateBatiment($batiment);
			$objManager->UpdateBatiment($maison);
			unset($batiment);
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionProduction - '.ucfirst(strtolower($NomBatiment)).' Introuvable';
		}
	}else{
		$check = false;
		echo 'Erreur GLX0003: Fonction ActionProduction - Maison Introuvable';
	}
}
function ActionStocker(&$check, $id, $type, &$objManager, &$oJoueur){
	if(isset($_SESSION['main'][$type]['stock'])){
		$batiment = FoundBatiment($id);
		if(!is_null($batiment)){
			$batiment->AddStock($oJoueur);
			$objManager->UpdateBatiment($batiment);
			unset($batiment);
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionStocker - '.ucfirst(strtolower($type)).' introuvable';
		}
		unset($_SESSION['main'][$type]['stock']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionStocker';
	}
}
function ActionDruide(&$chkErr, $id, &$oJoueur, &$objManager){
	if(isset($_SESSION['main']['druide'])){
		$maison = FoundBatiment(1);
		foreach($_SESSION['main']['druide'][$id] as $key=>$value){
			switch($key){
				case 'N' :
					if($value < 0){
						$maison->MindNourriture(abs($value));
					}
					elseif($value > 0){
						$maison->AddNourriture(abs($value));
					}
					break;
				case 'B':
					if($value < 0){
						$maison->MindBois(abs($value));
					}
					elseif($value > 0){
						$maison->AddBois(abs($value));
					}
					break;
				case 'P':
					if($value < 0){
						$maison->MindPierre(abs($value));
					}
					elseif($value > 0){
						$maison->AddPierre(abs($value));
					}
					break;
				case 'H':
					if($value < 0){
						$oJoueur->CleanInventaire('Hydromel', NULL, abs($value));
					}
					elseif($value > 0){
						$oJoueur->AddInventaire('Hydromel', NULL, abs($value), false);
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
						$oJoueur->AddInventaire('ResVie'.abs($value), NULL, 1, false);
					}
					break;
				case 'D':
					if($value > 0){
						$oJoueur->AddInventaire('ResDep'.abs($value), NULL, 1, false);
					}
					break;
			}
		}

		//on recoit le parchemin du sort
		if($_SESSION['main']['druide'][$id]['type'] != 'ressource'){
			$oJoueur->AddInventaire($_SESSION['main']['druide'][$id]['type'], NULL, 1, false);
		}

		$objManager->UpdateBatiment($maison);
		unset($maison);
		unset($_SESSION['main']['druide']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionDruide';
	}
}
function ActionVenteMarcher(&$check, $id, &$oJoueur, &$objManager){
	if(isset($_SESSION['main']['vente'])){
		$sql = "SELECT contenu_vendeur FROM table_marcher WHERE type_vendeur='marchant'";
		$requete = mysql_query($sql) or die (mysql_error());

		$objMarcher = new marchant(mysql_fetch_array($requete, MYSQL_ASSOC));

		for ($i = 1; $i <= $_GET['qte']; $i++) {
			if($oJoueur->GetArgent() < $_SESSION['main']['vente'][$id]['prix']){
				break;
			}
				
			$oJoueur->AddInventaire($_SESSION['main']['vente'][$id]['code'], $_SESSION['main']['vente'][$id]['type'], 1, false);
			$oJoueur->MindOr($_SESSION['main']['vente'][$id]['prix']);
				
			$objMarcher->RemoveMarchandise($_SESSION['main']['vente'][$id]['code']);
		}


		$objManager->UpdateMarcher($objMarcher);
		unset($_SESSION['main']['vente']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionVenteMarcher';
	}
}
function ActionAnnulerTransaction(&$chkErr, $id, &$oJoueur, &$objManager){
	if(isset($_GET['action']) and $_GET['action'] == 'annulertransaction'){
		UpdateTransaction($_SESSION['main']['transaction'][$id]['annuler'], 'annule');

		$sql = "SELECT vente_or, vente_nourriture, vente_bois, vente_pierre FROM table_marcher WHERE ID_troc=".$_SESSION['main']['transaction'][$id]['annuler'].";";
		$requete = mysql_query($sql) or die (mysql_error());
		$row = mysql_fetch_array($requete, MYSQL_ASSOC);

		$maison = FoundBatiment(1);

		$maison->AddNourriture($row['vente_nourriture']);
		$maison->AddPierre($row['vente_pierre']);
		$maison->AddBois($row['vente_bois']);
		$oJoueur->AddOr($row['vente_or']);

		$objManager->UpdateBatiment($maison);

		unset($maison);
		unset($_GET['action']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionAnnulerTransaction';
	}
}
function ActionAccepterTransaction(&$chkErr, $id, &$oJoueur, &$objManager){
	if(isset($_GET['action']) and $_GET['action'] == 'acceptertransaction'){
		$sql = "SELECT * FROM table_marcher WHERE ID_troc=".$_SESSION['main']['transaction'][$id]['accepter'].";";
		$requete = mysql_query($sql) or die (mysql_error());
		$row = mysql_fetch_array($requete, MYSQL_ASSOC);

		$maisonA = FoundBatiment(1);
		$maisonV = FoundBatiment(1, $row['vendeur']);
		$vendeur = $objManager->GetPersoLogin($row['vendeur']);

		if($maisonA->GetRessourceNourriture()	>= $row['achat_nourriture']){
			$checka = true;
		}else{$checka = false;
		}
		if($maisonA->GetRessourceBois()			>= $row['achat_bois']){
			$checkb = true;
		}else{$checkb = false;
		}
		if($maisonA->GetRessourcePierre()		>= $row['achat_pierre']){
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
			$maisonA->AddNourriture($row['vente_nourriture']);
			$maisonA->AddBois($row['vente_bois']);
			$maisonA->AddPierre($row['vente_pierre']);
			$oJoueur->AddOr($row['vente_or']);
			//l'acheteur paie
			$maisonA->MindNourriture($row['achat_nourriture']);
			$maisonA->MindBois($row['achat_bois']);
			$maisonA->MindPierre($row['achat_pierre']);
			$oJoueur->MindOr($row['achat_or']);
			//le vendeur recoit son dû
			$maisonV->AddNourriture($row['achat_nourriture']);
			$maisonV->AddBois($row['achat_bois']);
			$maisonV->AddPierre($row['achat_pierre']);
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
function ActionTransactionMarcher(&$chkErr, &$oJoueur, &$objManager){
	if(isset($_POST['transaction'])){
		$_SESSION['transaction']['acceptation'] = null;

		$maison = FoundBatiment(1);

		if($_POST['VOr']			> $oJoueur->GetArgent()){
			$_SESSION['transaction']['acceptation'] .= 'Transaction annulée : Pas assez d\'or<br />';
		}
		if($_POST['VBois']			> $maison->GetRessourceBois()){
			$_SESSION['transaction']['acceptation'] .= 'Transaction annulée : Pas assez de bois<br />';
		}
		if($_POST['VPierre']		> $maison->GetRessourcePierre()){
			$_SESSION['transaction']['acceptation'] .= 'Transaction annulée : Pas assez de pierre<br />';
		}
		if($_POST['VNourriture']	> $maison->GetRessourceNourriture()){
			$_SESSION['transaction']['acceptation'] .= 'Transaction annulée : Pas assez de nourriture<br />';
		}

		if(is_null($_SESSION['transaction']['acceptation'])){
			AddTransaction(	$oJoueur->GetLogin(),
							array('nourriture'=>$_POST['ANourriture'], 'pierre'=>$_POST['APierre'], 'bois'=>$_POST['ABois'], 'or'=>$_POST['AOr']),
							array('nourriture'=>$_POST['VNourriture'], 'pierre'=>$_POST['VPierre'], 'bois'=>$_POST['VBois'], 'or'=>$_POST['VOr']));
			
			$maison->MindNourriture($_POST['VNourriture']);
			$maison->MindPierre($_POST['VPierre']);
			$maison->MindBois($_POST['VBois']);
			$oJoueur->MindOr($_POST['VOr']);
			$objManager->UpdateBatiment($maison);
		}

		unset($maison);
		unset($_POST['transaction']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionTransactionMarcher';
	}
}

?>