<?php
class marcher extends batiment{
	//--- fonction qui est lancer lors de la création de l'objet. ---
	public function __construct(array $carte, array $batiment){
		$this->Hydrate($carte, $batiment);
	}
	public function Hydrate(array $carte, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($carte, $batiment);
	}
	
	//Les Affichages
	//--------------
	private function AfficheObjetsDuMarchant(personnage &$oJoueur){
		$txt = null;
		
		$sql = "SELECT contenu_vendeur FROM table_marcher WHERE type_vendeur='marchant'";
		$requete = mysql_query($sql) or die (mysql_error());
		
		if(mysql_num_rows($requete) == 0){
			$txt .= '
			<tr>
				<td colspan="4">Aucun objet en vente au marcher nationnal.</td>
			</tr>';
		}else{
			//on crée l'objet Marecher
			$marecher = new marchant(mysql_fetch_array($requete, MYSQL_ASSOC));
			$txt .='
			<tr>
				<td colspan="4">
					<table>';
			$i = 0;
				
			$numC = 0;
			$numL = 0;
				
			foreach($marecher->GetLstContenu() as $Marchandise){
				$arMarchandise = explode('=', $Marchandise);
				$txtAttaque = NULL;
					
				$sql = "SELECT * FROM table_objets WHERE objet_code='".strval($arMarchandise[0])."';";
					
				$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
				$result = mysql_fetch_array($requete, MYSQL_ASSOC);
					
				if(substr($arMarchandise[0],0,3) != 'Res' AND !in_array($result['objet_type'], array('objet', 'sac'))){
					$txtAttaque = '<tr><td>'.AfficheIcone('attaque',12).' : '.$result['objet_attaque'].'</td><td>'.AfficheIcone('defense',12).' : '.$result['objet_defense'].'</td><td>'.AfficheIcone('distance',12).' : '.$result['objet_distance'].'</td></tr>';
				}
					
				$InfoBulle = '<table class="InfoBulle">'
								.'<tr>'
									.'<th'.(!is_null($txtAttaque)?' colspan="3"':'').'>'
										.$arMarchandise[1].'x '.$result['objet_nom']
									.'</th>'
								.'</tr>'
								.(!is_null($txtAttaque)?$txtAttaque:'')
								.'<tr>'
									.'<td'.(!is_null($txtAttaque)?' colspan="3"':'').'>'
											.'Prix : '.AfficheListePrix(array('Or'=>intval($result['objet_prix'] * 1.5)), array('Or'=>$oJoueur->GetArgent()))
									.'</td>'
								.'</tr>'
							.'</table>';
					
				$_SESSION['main']['vente'][$i]['code'] = $arMarchandise[0];
				$_SESSION['main']['vente'][$i]['prix'] = intval($result['objet_prix'] * 1.5);
				$_SESSION['main']['vente'][$i]['type'] = $result['objet_type'];
		
				if($numC == 0){
					$txt .= '<tr style="vertical-align:top;">';
				}
		
				$txt .= '<td>'.$this->AfficheUnElement(	$result['objet_code'],
														$InfoBulle,
														$i,
														$result['objet_nom'],
														$oJoueur->GetArgent(),
														($oJoueur->QuelCapaciteMonBolga() > count($oJoueur->GetLstInventaire())),
														$result['objet_prix'],
														$arMarchandise[1])
						.'</td>';
				
				$numC++;
		
				if($numC == 7){
					$txt .= '</tr>';
					$numC = 0;
				}
		
				$i++;
			}
			$txt .='</table>
				</td>
			</tr>';
		}
		
		return $txt;
	} 
	private function AfficheUnElement(	$CodeObjet,
										$InfoBulle,
										$id,
										$NomObjet,
										$Argent,
										$chkBolga,
										$PrixObjet,
										$nbObjet){
		
		$txtBt = null;
		
		Foreach(array(1, 10, 100) as $StepAchat){
			if(	(intval($PrixObjet * 1.5) * $StepAchat) <= $Argent
				AND $chkBolga
				AND $StepAchat <= $nbObjet){
				$chkAchat = true;
			}else{
				$chkAchat = false;
			}
			
			if($StepAchat == 1){
				$chkFirst = $chkAchat;
			}
			
			$InfoBulleAchat = '<table class="marchandise">'
									.'<tr>'
										.'<td>Acheter '.$StepAchat.'x de '.$NomObjet.' pour '.intval($StepAchat * 1.5 * $PrixObjet).AfficheIcone('or', 15).'</td>'
									.'</tr>'
								.'</table>';
			
			$txtBt .= '
			<button type="button" '
				.'class="marcher" '
				.($chkAchat?'':'disabled="disabled" ')
				.'onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulleAchat).'\');" '
				.'onmouseout="cache();" '
				.'onclick="window.location=\'index.php?page=village&action=VenteMarcher&amp;id='.$id.'&amp;qte='.$StepAchat.'\'">'
					.'Acheter '.$StepAchat.'x'
			.'</button>' ;
		}
		
		return '<p>'.$nbObjet.'x '
					.'<img src="./img/objets/'.$CodeObjet.'.png" '
						.'height="50px" '
						.'style="vertical-align:middle;'.(!$chkFirst?' filter:Alpha(opacity=50); opacity:0.5;':'').'"'
						.'alt="'.$NomObjet.'" '
						.'onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" '
						.'onmouseout="cache();" />'
					.'<br />'
					.$txtBt
				.'</p>';
	}
	private function AffichePropositionsTrocs(personnage &$oJoueur){
		$txt = null;
		
		$sql = "SELECT * FROM table_marcher WHERE status_vente IS NULL AND vendeur IS NOT NULL AND vendeur IN ('".implode("', '", ListeMembreClan($oJoueur->GetClan()))."')";
		$requete = mysql_query($sql) or die (mysql_error());
		
		if(mysql_num_rows($requete) == 0){
			$txt .='
			<tr>
				<td colspan="4"><p>Aucune transaction en cours</p></td>
			</tr>';
		}else{
			//on trouve la maison du joueur
			$maison = FoundBatiment(1);
			
			$txt .= '
			<tr>
				<td colspan="4">
					<table style="width:100%" class="listetransactions">
						<tr><td style="background:lightgreen; width:45%;">Vente</td><td style="background:lightcoral; width:45%;">Achat</td><td>Action</td></tr>';
			
			$numTransaction = 0;
			
			while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
				
				$_SESSION['main']['transaction'][$numTransaction]['annuler'] = $row['ID_troc'];
				$_SESSION['main']['transaction'][$numTransaction]['accepter'] = $row['ID_troc'];
					
				if(	$row['achat_or']				<= $oJoueur->GetArgent()
					AND $row['achat_nourriture']	<= $maison->GetRessourceNourriture()
					AND $row['achat_bois']			<= $maison->GetRessourceBois()
					AND $row['achat_pierre']		<= $maison->GetRessourcePierre()
				){
					$checkValid = true;
				}else{
					$checkValid = false;
				}
					
				$txt .= '
						<tr>
							<td>'.AfficheListePrix(	array(	'Or'=>$row['vente_or'],
															'Nourriture'=>$row['vente_nourriture'],
															'Bois'=>$row['vente_bois'],
															'Pierre'=>$row['vente_pierre'])
													)
								.' de '.$row['vendeur'].'</td>
							<td>'.AfficheListePrix(	array(	'Or'=>$row['achat_or'],
															'Nourriture'=>$row['achat_nourriture'],
															'Bois'=>$row['achat_bois'],
															'Pierre'=>$row['achat_pierre']),
													array(	'Or'=>$oJoueur->GetArgent(),
															'Nourriture'=>$maison->GetRessourceNourriture(),
															'Bois'=>$maison->GetRessourceBois(),
															'Pierre'=>$maison->GetRessourcePierre())
													).'</td>'
							.'<td>'
								.($oJoueur->GetLogin() == $row['vendeur']?
									'<button type="button" 
										onclick="window.location=\'index.php?page=village&action=annulertransaction&amp;id='.$numTransaction.'\'" 
										style="width:40px;">'
										.AfficheIcone('marcher_cancel')
									.'</button>'
									:'')
								.($oJoueur->GetLogin() != $row['vendeur']?
									'<button 
										type="button" 
										onclick="window.location=\'index.php?page=village&action=acceptertransaction&amp;id='.$numTransaction.'\'" 
										style="width:40px;" '
										.(!$checkValid?'disabled="disabled"':'').'>'
										.AfficheIcone((!$checkValid?'marcher_attention':'marcher_accept'))
									.'</button>'
									:'')
							.'</td>
						</tr>';
				
				$numTransaction ++;
			}
			$txt .= '
					</table>
				</td>
			</tr>';
		}
		
		return $txt;
	}
	private function AfficheNouveauTroc(){
		$txt = null;
		
		if(isset($_SESSION['transaction']['acceptation'])){
			$txt .= '
			<tr  style="background:'.(is_null($_SESSION['transaction']['acceptation'])?'lightgreen':'red').';">
				<td colspan="4">'
					.(is_null($_SESSION['transaction']['acceptation'])?'Transaction acceptée':$_SESSION['transaction']['acceptation'])
				.'</td>
			</tr>';
			unset($_SESSION['transaction']['acceptation']);
		}
		
		$txt .= '
			<tr>
				<td colspan="4" style="text-align:center;">
					<form method="post" action="index.php" class="nouveau_troc">
						<input type="hidden" name="page" value="village" />
						<fieldset class="donner">
							<legend style="background:lightgreen;">Donner</legend>'
								.AfficheIcone('nourriture').' : <input type="text" name="VNourriture" maxlength="4" value="0" />'
								.AfficheIcone('or').' : <input type="text" name="VOr" maxlength="4" value="0" />'
								.AfficheIcone('bois').' : <input type="text" name="VBois" maxlength="4" value="0" />'
								.AfficheIcone('pierre').' : <input type="text" name="VPierre" maxlength="4" value="0" />
						</fieldset>
						<br />
						<fieldset class="recevoir">
							<legend style="background:lightcoral;">Recevoir</legend>'
								.AfficheIcone('nourriture').' : <input type="text" name="ANourriture" maxlength="4" value="0" />'
								.AfficheIcone('or').' : <input type="text" name="AOr" maxlength="4" value="0" />'
								.AfficheIcone('bois').' : <input type="text" name="ABois" maxlength="4" value="0" />'
								.AfficheIcone('pierre').' : <input type="text" name="APierre" maxlength="4" value="0" />
						</fieldset>
						<br />
						<input type="submit" name="transaction" value="Proposer transaction" style="display:inline;" />
					</form>
				</td>
			</tr>';
		
		return $txt;
	}
	public function AfficheTransactions(personnage &$oJoueur){
		$txt = '
			<tr>
				<th colspan="4">Achat possible</th>
			</tr>'
			.$this->AfficheObjetsDuMarchant($oJoueur)
			.'<tr>
				<th colspan="4">Propositions de trocs</th>
			</tr>'
			.$this->AffichePropositionsTrocs($oJoueur)
			.'<tr>
				<th colspan="4">Nouveau troc?</th>
			</tr>'
			.$this->AfficheNouveauTroc();
		
		return $txt;
	}
}
?>