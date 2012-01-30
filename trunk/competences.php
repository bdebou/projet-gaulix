<?php
// On prolonge la session
function chargerClasse($classname){require './fct/'.$classname.'.class.php';}
spl_autoload_register('chargerClasse');

session_start();

// On teste si la variable de session existe et contient une valeur
if(!isset($_SESSION['joueur'])) {
    // Si inexistante ou nulle, on redirige vers le formulaire de login
    //header('Location: ./regle.php');
	echo '<script language="javascript">window.location="./";</script>';
    exit();
}else{
	include('./fct/config.php');
	include('./fct/fct_main.php');
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr">

<head>
	<?php echo AfficheHead();?>
	<title>Gaulix - Compétences</title>
	<script type="text/javascript">
        //<!--
		var arOnglets = new Array;
		function change_onglet(NewName, NewNiveau){
			for (var i=0; i<arOnglets.length; i++){
				if(arOnglets[i][0] == NewName){
					for(var j=1; j<arOnglets[i].length; j++){
						document.getElementById('onglet_'+NewName+'_'+arOnglets[i][j]).className = 'onglet_0 onglet';
						document.getElementById('contenu_onglet_'+NewName+'_'+arOnglets[i][j]).style.display = 'none';
					}
				}
			}
			document.getElementById('onglet_'+NewName+'_'+NewNiveau).className = 'onglet_1 onglet';
			document.getElementById('contenu_onglet_'+NewName+'_'+NewNiveau).style.display = 'block';
		}
        //-->
	</script>
</head>
<body>
<div class="container">
<div id="curseur" class="infobulle"></div>
<div class="loginstatus"><?php RefreshData(4); echo affiche_LoginStatus();?></div>
<div class="carte"><?php echo affiche_carte();?></div>
<div class="pub">
	<script type="text/javascript"><!--
		google_ad_client = "ca-pub-2161674761092050";
		/* Pub Fight 2 */
		google_ad_slot = "3236602268";
		google_ad_width = 300;
		google_ad_height = 250;
		//-->
	</script>
	<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
</div>
<div class="mouvements"><?php echo affiche_mouvements();?></div>
<div class="actions"><?php affiche_actions();?></div>
<div class="menu"><?php echo affiche_menu();?></div>
<div class="module_social"><?php echo AfficheModuleSocial();?></div>

<div class="main">
<h2>Compétences</h2>
<table class="perfectionnement">
	<tr>
		<td style="width:50%;">
			<?php echo AfficheModulePerfectionnement('attaque');?>
		</td>
		<td>
			<?php echo AfficheModulePerfectionnement('defense');?>
		</td>
	</tr>
	<?php echo AfficheAutreCompetences();	?>
</div>

<div class="version">
	<?php echo AfficheFooter(true);?>
</div>
</div>
</body>
</html>

<?php
function AfficheAutreCompetences(){
	global $objManager;
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

	$NomPrecedent = null;
	$txtJScript = '
				<script type="text/javascript">
					//<!--';
	$nbComp = 0;
	
	//FinishAllCompetenceEnCours();
	
	$sqlLstCmp = "SELECT * FROM table_competence_lst WHERE cmp_lst_type='competence' ORDER BY cmp_lst_nom, cmp_lst_niveau ASC;";
	$rqtLstCmp = mysql_query($sqlLstCmp) or die (mysql_error().'<br />'.$sqlLstCmp);
		
	while($cmp = mysql_fetch_array($rqtLstCmp, MYSQL_ASSOC)){
		$chkFinis = false;
		//on récupère les info de la première compétence
		if($cmp['cmp_lst_nom'] != $NomPrecedent){
			$NomPrecedent = $cmp['cmp_lst_nom'];
			$nbNiveau = 1;
			$txtJScript .= '
					arOnglets['.$nbComp.'] = new Array;
					arOnglets['.$nbComp.'][0] = \''.$cmp['cmp_lst_nom'].'\';
					arOnglets['.$nbComp.']['.$nbNiveau.'] = '.$cmp['cmp_lst_niveau'].';';
			$nbComp++;
		}else{
			$nbNiveau++;
			$txtJScript .= '
					arOnglets['.($nbComp-1).']['.$nbNiveau.'] = '.$cmp['cmp_lst_niveau'].';';
		}
		$sqlCmp = "SELECT * FROM table_competence WHERE cmp_login='".$oJoueur->GetLogin()."' AND cmp_nom='".$cmp['cmp_lst_nom']."' AND cmp_niveau=".$cmp['cmp_lst_niveau'].";";
		$rqtCmp = mysql_query($sqlCmp) or die (mysql_error().'<br />'.$sqlCmp);
		if(mysql_num_rows($rqtCmp) > 0){
			$CmpEnCours = mysql_fetch_array($rqtCmp, MYSQL_ASSOC);
			
			$arOnglets[$cmp['cmp_lst_nom']][$cmp['cmp_lst_niveau']]['contenu'] = '
            <div class="contenu_onglet" id="contenu_onglet_'.$cmp['cmp_lst_nom'].'_'.$cmp['cmp_lst_niveau'].'">'
                .AfficheAvancementCompetence($CmpEnCours, $cmp, $chkFinis)
            .'</div>';
		}else{
			$arOnglets[$cmp['cmp_lst_nom']][$cmp['cmp_lst_niveau']]['contenu'] = '
            <div class="contenu_onglet" id="contenu_onglet_'.$cmp['cmp_lst_nom'].'_'.$cmp['cmp_lst_niveau'].'">'
                .AfficheInfoCompetence($cmp, $oJoueur)
            .'</div>';
		}
		$arOnglets[$cmp['cmp_lst_nom']][$cmp['cmp_lst_niveau']]['span'] = '
			<span 
				class="onglet_0 onglet" 
				id="onglet_'.$cmp['cmp_lst_nom'].'_'.$cmp['cmp_lst_niveau'].'" 
				onclick="javascript:change_onglet(\''.$cmp['cmp_lst_nom'].'\', \''.$cmp['cmp_lst_niveau'].'\');">'
				.($chkFinis?AfficheIcone('check', 18).' ':'')
				.($cmp['cmp_lst_nom'] == 'tailleurp'
					?'Tailleur de pierre'
					:ucfirst($cmp['cmp_lst_nom'])
				)
				.' (Niveau '.$cmp['cmp_lst_niveau'].')
			</span>';
					
	}
    $txtJScript .= '
					//-->
				</script>';
	$txt = null;
	$txtFinish = null;
	$numCol = 0;
	$checkA = true;
	
	foreach($arOnglets as $key => $onglet){
	
		$chkPremier = true;
		
		//on ouvre la ligne
		if($numCol == 0 AND $checkA){
			$txt .= '<tr style="vertical-align:top;">';
			$checkA = false;
		}
		
		$txt .= '<td>
	<div class="systeme_onglets">
		<div class="onglets">';
		
		foreach($onglet as $niveau => $span){
			//on récupère les info de la première compétence
			if($chkPremier){
				if(is_null($oJoueur->GetNiveauCompetence(ucfirst($key)))){
					$chkPremier = false;
					$NomPremier = $key;
					$NiveauPremier = $niveau;
				}elseif($oJoueur->GetNiveauCompetence(ucfirst($key)) == $niveau){
					$chkPremier = false;
					$NomPremier = $key;
					$NiveauPremier = $niveau+1;
					if($oJoueur->GetNiveauCompetence(ucfirst($key)) == 0){$NiveauPremier = 1;}
				}
			}
			$txt .= $span['span'];
		}
		
		$txt .=	'</div>
        <div class="contenu_onglets">';
		
		foreach($onglet as $contenu){$txt .= $contenu['contenu'];}
		
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
		if($numCol == 2){
			$txt .= '</tr>';
			$numCol = 0;
			$checkA = true;
		}
	}
	
	$objManager->update($oJoueur);
	unset($oJoueur);
	return $txt.'</table>'.$txtJScript;
}
function AfficheAvancementCompetence($competence, $info, &$chkFinis){
	if($competence['cmp_temp'] >= (strtotime('now')-strtotime($competence['cmp_date']))){
		$txtStatus = '
		<tr>
			<td style="background:#b6ff6c; text-align:center;"><p style="display:inline;">En cours.<br />Reste : </p>'
				.'<div style="display:inline;" id="TimeToWait'.$competence['cmp_nom'].'"></div>'
				.AfficheCompteurTemp($competence['cmp_nom'], './competences.php', ($competence['cmp_temp'] - (strtotime('now')-strtotime($competence['cmp_date']))))
			.'</td>
		</tr>';
		$chkFinis = false;
	}else{
		$txtStatus = '<tr><td style="background:#6495ED; text-align:center;">Finis</td></tr>';
		$chkFinis = true;
	}
	
	return '
	<table class="competence">
		<tr style="background:lightgrey;"><th>'
		.($competence['cmp_nom'] == 'tailleurp'
			?'Tailleur de pierre'
			:ucfirst($competence['cmp_nom'])
		)
		.'</th></tr>
		<tr><td>'.$info['cmp_lst_description'].'</td></tr>
		<tr><td>Niveau : '.$competence['cmp_niveau'].'</td></tr>'
		.$txtStatus
	.'</table>';
}
function AfficheInfoCompetence($competence, &$oJoueur){
	$maison = FoundBatiment(1);
	$check = false;
	if(	!is_null($maison)
		AND $competence['cmp_lst_prix_or']			<= $oJoueur->GetArgent()
		AND $competence['cmp_lst_prix_nourriture']	<= $maison->GetRessourceNourriture()
		AND $competence['cmp_lst_prix_bois']		<= $maison->GetRessourceBois()
		AND $competence['cmp_lst_prix_pierre']		<= $maison->GetRessourcePierre()
		AND $competence['cmp_lst_prix_hydromel']	<= $oJoueur->GetCombienElementDansBolga('Hydromel')
		AND (
			$oJoueur->GetNiveauCompetence(ucfirst($competence['cmp_lst_nom'])) == ($competence['cmp_lst_niveau'] - 1)
			OR (
				is_null($oJoueur->GetNiveauCompetence(ucfirst($competence['cmp_lst_nom'])))
					AND
				in_array($competence['cmp_lst_niveau'], array(0, 1))
				)
			)
		){
			$check = true;
			$_SESSION['main'][$competence['cmp_lst_nom']]['prix_or']			= $competence['cmp_lst_prix_or'];
			$_SESSION['main'][$competence['cmp_lst_nom']]['prix_nourriture']	= $competence['cmp_lst_prix_nourriture'];
			$_SESSION['main'][$competence['cmp_lst_nom']]['prix_bois']			= $competence['cmp_lst_prix_bois'];
			$_SESSION['main'][$competence['cmp_lst_nom']]['prix_pierre']		= $competence['cmp_lst_prix_pierre'];
			$_SESSION['main'][$competence['cmp_lst_nom']]['prix_hydromel']		= $competence['cmp_lst_prix_hydromel'];
			$_SESSION['main'][$competence['cmp_lst_nom']]['niveau']				= $competence['cmp_lst_niveau'];
			$_SESSION['main'][$competence['cmp_lst_nom']]['temp']				= $competence['cmp_lst_temp'];
		}
	
	return '
	<table class="competence">
		<tr style="background:lightgrey;"><th colspan="2">'
		.($competence['cmp_lst_nom'] == 'tailleurp'
			?'Tailleur de pierre'
			:ucfirst($competence['cmp_lst_nom'])
		)
		.'</th></tr>
		<tr><td colspan="2">'.$competence['cmp_lst_description'].'</td></tr>
		<tr>
			<td>Niveau : '.$competence['cmp_lst_niveau'].'</td>
			<td>Durée : '.AfficheTempPhrase(DecoupeTemp($competence['cmp_lst_temp'])).'</td>
		</tr>
		<tr><td colspan="2" style="text-align:center;">Coût : '
		.AfficheListePrix(
			array('Or'=>$competence['cmp_lst_prix_or'], 'Bois'=>$competence['cmp_lst_prix_bois'], 'Pierre'=>$competence['cmp_lst_prix_pierre'], 'Nourriture'=>$competence['cmp_lst_prix_nourriture'], 'Hydromel'=>$competence['cmp_lst_prix_hydromel']),
			array('Or'=>$oJoueur->GetArgent(), 'Bois'=>(!is_null($maison)?$maison->GetRessourceBois():0), 'Pierre'=>(!is_null($maison)?$maison->GetRessourcePierre():0), 'Nourriture'=>(!is_null($maison)?$maison->GetRessourceNourriture():0), 'Hydromel'=>$oJoueur->GetCombienElementDansBolga('Hydromel')))
		.'</td></tr>
		<tr><td colspan="2" style="text-align:center;">
			<button 
				type="button" 
				onclick="window.location=\'./fct/main.php?action=competence&amp;cmp='.$competence['cmp_lst_nom'].'\'" '
				.($check?'':'disabled="disabled"').' 
				style="width:100px;background:'.(!$check?'Tomato':'LightGreen').';">
					Go
			</button>
		</td></tr>
	</table>';
	
}
function AfficheModulePerfectionnement($type){
	global $objManager;
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	if(	($type == 'attaque' AND is_null($oJoueur->GetDatePerfAttaque()))
		OR
		($type == 'defense' AND is_null($oJoueur->GetDatePerfDefense()))){
		return GoPerfectionnement($type, $oJoueur);		
	}elseif(	($type == 'attaque' AND (strtotime('now')-$oJoueur->GetDatePerfAttaque()) < $oJoueur->GetTmpPerfAttaque())
			OR	($type == 'defense' AND (strtotime('now')-$oJoueur->GetDatePerfDefense()) < $oJoueur->GetTmpPerfDefense())
			){
		$txt = '
	<table class="competence">
		<tr style="background:lightgrey;">
			<th>Entrainement pour "'.ucfirst($type).'"</th>
		</tr>
		<tr>
			<td>Augmente vos points '.($type == 'attaque'?'d\'':'de ').$type.'.</td>
		</tr>
		<tr>
			<td style="background:#b6ff6c; text-align:center;"><p style="display:inline;">En cours.<br />Reste : </p>
				<div style="display:inline;" id="TimeToWait'.ucfirst(substr($type, 0, 3)).'"></div>'
				.AfficheCompteurTemp(
							ucfirst(substr($type, 0, 3)),
							'./competences.php',
							($type == 'attaque'?
								($oJoueur->GetTmpPerfAttaque()-(strtotime('now')-$oJoueur->GetDatePerfAttaque()))
								:($oJoueur->GetTmpPerfDefense()-(strtotime('now')-$oJoueur->GetDatePerfDefense()))
							)
							)
			.'</td>
		</tr>
	</table>';
		
		$objManager->update($oJoueur);
		unset($oJoueur);
		return $txt;
	}else{
		$_SESSION['main']['Perf'.ucfirst(substr($type, 0, 3))] = 'Finish';
		$_SESSION['main']['Tmp'.ucfirst(substr($type, 0, 3))] = 0;
		$objManager->update($oJoueur);
		unset($oJoueur);
		//redir('./fct/main.php?action=Perf'.ucfirst(substr($type, 0, 3)));
		return '<script language="javascript">window.location=\'./fct/main.php?action=Perf'.ucfirst(substr($type, 0, 3)).'\';</script>';
	}
}
function GoPerfectionnement($type, &$oJoueur){
	$nbMaxPerf = 25; $PrixDepart = 50; $check = false; $StepPerf=5;
	switch($type){
		case 'attaque':	$temp = $oJoueur->GetAttPerso();	break;
		case 'defense':	$temp = $oJoueur->GetDefPerso();	break;
	}
	
	$nbPerfDone = $temp['0']-10;
	
	$prix = intval($PrixDepart * exp($nbPerfDone / 8));
	$temp = intval(3600 * exp($nbPerfDone / 8));
	//$arTemp = DecoupeTemp($temp);
	
	if(	$nbPerfDone < (($oJoueur->GetNiveau() + 1) * $StepPerf)
		AND $oJoueur->GetArgent() >= $prix){
		$check=true;
	}elseif($nbPerfDone > (($oJoueur->GetNiveau() + 1) * $StepPerf)){
		return '
	<table class="competence">
		<tr style="background:lightgrey;"><th colspan="2">Amélioration '.ucfirst($type).'</th></tr>
		<tr><td>Vous avez atteint le maximum.</td></tr>
	</table>';
	}
	
	$_SESSION['main']['Perf'.ucfirst(substr($type, 0, 3))] = 'Go';
	$_SESSION['main']['Tmp'.ucfirst(substr($type, 0, 3))] = $temp;
	$_SESSION['main']['Prix'.ucfirst(substr($type, 0, 3))] = $prix;
	
	return '
	<table class="competence">
		<tr style="background:lightgrey;"><th colspan="2">Entrainement pour "'.ucfirst($type).'"</th></tr>
		<tr><td>
			<p>Cliquez ci-dessous pour lancer une amélioration de votre '.$type.'. Vous ne pouvez augmenter que de 1 point à la fois.</p>
			<p>Vous avez encore la possibilité d\'augmenter de '.((($oJoueur->GetNiveau() + 1) * $StepPerf) - $nbPerfDone).' points votre '.$type.'.</p>
		</td></tr>
		<tr><td style="text-align:center;">Durée : '.AfficheTempPhrase(DecoupeTemp($temp)).'</td></tr>
		<tr><td style="text-align:center;">Coût : '.AfficheListePrix(array('Or'=>$prix), array('Or'=>$oJoueur->GetArgent())).'</td></tr>
		<tr><td style="text-align:center;">
			<button 
				type="button" 
				onclick="window.location=\'./fct/main.php?action=Perf'.ucfirst(substr($type, 0, 3)).'\'" '
				.($check?'':'disabled="disabled"').' 
				style="width:100px;background:'.(!$check?'Tomato':'LightGreen').';">
					Go
			</button>
		</td></tr>
	</table>';
}
?>