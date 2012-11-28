<?php
function AfficheAutreCompetences(personnage &$oJoueur, maison &$oMaison = NULL){

	$NomPrecedent = null;
	$txtJScript = '
				<script type="text/javascript">
					//<!--';
	$nbComp = 0;

	$CarriereClass = GetInfoCarriere($oJoueur->GetCodeCarriere(), 'carriere_class');
	
	//$sqlLstCmp = "SELECT * FROM table_competence_lst WHERE cmp_lst_acces IN ('".GetInfoCarriere($oJoueur->GetCodeCarriere(), 'carriere_class')."', 'Tous') ORDER BY cmp_lst_code ASC;";
	$sqlLstCmp = "SELECT * FROM table_competence_lst 
				WHERE (cmp_lst_acces IN ('".$CarriereClass."', 'Tous') 
					OR cmp_lst_acces LIKE '%".$CarriereClass."%') 
				ORDER BY cmp_lst_code ASC;";
	$rqtLstCmp = mysql_query($sqlLstCmp) or die (mysql_error().'<br />'.$sqlLstCmp);

	while($cmp = mysql_fetch_array($rqtLstCmp, MYSQL_ASSOC)){
		
		$chkFinis = false;
		$NomOnglet = str_replace(array(' ', "'", '&#39;'), '_', $cmp['cmp_lst_type']);
		
		//on récupère les info de la première compétence
		if($NomOnglet != $NomPrecedent){
			$NomPrecedent = $NomOnglet;
			$nbNiveau = 1;
			$txtJScript .= '
					arOnglets['.$nbComp.'] = new Array;
					arOnglets['.$nbComp.'][0] = \''.$NomOnglet.'\';
					arOnglets['.$nbComp.']['.$nbNiveau.'] = '.$cmp['cmp_lst_niveau'].';';
			$nbComp++;
		}else{
			$nbNiveau++;
			$txtJScript .= '
					arOnglets['.($nbComp-1).']['.$nbNiveau.'] = '.$cmp['cmp_lst_niveau'].';';
		}
		
		$sqlCmp = "SELECT * FROM table_competence WHERE cmp_login='".$oJoueur->GetLogin()."' AND cmp_code='".$cmp['cmp_lst_code']."';";
		$rqtCmp = mysql_query($sqlCmp) or die (mysql_error().'<br />'.$sqlCmp);
		
		if(mysql_num_rows($rqtCmp) > 0)
		{
			$CmpEnCours = mysql_fetch_array($rqtCmp, MYSQL_ASSOC);
				
			$arOnglets[$NomOnglet][$cmp['cmp_lst_niveau']]['contenu'] = '
            <div class="contenu_onglet" id="contenu_onglet_'.$NomOnglet.'_'.$cmp['cmp_lst_niveau'].'">'
			.AfficheAvancementCompetence($CmpEnCours, $cmp, $chkFinis)
			.'</div>';
		}else{
			$arOnglets[$NomOnglet][$cmp['cmp_lst_niveau']]['contenu'] = '
            <div class="contenu_onglet" id="contenu_onglet_'.$NomOnglet.'_'.$cmp['cmp_lst_niveau'].'">'
			.AfficheInfoCompetence($cmp, $oJoueur, $oMaison)
			.'</div>';
		}
		
		$arOnglets[$NomOnglet][$cmp['cmp_lst_niveau']]['code'] = $cmp['cmp_lst_code'];
		$arOnglets[$NomOnglet][$cmp['cmp_lst_niveau']]['span'] = '
			<span 
				class="onglet_0 onglet" 
				id="onglet_'.$NomOnglet.'_'.$cmp['cmp_lst_niveau'].'" 
				onclick="javascript:change_onglet(\''.$NomOnglet.'\', \''.$cmp['cmp_lst_niveau'].'\');">'
		.($chkFinis?AfficheIcone('check', 18).' ':'')
		.ucfirst($cmp['cmp_lst_nom']).'</span>';
			
	}
	$txtJScript .= '
					//-->
				</script>';
	$txt = null;
	$txtFinish = null;
	$numCol = 0;
	$checkA = true;
	
	$nbColonne = 2;

	foreach($arOnglets as $onglet => $data){

		$chkPremier = true;

		//on ouvre la ligne
		if($numCol == 0 AND $checkA){
			$txt .= '<tr style="vertical-align:top;">';
			$checkA = false;
		}

		$txt .= '<td>
	<div class="systeme_onglets">
		<div class="onglets">';
	
		foreach($data as $Niveau => $span){
			//on récupère les info de la première compétence
			if($chkPremier){
				if(!$oJoueur->CheckCompetence($span['code'])){
					$chkPremier = false;
					$NomPremier = $onglet;
					$NiveauPremier = $Niveau;
				}
			}
			$txt .= $span['span'];
		}

		$txt .=	'</div>
        <div class="contenu_onglets">';

		foreach($data as $contenu){
			$txt .= $contenu['contenu'];
		}

		$txt .= '</div>
    </div>
	<script type="text/javascript">
		//<!--
			change_onglet(\''.$NomPremier.'\', '.$NiveauPremier.');
		//-->
	</script>
	</td>';

		$numCol++;
		//on ferme la ligne
		if($numCol == $nbColonne){
			$txt .= '</tr>';
			$numCol = 0;
			$checkA = true;
		}
	}

	return array($txt, $txtJScript);
}
function AfficheAvancementCompetence($competence, $info, &$chkFinis){
	if($competence['cmp_temp'] >= (strtotime('now')-strtotime($competence['cmp_date']))){
		$txtStatus = '
		<tr>
			<td style="background:#b6ff6c; text-align:center;"><p style="display:inline;">En cours.<br />Reste : </p>'
				.'<div style="display:inline;" id="TimeToWait'.$competence['cmp_code'].'"></div>'
				.AfficheCompteurTemp($competence['cmp_code'], 'index.php?page=competences', ($competence['cmp_temp'] - (strtotime('now')-strtotime($competence['cmp_date']))))
			.'</td>
		</tr>';
		$chkFinis = false;
	}else{
		$txtStatus = '<tr><td style="background:#6495ED; text-align:center;">Finis</td></tr>';
		$chkFinis = true;
	}
	
	return '
	<table class="competence">
		<tr style="background:lightgrey;"><th>'.ucfirst($info['cmp_lst_nom']).'</th></tr>
		<tr><td>'.$info['cmp_lst_description'].'</td></tr>
		<tr><td>Niveau : '.$info['cmp_lst_niveau'].'</td></tr>'
		.$txtStatus
	.'</table>';
}
function AfficheInfoCompetence($competence, personnage &$oJoueur, maison &$oMaison = NULL){
	
	$check = false;
	
	if(CheckCout(explode(',', $competence['cmp_lst_prix']), $oJoueur, $oMaison))
	{
		$check = true;
		$_SESSION['competences'][$competence['cmp_lst_code']]['prix']				= explode(',', $competence['cmp_lst_prix']);
		//$_SESSION['competences'][$competence['cmp_lst_code']]['code']				= $competence['cmp_lst_code'];
		$_SESSION['competences'][$competence['cmp_lst_code']]['temp']				= $competence['cmp_lst_temp'];
	}
		
	return '
	<table class="competence">'
		//.'<tr style="background:lightgrey;"><th colspan="2">'.ucfirst($competence['cmp_lst_nom']).'</th></tr>'
		.(!is_null($competence['cmp_lst_description'])?'<tr><td colspan="2">'.$competence['cmp_lst_description'].'</td></tr>':NULL)
		.'<tr>
			<td>Niveau : '.$competence['cmp_lst_niveau'].'</td>
			<td>Durée : '.AfficheTempPhrase(DecoupeTemp($competence['cmp_lst_temp'])).'</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">Coût : '.AfficheListePrix(explode(',', $competence['cmp_lst_prix']), $oJoueur, $oMaison).'</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center;">
				<form method="post" action="index.php?page=competences">
					<input type="hidden" name="action" value="competence" />
					<input type="hidden" name="cmp" value="'.$competence['cmp_lst_code'].'" />
					<input style="width:100px;background:'.(!$check?'Tomato':'LightGreen').';" '.($check?'':'disabled="disabled"').' type="submit" name="submit" value="Go" />
				</form>
			</td>
		</tr>
	</table>';
	
}
function AfficheModulePerfectionnement($type, personnage &$oJoueur, maison &$oMaison = NULL){
	
	if(	($type == objArmement::TYPE_ATTAQUE AND is_null($oJoueur->GetDatePerfect(personnage::TYPE_PERFECT_ATTAQUE)))
		OR
		($type == objArmement::TYPE_DEFENSE AND is_null($oJoueur->GetDatePerfect(personnage::TYPE_PERFECT_DEFENSE)))){
		return GoPerfectionnement($type, $oJoueur, $oMaison);		
	}elseif(	($type == objArmement::TYPE_ATTAQUE AND (strtotime('now')-$oJoueur->GetDatePerfect(personnage::TYPE_PERFECT_ATTAQUE)) < $oJoueur->GetTmpPerfect(personnage::TYPE_PERFECT_ATTAQUE))
			OR	($type == objArmement::TYPE_DEFENSE AND (strtotime('now')-$oJoueur->GetDatePerfect(personnage::TYPE_PERFECT_DEFENSE)) < $oJoueur->GetTmpPerfect(personnage::TYPE_PERFECT_DEFENSE))
			){
		$txt = '
	<table class="competence">
		<tr style="background:lightgrey;">
			<th>Entrainement pour "'.ucfirst($type).'"</th>
		</tr>
		<tr>
			<td>Augmente vos points '.($type == objArmement::TYPE_ATTAQUE?'d\'':'de ').$type.'.</td>
		</tr>
		<tr>
			<td style="background:#b6ff6c; text-align:center;"><p style="display:inline;">En cours.<br />Reste : </p>
				<div style="display:inline;" id="TimeToWait'.ucfirst(substr($type, 0, 3)).'"></div>'
				.AfficheCompteurTemp(
							ucfirst(substr($type, 0, 3)),
							'index.php?page=competences',
							($type == objArmement::TYPE_ATTAQUE?
								($oJoueur->GetTmpPerfect(personnage::TYPE_PERFECT_ATTAQUE)-(strtotime('now')-$oJoueur->GetDatePerfect(personnage::TYPE_PERFECT_ATTAQUE)))
								:($oJoueur->GetTmpPerfect(personnage::TYPE_PERFECT_DEFENSE)-(strtotime('now')-$oJoueur->GetDatePerfect(personnage::TYPE_PERFECT_DEFENSE)))
							)
							)
			.'</td>
		</tr>
	</table>';
		
		return $txt;
	}else{
		$_SESSION['competences']['Perf'.ucfirst(substr($type, 0, 3))] = 'Finish';
		$_SESSION['competences']['Tmp'.ucfirst(substr($type, 0, 3))] = 0;
		//redir('./fct/main.php?action=Perf'.ucfirst(substr($type, 0, 3)));
		return '<script type="text/javascript">window.location="index.php?page=competences&action=Perf'.ucfirst(substr($type, 0, 3)).'";</script>';
		//header('location: index.php?page=competences&action=Perf'.ucfirst(substr($type, 0, 3)));
	}
}
function GoPerfectionnement($type, personnage &$oJoueur, maison &$oMaison = NULL){
	$nbMaxPerf	= 25;
	$PrixDepart	= 50;
	$check		= false;
	$StepPerf	= 5;
		
	switch($type){
		case objArmement::TYPE_ATTAQUE:	$temp = $oJoueur->GetAttPerso();	break;
		case objArmement::TYPE_DEFENSE:	$temp = $oJoueur->GetDefPerso();	break;
	}
	
	//Si c'est un guerrier on augmente sa limite de perfectionnement.
	if(GetInfoCarriere($oJoueur->GetCodeCarriere(), 'carriere_class') === personnage::CARRIERE_CLASS_GUERRIER)
	{
		$StepPerf = 10;
	}
	
	$nbPerfDone = $temp['0']-10;
	
	$prix = intval($PrixDepart * exp($nbPerfDone / 8));
	$temp = intval(3600 * exp($nbPerfDone / 8));
	//$arTemp = DecoupeTemp($temp);
	
	if($nbPerfDone > (($oJoueur->GetNiveau() + 1) * $StepPerf))
	{
		return '
	<table class="competence">
		<tr style="background:lightgrey;"><th colspan="2">Amélioration '.ucfirst($type).'</th></tr>
		<tr><td>Vous avez atteint le maximum.</td></tr>
	</table>';
	}elseif($nbPerfDone < (($oJoueur->GetNiveau() + 1) * $StepPerf)
			AND CheckCout(array(personnage::TYPE_RES_MONNAIE.'='.$prix), $oJoueur, $oMaison))
	{
		$check=true;
	}
	
	$_SESSION['competences']['Perf'.ucfirst(substr($type, 0, 3))] = 'Go';
	$_SESSION['competences']['Tmp'.ucfirst(substr($type, 0, 3))] = $temp;
	$_SESSION['competences']['Prix'.ucfirst(substr($type, 0, 3))] = $prix;
	
	return '
	<table class="competence">
		<tr style="background:lightgrey;"><th colspan="2">Entrainement pour "'.ucfirst($type).'"</th></tr>
		<tr><td>
			<p>Cliquez ci-dessous pour lancer une amélioration de votre '.$type.'. Vous ne pouvez augmenter que de 1 point à la fois.</p>
			<p>Vous avez encore la possibilité d\'augmenter de '.((($oJoueur->GetNiveau() + 1) * $StepPerf) - $nbPerfDone).' points votre '.$type.'.</p>
		</td></tr>
		<tr><td style="text-align:center;">Durée : '.AfficheTempPhrase(DecoupeTemp($temp)).'</td></tr>
		<tr><td style="text-align:center;">Coût : '.AfficheListePrix(array(personnage::TYPE_RES_MONNAIE.'='.$prix), $oJoueur, $oMaison).'</td></tr>
		<tr><td style="text-align:center;">
			<button 
				type="button" 
				onclick="window.location=\'index.php?page=competences&amp;action=Perf'.ucfirst(substr($type, 0, 3)).'\'" '
				.($check?'':'disabled="disabled"').' 
				style="width:100px;background:'.(!$check?'Tomato':'LightGreen').';">
					Go
			</button>
		</td></tr>
	</table>';
}
function AddEnregistrementCompetence($code, $duree){
	$sql = "INSERT INTO `table_competence`
		(`cmp_id`, `cmp_login`, `cmp_code`, `cmp_temp`, `cmp_date`, `cmp_finish`)
		VALUES
		(NULL, '".$_SESSION['joueur']."', '$code', $duree, '".date('Y-m-d H:i:s')."', NULL);";
	mysql_query($sql) or die ( mysql_error() .'<br />'.$sql);
}

