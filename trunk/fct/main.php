<?php

// On enregistre notre autoload
function chargerClasse($classname){require './'.$classname.'.class.php';}
spl_autoload_register('chargerClasse');

session_start();

include('config.php');
include('fct_main.php');


$chkDebug = false;

global $objManager;

$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

if($chkDebug){echo '$_SESSION[\'main\']<br />';print_r($_SESSION['main']);echo '<br />';}
if($chkDebug){echo '$_GET<br />';print_r($_GET);echo '<br />';}
if($chkDebug){echo '$_POST<br />';print_r($_POST);echo '<br />';}
if($chkDebug){echo '<hr />';}

$chkErr = true;

if(isset($_GET['move']) AND $oJoueur->GetDepDispo() > 0){				ActionMove($chkErr, $oJoueur, $objManager);
}elseif(isset($_POST['depot'])){		ActionDepot($chkErr, $oJoueur, $objManager);
}elseif(isset($_POST['retrait'])){		ActionRetrait($chkErr, $oJoueur, $objManager);
}elseif(isset($_POST['transaction'])){	ActionTransactionMarcher($chkErr, $oJoueur, $objManager);
}elseif(isset($_GET['action'])){
	switch($_GET['action']){
		case 'stock':					ActionStock($chkErr, $oJoueur); break;
		case 'utiliser': 				if(isset($_GET['id'])){ActionUtiliser($chkErr, $_SESSION['main'][$_GET['id']], $oJoueur, $objManager);}
										else{ActionUtiliser($chkErr, $_SESSION['main']['objet'], $oJoueur, $objManager);}
										break;
		case 'equiper':					if(isset($_GET['id'])){ActionEquiper($chkErr, $_SESSION['main'][$_GET['id']], $oJoueur);}
										else{ActionEquiper($chkErr, $_SESSION['main']['objet'], $oJoueur);}
										break;
		case 'laisser':					ActionLaisser($chkErr, $oJoueur); break;
		case 'quete':					ActionQuete($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'construire':				ActionConstruire($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'frapper':					ActionFrapper($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'attaquer':				ActionAttaquer($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'deplacement':				ActionDeplacement($chkErr, $oJoueur); break;
		case 'stockerferme':			ActionStocker($chkErr, 6, 'ferme', $objManager, $oJoueur); break;
		case 'viderstockferme':			ActionViderStock($chkErr, 6, 'ferme', $oJoueur, $objManager); break;
		case 'productionferme':			ActionProduction($chkErr, 6, 'ferme', $_GET['type'], $oJoueur, $objManager); break;
		case 'reparer':					ActionReparer($chkErr, $_GET['id'], $_GET['num'], $oJoueur, $objManager); break;
		case 'reprendre':				ActionReprendre($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'PerfAtt':					ActionPerfAtt($chkErr, $oJoueur); break;
		case 'PerfDef':					ActionPerfDef($chkErr, $oJoueur); break;
		case 'unuse':					ActionUnuse($chkErr, $oJoueur); break;
		case 'entreposer':				ActionEntreposer($chkErr, $objManager, $_GET['id'], $oJoueur); break;
		case 'vendre':					ActionVendre($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'ressource':				ActionRessource($chkErr, $oJoueur, $objManager, (isset($_GET['id'])?$_GET['id']:NULL)); break;
		case 'ameliorer':				ActionAmeliorerBatiment($chkErr, $oJoueur, $objManager, $_GET['id']); break;
		case 'druide':					ActionDruide($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'annulertransaction':		ActionAnnulerTransaction($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'acceptertransaction':		ActionAccepterTransaction($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'stockermine':				ActionStocker($chkErr, 18, 'mine', $objManager, $oJoueur); break;
		case 'viderstockmine':			ActionViderStock($chkErr, 18, 'mine', $oJoueur, $objManager); break;
		case 'productionmine':			ActionProduction($chkErr, 18, 'mine', $_GET['type'], $oJoueur, $objManager); break;
		case 'competence':				ActionCompetence($chkErr, $oJoueur, $_GET['cmp'], $objManager); break;
		case 'chasser':					ActionChasser($chkErr, $oJoueur, $objManager); break;
		case 'fabriquer':				ActionFabriquer($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'VenteMarcher':			ActionVenteMarcher($chkErr, $_GET['id'], $oJoueur, $objManager); break;
		case 'MettreBolga':				ActionMettreDansBolga($chkErr, $_GET['type'], $oJoueur, $objManager); break;
		case 'legionnaire':				ActionLegionnaire($chkErr, $oJoueur); break;
		default:
			$chkErr = false;
			echo 'Erreur GLX0003: Pas d\'action correcte <br />';
			print_r($_SESSION['main']);
	}
}else{
	$chkErr = false;
	echo 'Erreur: GLX0001';
}

if($chkDebug){print_r($_SESSION['main']);}

$objManager->update($oJoueur);
if($chkErr AND !$chkDebug){echo RetourPage($_SESSION['main']['uri'], true);}
else{echo '<br /><a href="'.RetourPage($_SESSION['main']['uri'], true, false).'">Retour</a>';}


//Les Actions
//===========
function ActionLegionnaire(&$check, &$oJoueur){
	if(isset($_SESSION['main']['legionnaire'])){
		$_SESSION['message'][] = $_SESSION['main']['legionnaire']->CombatLegionnaire($oJoueur);
		unset($_SESSION['main']['legionnaire']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionLegionnaire';
	}
}
function ActionMettreDansBolga(&$check, $type, &$oJoueur, &$objManager){
	if(isset($_SESSION['main']['LoginStatus'])){
		//On vérifie si le bolga est plein ou pas
		if(count($oJoueur->GetLstInventaire()) >= $oJoueur->QuelCapaciteMonBolga()){
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionMettreDansBolga - Bolga plein';
			return;
		}
		//on vérifie si on a bien la quantitée
		if(!isset($_SESSION['main']['LoginStatus'][$type])){
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionMettreDansBolga - Pas assez de ressource';
			return;
		}
		//Si tout OK, alors on transfert
		$maison = FoundBatiment(1);
		switch($type){
			case 'Nourriture':
				$maison->MindNourriture($_SESSION['main']['LoginStatus'][$type]);
				break;
			case 'Bois':
				$maison->MindBois($_SESSION['main']['LoginStatus'][$type]);
				break;
			case 'Pierre':
				$maison->MindPierre($_SESSION['main']['LoginStatus'][$type]);
				break;
		}
		$oJoueur->AddInventaire('Res'.ucfirst(substr($type, 0, 3)).$_SESSION['main']['LoginStatus'][$type], strtolower($type), 1, false);
		
		$objManager->UpdateBatiment($maison);
		
		unset($_SESSION['main']['LoginStatus']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionMettreDansBolga';
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
function ActionFabriquer(&$check, $id, &$oJoueur, &$objManager){
	global $lstPoints;
	if(isset($_SESSION['main']['bricolage'][$id])){
			//on trouve la maison
		$maison = FoundBatiment(1);
		
		if(!is_null($maison)){
			$LstPrix = explode(',', $_SESSION['main']['bricolage'][$id]['prix']);
			
			foreach($LstPrix as $Prix){
				$arPrix = explode('=', $Prix);
				//on vérifie si on a assez de ressource
				if(!CheckIfAssezRessource(array($arPrix['0'], ($arPrix['1'] * abs($_GET['qte']))), $oJoueur, $maison)){
					$check = false;
					break;
				}
				
				if(in_array($arPrix['0'], array('ResBoi', 'ResPie', 'ResNou', 'ResOr'))){
					switch($arPrix['0']){
						case 'ResNou':	$maison->MindNourriture($arPrix['1'] * abs($_GET['qte']));	break;
						case 'ResPie':	$maison->MindPierre($arPrix['1'] * abs($_GET['qte']));		break;
						case 'ResBoi':	$maison->MindBois($arPrix['1'] * abs($_GET['qte']));		break;
						case 'ResOr':	$oJoueur->MindOr($arPrix['1'] * abs($_GET['qte']));			break;
					}
					
				}else{
					for($i=1;$i<=($arPrix['1'] * abs($_GET['qte']));$i++){
						$oJoueur->CleanInventaire($arPrix['0']);
					}
				}
			}
			
			$objManager->UpdateBatiment($maison);
			unset($maison);
			
			if(!$check){
				echo 'Erreur GLX0004: Fonction ActionFabriquer - Pas assez de ressource';
			}else{
					//on ajoute le nouvel objet dans son bolga
				$oJoueur->AddInventaire($_SESSION['main']['bricolage'][$id]['code'], $_SESSION['main']['bricolage'][$id]['type'], abs($_GET['qte']), false);
					//on gagne des points
				$oJoueur->UpdatePoints($lstPoints['ObjFabriqué']);
			}
				
			
			unset($_SESSION['main']['bricolage']);
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionFabriquer - Pas de maison trouvée';
		}
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionFabriquer';
	}
}
function ActionChasser(&$check, &$oJoueur, &$objManager){
	if(!is_null($_SESSION['main']['chasser'])){
		$maison = FoundBatiment(1);
		$maison->AddNourriture($_SESSION['main']['chasser']['nourriture']);
		if(!is_null($_SESSION['main']['chasser']['cuir'])){$oJoueur->AddInventaire('ResCuir', NULL, $_SESSION['main']['chasser']['cuir'], false);}
		$oJoueur->PerdreVie($_SESSION['main']['chasser']['attaque'], 'chasse');
		
		$objManager->UpdateBatiment($maison);
		unset($maison);
		
		$_SESSION['main']['chasser'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionChasser';
	}
}
function ActionCompetence(&$check, &$oJoueur, $cmp, &$objManager){
	if(!is_null($_SESSION['main'][$cmp])){
		$maison = FoundBatiment(1);
		
		AddEnregistrementCompetence($cmp, $_SESSION['main'][$cmp]['niveau'], $_SESSION['main'][$cmp]['temp']);
		
		$maison->MindNourriture($_SESSION['main'][$cmp]['prix_nourriture']);
		$maison->MindBois($_SESSION['main'][$cmp]['prix_bois']);
		$maison->MindPierre($_SESSION['main'][$cmp]['prix_pierre']);
		$oJoueur->CleanInventaire('Hydromel', NULL, $_SESSION['main'][$cmp]['prix_hydromel']);
		$oJoueur->MindOr($_SESSION['main'][$cmp]['prix_or']);
		
		$objManager->UpdateBatiment($maison);
		unset($maison);
		
		$_SESSION['main'][$cmp] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionCompetence';
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
		unset($_GET);
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
		
		if($maisonA->GetRessourceNourriture()	>= $row['achat_nourriture']){	$checka = true;}else{$checka = false;}
		if($maisonA->GetRessourceBois()			>= $row['achat_bois']){			$checkb = true;}else{$checkb = false;}
		if($maisonA->GetRessourcePierre()		>= $row['achat_pierre']){		$checkc = true;}else{$checkc = false;}
		if($oJoueur->GetArgent()				>= $row['achat_or']){			$checke = true;}else{$checke = false;}
		
		if($checka AND $checkb AND $checkc AND $checkd AND $checke){
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
		unset($_GET);
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
			AddTransaction($oJoueur->GetLogin(), 
					array('nourriture'=>$_POST['ANourriture'], 'pierre'=>$_POST['APierre'], 'bois'=>$_POST['ABois'], 'or'=>$_POST['AOr']), 
					array('nourriture'=>$_POST['VNourriture'], 'pierre'=>$_POST['VPierre'], 'bois'=>$_POST['VBois'], 'or'=>$_POST['VOr']));
			$maison->MindNourriture($_POST['VNourriture']);
			$maison->MindPierre($_POST['VPierre']);
			$maison->MindBois($_POST['VBois']);
			$oJoueur->MindOr($_POST['VOr']);
			$objManager->UpdateBatiment($maison);
		}
		
		unset($maison);
		unset($_POST);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionTransactionMarcher';
	}
}
function ActionDruide(&$chkErr, $id, &$oJoueur, &$objManager){
	if(isset($_SESSION['main']['druide'])){
		$maison = FoundBatiment(1);
		foreach($_SESSION['main']['druide'][$id] as $key=>$value){
			switch($key){
				case 'N' : 
					if($value < 0){$maison->MindNourriture(abs($value));}
					elseif($value > 0){$maison->AddNourriture(abs($value));}
					break;
				case 'B': 
					if($value < 0){$maison->MindBois(abs($value));}
					elseif($value > 0){$maison->AddBois(abs($value));}
					break;
				case 'P': 
					if($value < 0){$maison->MindPierre(abs($value));}
					elseif($value > 0){$maison->AddPierre(abs($value));}
					break;
				case 'H': 
					if($value < 0){$oJoueur->CleanInventaire('Hydromel', NULL, abs($value));}
					elseif($value > 0){$oJoueur->AddInventaire('Hydromel', NULL, abs($value), false);}
					break;
				case 'O': 
					if($value < 0){$oJoueur->MindOr(abs($value));}
					elseif($value > 0){$oJoueur->AddOr(abs($value));}
					break;
				case 'V':
					if($value < 0){$oJoueur->PerdreVie(abs($value));}
					elseif($value > 0){$oJoueur->AddInventaire('ResVie'.abs($value), NULL, 1, false);}
					break;
				case 'D':
					if($value > 0){$oJoueur->AddInventaire('ResDep'.abs($value), NULL, 1, false);}
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
						$oJoueur->MindOr($_SESSION['main'][$coordonnee]['prixAmelioration']['Or']);
						$maison->MindBois($_SESSION['main'][$coordonnee]['prixAmelioration']['Bois']);
						$maison->MindPierre($_SESSION['main'][$coordonnee]['prixAmelioration']['Pierre']);
						$maison->MindNourriture($_SESSION['main'][$coordonnee]['prixAmelioration']['Nourriture']);
						if($batiment->GetType() == 'maison'){
							$batiment = $maison;
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
			if($chk){$objManager->UpdateBatiment($maison);}
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
function ActionRessource(&$check, &$oJoueur, &$objManager, $id = NULL){
	if(isset($_SESSION['main']['ressource'])){
		if(is_null($_SESSION['main']['ressource']->GetCollecteur())){
			$_SESSION['main']['ressource']->StartCollect($oJoueur, $id);
		}elseif((strtotime('now') - $_SESSION['main']['ressource']->GetDateDebutAction()) >= $_SESSION['main']['ressource']->GetTempRessource()){
			if($oJoueur->GetLogin() == $_SESSION['main']['ressource']->GetCollecteur()){
				$oMaison = FoundBatiment(1, $oJoueur->GetLogin());
				$_SESSION['main']['ressource']->FinishCollect($oJoueur, $oMaison);
			}else{
				$oCollecteur = $objManager->GetPersoLogin($_SESSION['main']['ressource']->GetCollecteur());
				$oMaison = FoundBatiment(1, $oCollecteur->GetLogin());
				$_SESSION['main']['ressource']->FinishCollect($oCollecteur, $oMaison);
				$objManager->update($oCollecteur);
				unset($oCollecteur);
			}
			$objManager->UpdateBatiment($oMaison);
			unset($oMaison);
		}
		$objManager->UpdateRessource($_SESSION['main']['ressource']);
		unset($_SESSION['main']['ressource']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionRessource';
	}
}
function ActionVendre(&$check, $id, &$oJoueur, &$objManager){
	if(isset($_SESSION['main'][$id]['code'])){
		
		$sql = "SELECT contenu_vendeur FROM table_marcher WHERE type_vendeur='marchant'";
		$requete = mysql_query($sql) or die (mysql_error());
		
		$objMarcher = new marchant(mysql_fetch_array($requete, MYSQL_ASSOC));
		
		if($oJoueur->GetCombienElementDansBolga($_SESSION['main'][$id]['code']) == 0){
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionVendre : Pas assez d\'éléments';
		}elseif (abs($_GET['qte']) > $oJoueur->GetCombienElementDansBolga($_SESSION['main'][$id]['code'])){
			$_GET['qte'] = $oJoueur->GetCombienElementDansBolga($_SESSION['main'][$id]['code']);
		}
		
		if($check){
			for ($i = 1; $i <= $_GET['qte']; $i++) {
				$oJoueur->VendreObjet($_SESSION['main'][$id]['code'], $_SESSION['main'][$id]['prix']);
				$objMarcher->AddMarchandise($_SESSION['main'][$id]['code']);
			}
			
			$objManager->UpdateMarcher($objMarcher);
			
		}
		
		unset($_SESSION['main'][$id]['code']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionVendre';
	}
}
function ActionEntreposer(&$check, $objManager, $id, &$oJoueur){
	if(isset($_SESSION['main'][$id]['code'])){
		
		$entrepot = FoundBatiment(4);
		
		if(!is_null($entrepot)){
			
			if($oJoueur->GetCombienElementDansBolga($_SESSION['main'][$id]['code']) == 0){
				$check = false;
				echo 'Erreur GLX0004: Fonction ActionEntreposer : Pas assez d\'éléments';
			}elseif (abs($_GET['qte']) > $oJoueur->GetCombienElementDansBolga($_SESSION['main'][$id]['code'])){
				$_GET['qte'] = $oJoueur->GetCombienElementDansBolga($_SESSION['main'][$id]['code']);
			}
			
			if($check){
				for ($i = 1; $i <= $_GET['qte']; $i++) {
					
					$oJoueur->CleanInventaire($_SESSION['main'][$id]['code']);
					$entrepot->AddContenu($_SESSION['main'][$id]['code']);
				
				}
			
				$objManager->UpdateBatiment($entrepot);
			
			}
		
		}else{
			
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionEntreposer - Entrepot Introuvable';
		
		}
		unset($_SESSION['main'][$id]['code']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionEntreposer';
	}
}
function ActionUnuse(&$check, &$oJoueur){
	if(isset($_GET['id'])){
		switch($_GET['id']){
			case 1:	$type = 'casque';	break;
			case 2:	$type = 'arme';		break;
			case 3:	$type = 'cuirasse';	break;
			case 4:	$type = 'bouclier';	break;
			case 5:	$type = 'jambiere';	break;
			case 6:	$type = 'sac';		break;
		}
		$oJoueur->DesequiperPerso($type);
		unset($_GET['id']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionUnuse';
	}
}
Function ActionPerfDef(&$check, &$oJoueur){
	if(!is_null($_SESSION['main']['PerfDef'])){
		switch($_SESSION['main']['PerfDef']){
			case 'Go':
				$oJoueur->LaunchPerfDefense($_SESSION['main']['TmpDef'], $_SESSION['main']['PrixDef'],1);
				break;
			case 'Finish':
				$oJoueur->LaunchPerfDefense(null,null,2);
				break;
		}
		$_SESSION['main']['PerfDef'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionPerfDef';
	}
}
function ActionPerfAtt(&$check, &$oJoueur){
	if(!is_null($_SESSION['main']['PerfAtt'])){
		switch($_SESSION['main']['PerfAtt']){
			case 'Go':
				$oJoueur->LaunchPerfAttaque($_SESSION['main']['TmpAtt'],$_SESSION['main']['PrixAtt'],1);
				break;
			case 'Finish':
				$oJoueur->LaunchPerfAttaque(null,null,2);
				break;
		}
		$_SESSION['main']['PerfAtt'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionPerfAtt';
	}
}
function ActionDepot(&$check, &$oJoueur, &$objManager){
	if(!is_null($_POST['depot'])){
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
			$_POST['depot'] = null;
		}
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionDepot';
	}
}
function ActionRetrait(&$check, &$oJoueur, &$objManager){
	if(!is_null($_POST['retrait'])){
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
		$_POST['retrait'] = null;
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
function ActionReparer(&$check, $id, $num, &$oJoueur, &$objManager){
	if(isset($_SESSION['main']['Reparer'])){
		$batiment = FoundBatiment(null, $oJoueur->GetLogin(), implode(',', array_merge(array($oJoueur->GetCarte()), $oJoueur->GetPosition())));
		if(!is_null($batiment)){
			$batiment->Reparer($_SESSION['main']['Reparer'][$id]['pts'], $oJoueur);
			$oJoueur->MindOr($_SESSION['main']['Reparer'][$id]['montant']);
			$objManager->UpdateBatiment($batiment);
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
function ActionViderStock(&$check, $id, $type, &$oJoueur, &$objManager){
	if(!is_null($_SESSION['main'][$type]['vider'])){
		$maison = FoundBatiment(1);
		if(!is_null($maison)){
			$batiment = FoundBatiment($id, null, $_SESSION['main']['position']);
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
		$_SESSION['main'][$type]['vider'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionViderStock';
	}
}
function ActionProduction(&$check, $id, $NomBatiment, $type, &$oJoueur, &$objManager){
	$maison = FoundBatiment(1);
	if(!is_null($maison)){
		$batiment = FoundBatiment($id, null, $_SESSION['main']['position']);
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
	if(!is_null($_SESSION['main'][$type]['stock'])){
		$batiment = FoundBatiment($id);
		if(!is_null($batiment)){
			$batiment->AddStock($oJoueur);
			$objManager->UpdateBatiment($batiment);
			unset($batiment);
		}else{
			$check = false;
			echo 'Erreur GLX0003: Fonction ActionStocker - '.ucfirst(strtolower($type)).' introuvable';
		}
		$_SESSION['main'][$type]['stock'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionStocker';
	}
}
function ActionDeplacement(&$check, &$oJoueur){
	if(!is_null($_SESSION['main']['deplacement'])){
		global $temp_attente, $nbDeplacement;
		switch($_SESSION['main']['deplacement']){
			case 'new':
				if($temp_attente-(strtotime('now')-$oJoueur->GetLastAction())<=0){
					$oJoueur->AddDeplacement($nbDeplacement,'new');
				}
				break;
		}
		$_SESSION['main']['deplacement'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionDeplacement';
	}
}
function ActionAttaquer(&$check, $id, &$oJoueur, &$objManager){
	if(!is_null($_SESSION['main']['attaquer'][$id])){
		$BatimentAAttaquer = $objManager->GetBatiment(strval($_SESSION['main']['attaquer'][$id]));
		//$BatimentAAttaquer = FoundBatiment(NULL, NULL, strval($_SESSION['main']['attaquer'][$id]));
		
		if($BatimentAAttaquer->GetLogin() == 'romain'){
			$PersoAttaque = new personnage(array('login'=>'romain', 'not_attaque'=>NULL, 'nb_points'=>0));
		}else{
			$PersoAttaque = $objManager->GetPersoLogin($BatimentAAttaquer->GetLogin());
		}
		
		$_SESSION['retour_attaque'][] = $BatimentAAttaquer->GetLogin();
		$_SESSION['retour_attaque'][] = $BatimentAAttaquer->AttaquerBatiment($PersoAttaque, $oJoueur);
		
		$objManager->update($PersoAttaque);
		$objManager->UpdateBatiment($BatimentAAttaquer);
		
		$info = end($_SESSION['retour_attaque']);
		
		unset($BatimentAAttaquer);
		unset($PersoAttaque);
		$_SESSION['main']['attaquer'][$id] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionAttaquer';
	}
}
function ActionFrapper(&$check, $id, &$oJoueur, &$objManager){
	if(!is_null($_SESSION['main']['frapper'][$id])){
		$PersoAFrapper = $objManager->get(intval($_SESSION['main']['frapper'][$id]));
		$_SESSION['retour_combat'][] = $PersoAFrapper->GetLogin();
		$_SESSION['retour_combat'][] = $oJoueur->frapper($PersoAFrapper);
		$objManager->update($PersoAFrapper);
		$info = end($_SESSION['retour_combat']);
		//on envoie un mail
		if($PersoAFrapper->GetNotifCombat()){NotificationMail($PersoAFrapper->GetMail(), 'combat', $oJoueur->GetLogin(), $info['1']);}
		//on ajoute un historique
		AddHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'combat', $PersoAFrapper->GetLogin(), NULL, $info['0']);
		AddHistory($PersoAFrapper->GetLogin(), $PersoAFrapper->GetCarte(), $PersoAFrapper->GetPosition(), 'combat', $oJoueur->GetLogin(), NULL, $info['1']);
		$_SESSION['main']['frapper'][$id] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionFrapper';
	}
}
function ActionMove(&$check, &$oJoueur, &$objManager){
	//on reset la variable MESSAGE
	unset($_SESSION['message']);
	
	//on libère la ressource avant de bouger
	if(isset($_SESSION['main']['ressource'])){
		if($_SESSION['main']['ressource']->GetCollecteur() == $oJoueur->GetLogin()){
			$_SESSION['main']['ressource']->FreeRessource($oJoueur);
		}
		$objManager->UpdateRessource($_SESSION['main']['ressource']);
		unset($_SESSION['main']['ressource']);
	}
		
	//on déplace le joueur
	if(!is_null($_GET['move']) AND $oJoueur->CheckMove(strtolower($_GET['move']))){
		$oJoueur->deplacer($_GET['move']);
		$_GET['move'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionMove';
	}
	
	//on vérifie si on a trouvé une quete
	if(isset($_SESSION['QueteEnCours'])){
		foreach($_SESSION['QueteEnCours'] as $Quete){
			$Quete->ActionSurQuete($oJoueur);
			$objManager->UpdateQuete($Quete);
		}
	}
	
	//on se fait voler par un voleur
	$num = mt_rand(1, 1000);
	if($num == 100){
		$oJoueur->ArgentVole();
		$msg = '<p>Un voleur vous a dérobé tout votre argent.</p>';
		$_SESSION['message'][] = $msg;
		AddHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'voleur', 'Voleur', NULL, $msg);
	}
	
	//on se fait attaquer par une tour
	if(!$oJoueur->GetAttaqueTour()){
		$tmp = AttaqueTour($oJoueur);
		if(!is_null($tmp)){
			$_SESSION['message'][] = '<p>Vous avez été attaqué par une ou des tours. Vous êtes blessé de '.$tmp.'pts de vie.</p>';
		}
	}
		
	if(isset($_SESSION['QueteEnCours'])){reset($_SESSION['QueteEnCours']);}
	
	unset($_SESSION['retour_combat']);
	unset($_SESSION['retour_attaque']);
}
function ActionConstruire(&$check, $id, &$oJoueur, &$objManager){
	if($_SESSION['main'][$id]['construire'] == 1){
		$oJoueur->MaisonInstalle($_SESSION['main']['position']);
		$ressource = array('pierre'=>0, 'bois'=>0, 'nourriture'=>0, 'hydromel'=>0);
	}
	
	// on recupère les info du batiment
	$sql = "SELECT * FROM table_batiment WHERE id_batiment=".$_SESSION['main'][$id]['construire'].";";
	$requete = mysql_query($sql) or die ( mysql_error() );
	$batiment = mysql_fetch_array($requete, MYSQL_ASSOC);
	
	// on ajoute le batiment à la carte
	if($_SESSION['main'][$id]['construire']==1){
		AddCaseCarte($_SESSION['main']['position'], $oJoueur->GetLogin(), $_SESSION['main'][$id]['construire'], $batiment['batiment_vie'], $batiment['batiment_niveau'], $ressource);
	}else{
		AddCaseCarte($_SESSION['main']['position'], $oJoueur->GetLogin(), $_SESSION['main'][$id]['construire'], $batiment['batiment_vie'], $batiment['batiment_niveau']);
	}
		//on gagne des points
	$oJoueur->UpdatePoints($batiment['batiment_points']);
		//on ajoute un historique que le batiment est construit
	AddHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'Construction', NULL, NULL, 'Batiment construit. ID du batiment = '.$_SESSION['main'][$id]['construire']);
		//On trouve la maison
	$maison = FoundBatiment(1);
		//on paie le batiment
	if(isset($_SESSION['main'][$id]['prix_or'])){			$oJoueur->MindOr($_SESSION['main'][$id]['prix_or']);}
	if(isset($_SESSION['main'][$id]['prix_bois'])){			$maison->MindBois($_SESSION['main'][$id]['prix_bois']);}
	if(isset($_SESSION['main'][$id]['prix_pierre'])){		$maison->MindPierre($_SESSION['main'][$id]['prix_pierre']);}
	if(isset($_SESSION['main'][$id]['prix_nourriture'])){	$maison->MindNourriture($_SESSION['main'][$id]['prix_nourriture']);}
	
	$objManager->UpdateBatiment($maison);
	unset($maison);
}
function ActionQuete(&$check, $id, &$oJoueur, &$objManager){
	reset($_SESSION['QueteEnCours']);
	
	foreach($_SESSION['QueteEnCours'] as $key=>$Quete){
		if($Quete->GetIDQuete() == $id){
			$_SESSION['message'][] = $Quete->ActionSurQueteCombat($oJoueur);
			$objManager->UpdateQuete($Quete);
			if($Quete->GetVie() <= 0){
				unset($_SESSION['QueteEnCours'][$key]);
			}
			break;
		}
	}
}
function ActionLaisser(&$check, &$oJoueur){
	if(isset($_GET['type'])){
		if($_GET['type'] == 'objet'){$oJoueur->SetLastObject(true,null);}
		unset($_SESSION['main'][$_GET['type']]);
	}elseif(isset($_GET['id'])){
		$oJoueur->CleanInventaire($_SESSION['main'][$_GET['id']]['code'], true);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionLaisser';
	}
}
function ActionEquiper(&$check, &$arInfoObject, &$oJoueur){
	if(!is_null($arInfoObject['code'])){
		if($arInfoObject['action']){
			$oJoueur->AddInventaire($arInfoObject['code'], $arInfoObject['type']);
		}
		$oJoueur->EquiperPerso($arInfoObject['code'], $arInfoObject['type']);
		$arInfoObject['code'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionEquiper';
	}
}
function ActionUtiliser(&$check, &$arInfoObject, &$oJoueur, &$objManager){
	if(!is_null($arInfoObject['code'])){
		if($arInfoObject['action']){
			$oJoueur->AddInventaire($arInfoObject['code'], $arInfoObject['type']);
		}
				
		$maison = FoundBatiment(1);
		
		switch($arInfoObject['type']){
			case 'vie':			$oJoueur->GagnerVie($arInfoObject['value']);							break;
			case 'deplacement':	$oJoueur->AddDeplacement($arInfoObject['value'],'objet');				break;
			case 'argent':		$oJoueur->AddOr($arInfoObject['value']);								break;
			case 'nourriture':	if(!is_null($maison)){$maison->AddNourriture($arInfoObject['value']);}	break;
			case 'bois':		if(!is_null($maison)){$maison->AddBois($arInfoObject['value']);}		break;
			case 'pierre':		if(!is_null($maison)){$maison->AddPierre($arInfoObject['value']);}		break;
		}
		
		if(!is_null($maison)){
			$objManager->UpdateBatiment($maison);
			unset($maison);
		}
		
		$oJoueur->CleanInventaire($arInfoObject['code']);
		$arInfoObject['code'] = null;
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionUtiliser';
	}
}
function ActionStock(&$check, &$oJoueur){
	if(isset($_SESSION['main']['objet'])){
		$oJoueur->AddInventaire($_SESSION['main']['objet']['code'], null, 1);
		unset($_SESSION['main']['objet']);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionStock';
	}
}

function AddEnregistrementCompetence($nom, $niveau, $duree){
	$sql = "INSERT INTO `table_competence` 
		(`cmp_id`, `cmp_login`, `cmp_nom`, `cmp_niveau`, `cmp_temp`, `cmp_date`, `cmp_finish`)
		VALUES
		(NULL, '".$_SESSION['joueur']."', '$nom', $niveau, $duree, '".date('Y-m-d H:i:s')."', NULL);";
	mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);
}
function AddCaseCarte($position, $login, $IDBatiment, $etat, $niveau, $res=array('pierre'=>'NULL', 'bois'=>'NULL', 'nourriture'=>'NULL')){
	$sql="INSERT INTO `table_carte` (
	`id_case_carte`, 
	`coordonnee`, 
	`login`, 
	`id_type_batiment`, 
	`contenu_batiment`, 
	`res_pierre`, `res_bois`, `res_nourriture`, `date_action_batiment`, 
	`etat_batiment`, 
	`date_last_attaque`, 
	`detruit`, 
	`niveau_batiment`) VALUES (
	NULL, 
	'$position', 
	'$login', 
	$IDBatiment, 
	".(($IDBatiment == 6 or $IDBatiment == 18)?"'0,0'":'NULL').", 
	".$res['pierre'].", ".$res['bois'].", ".$res['nourriture'].", ".(($IDBatiment == '6' or $IDBatiment == '18')?"'".date('Y-m-d H:i:s')."'":'NULL').", 
	$etat, 
	NULL, 
	NULL, 
	$niveau);";
	mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);
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


function AttaqueTour(&$oJoueur){
	global $lstPoints;
	
	$ptsViePerduTour=null;
	$arDef = $oJoueur->GetDefPerso();
	$DefenseJoueur = intval($arDef['0'] + $arDef['1']);
	$sql = "SELECT coordonnee, login, niveau_batiment FROM table_carte WHERE login NOT IN('".implode("', '", ListeMembreClan($oJoueur->GetClan()))."') AND detruit IS NULL AND id_type_batiment=3;";
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	if(mysql_num_rows($requete) > 0){
		while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
			if($row['login'] == 'romain'){
				$distance = ($oJoueur->GetNiveau()>=10?3:2);
			}else{
				$distance = 2 + $row['niveau_batiment'];
			}
			$arTour = ZoneAttaqueTour($oJoueur->GetPosition(), $distance, $oJoueur->GetCarte());
			if(in_array($row['coordonnee'], $arTour)){
				if($row['login'] == 'romain'){
					$ptsDegat = (20 + (10 * ($oJoueur->GetNiveau()>15?15:$oJoueur->GetNiveau()))) - $DefenseJoueur;
				}else{
					$ptsDegat = (20 + (10 * $row['niveau_batiment'])) - $DefenseJoueur;
				}
				if($ptsDegat > 0){
					$oJoueur->PerdreVie($ptsDegat, 'tour');
					$oJoueur->UpdatePoints($lstPoints['AttTour']);
					$ptsViePerduTour += $ptsDegat;
					AddHistory($oJoueur->GetLogin(), $oJoueur->GetCarte(), $oJoueur->GetPosition(), 'attaque', $row['login'], NULL, 'La Tour de '.$row['login'].' vous a attaqué et blessé de '.$ptsDegat.'pts de vie.');
				}
			}
		}
	}
	return $ptsViePerduTour;
}
function ZoneAttaqueTour($position, $distance, $carte){
	global $nbColonneCarte, $nbLigneCarte;
	$chkDirection = array('VH'=>true, 'VB'=>true, 'HG'=>true, 'HD'=>true, 'OHG'=>true, 'OHD'=>true, 'OBG'=>true, 'OBD'=>true);
	$lstCoordonnee[] = implode(',', array_merge(array($carte),$position));
	for($i=1;$i<=$distance;$i++){
		//la direction verticale haut
		if(($position['1']-$i)<0){$chkDirection['VH'] = false;}
		if($chkDirection['VH']){$lstCoordonnee[] = implode(',', array($carte, $position['0'], ($position['1'] - $i)));}
		//la direction verticale bas
		if(($position['1']+$i)>$nbLigneCarte){$chkDirection['VB'] = false;}
		if($chkDirection['VB']){$lstCoordonnee[] = implode(',', array($carte, $position['0'], ($position['1'] + $i)));}
		//la direction horizontale gauche
		if(($position['0']-$i)<0){$chkDirection['HG'] = false;}
		if($chkDirection['HG']){$lstCoordonnee[] = implode(',', array($carte, ($position['0'] - $i), $position['1']));}
		//la direction horizontale droite
		if(($position['0']+$i)>$nbColonneCarte){$chkDirection['HD'] = false;}
		if($chkDirection['HD']){$lstCoordonnee[] = implode(',', array($carte, ($position['0'] + $i), $position['1']));}
		//la direction oblique HG
		if(($position['0']-$i)<0 or ($position['1']-$i)<0){$chkDirection['OHG'] = false;}
		if($chkDirection['OHG']){$lstCoordonnee[] = implode(',', array($carte, ($position['0'] - $i), ($position['1'] - $i)));}
		//la direction oblique HD
		if(($position['0']-$i)<0 or ($position['1']+$i)>$nbColonneCarte){$chkDirection['OHD'] = false;}
		if($chkDirection['OHD']){$lstCoordonnee[] = implode(',', array($carte, ($position['0'] - $i), ($position['1'] + $i)));}
		//la direction oblique BG
		if(($position['0']+$i)>$nbLigneCarte or ($position['1']-$i)<0){$chkDirection['OBG'] = false;}
		if($chkDirection['OBG']){$lstCoordonnee[] = implode(',', array($carte, ($position['0'] + $i), ($position['1'] - $i)));}
		//la direction oblique BD
		if(($position['0']+$i)>$nbLigneCarte or ($position['1']+$i)>$nbColonneCarte){$chkDirection['OBD'] = false;}
		if($chkDirection['OBD']){$lstCoordonnee[] = implode(',', array($carte, ($position['0'] + $i), ($position['1'] + $i)));}
	}
	return $lstCoordonnee;
}
?>