//+---------------------------------+
//|				ACTIONS				|
//+---------------------------------+
/* Function ActionPerfDef(&$check, &$oJoueur){
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
} */
function ActionPerfectionnement(&$check, personnage &$oJoueur, $type){
	if(isset($_SESSION['competences']['Perf'.substr($type, 0, 3)])){
		switch($_SESSION['competences']['Perf'.substr($type, 0, 3)]){
			case 'Go':
				$oJoueur->LaunchPerfectionnement($type, $_SESSION['competences']['Tmp'.substr($type, 0, 3)],$_SESSION['competences']['Prix'.substr($type, 0, 3)],1);
				break;
			case 'Finish':
				$oJoueur->LaunchPerfectionnement($type, null,null,2);
				break;
		}
		unset($_SESSION['competences']['Perf'.substr($type, 0, 3)]);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionPerfectionnement - '.$type;
	}
}
function ActionCompetence(&$check, personnage &$oJoueur, $cmp, &$objManager){
	if(isset($_SESSION['competences'][$cmp])){
		$maison = $oJoueur->GetObjSaMaison();

		if(CheckCout($_SESSION['competences'][$cmp]['prix'], $oJoueur, $maison))
		{
			foreach($_SESSION['competences'][$cmp]['prix'] as $Prix)
			{
				UtilisationRessource(explode('=', $Prix), $oJoueur, $maison);
			}
			
			AddEnregistrementCompetence($cmp, $_SESSION['competences'][$cmp]['temp']);
		}
		
		$objManager->UpdateBatiment($maison);
		unset($maison);

		unset($_SESSION['competences'][$cmp]);
	}else{
		$check = false;
		echo 'Erreur GLX0002: Fonction ActionCompetence';
	}
}
?>