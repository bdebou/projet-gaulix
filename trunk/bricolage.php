<?php
// On prolonge la session
function chargerClasse($classname){require './fct/'.$classname.'.class.php';}
spl_autoload_register('chargerClasse');

session_start();

// On teste si la variable de session existe et contient une valeur
if(!isset($_SESSION['joueur'])) {
    // Si inexistante ou nulle, on redirige vers le formulaire de login
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
	<title>Gaulix - Bricolage</title>
	<script type="text/javascript">
        //<!--
		function change_onglet(NewName){
			document.getElementById('onglet_'+OldName).className = 'onglet_0 onglet';
			document.getElementById('onglet_'+NewName).className = 'onglet_1 onglet';
			document.getElementById('contenu_onglet_'+OldName).style.display = 'none';
			document.getElementById('contenu_onglet_'+NewName).style.display = 'block';
			
			OldName = NewName;
		}
        //-->
	</script>
</head>
<body>
<div class="container">
<div id="curseur" class="infobulle"></div>
<div class="loginstatus"><?php RefreshData(11); echo affiche_LoginStatus();?></div>
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
<h2>Bricolages</h2>
<p>Vous devez avoir tous les éléments dans votre Bolga pour pouvoir bricoler quelque chose. Donc allez vite à votre entrepôt récupérer les éléments manquant.</p>
<?php
echo AfficheListeElementBricolage();
?>

</div>
<div class="parchemin3"></div>
<div class="version">
	<?php echo AfficheFooter(true);?>
</div>
</div>
</body>
</html>

<?php
function AfficheListeElementBricolage(){
	$txt = '
	<div class="systeme_onglets">';
	$TypePrecedent = null;
	$arOnglets['Contenu'] = '
		<div class="contenu_onglets">';
	$arOnglets['Span'] = '
		<div class="onglets">';;
	$nbBricolage = 0;
	
	$chkFirst = false;

	$sql = "SELECT * 
			FROM table_objets 
			WHERE objet_quete IS NULL AND objet_type IN ('arme', 'bouclier', 'cuirasse', 'jambiere', 'casque', 'objet', 'sort', 'potion', 'sac')
			ORDER BY objet_type, objet_competence, objet_niveau, objet_attaque, objet_defense, objet_distance ASC;";
	
	$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		if($row['objet_type'] != $TypePrecedent){
			$arOnglets['Span'] .= '
			<span 
				class="onglet_0 onglet" 
				id="onglet_'.$row['objet_type'].'" 
				onclick="javascript:change_onglet(\''.$row['objet_type'].'\');">'.ucfirst($row['objet_type']).'
			</span>
			';
			if($chkFirst){
				$arOnglets['Contenu'] .= '
				</table>
			</div>';
			}else{$FirstOnglet = $row['objet_type'];}
			$arOnglets['Contenu'] .= '
			<div class="contenu_onglet" id="contenu_onglet_'.$row['objet_type'].'">
				<table class="bricolage">';
			$chkFirst = true;
			
			$TypePrecedent = $row['objet_type'];
			//$nbBricolage = 0;
		}
		$arOnglets['Contenu'] .= AfficheInfoObjetBricolage($row, $nbBricolage);
		$nbBricolage++;
	}
	
	$arOnglets['Contenu'] .= '
				</table>
			</div>
		</div>';
	$arOnglets['Span'] .= '
		</div>';
	$txt .= $arOnglets['Span']
			.$arOnglets['Contenu']
	.'</div>
	<script type="text/javascript">
		//<!--
			var OldName = \''.$FirstOnglet.'\';
			change_onglet(OldName);
		//-->
	</script>';
	return $txt;
}
function AfficheInfoObjetBricolage($Objet, &$numObjet){
	global $objManager;
	$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
	
	$txt = null;
	$ChkCompetence = false;
	$ChkRessource = true;
	
	if($oJoueur->GetNiveauCompetence(ucfirst($Objet['objet_competence'])) >= $Objet['objet_niveau']){$ChkCompetence = true;}	
	
	$nbCol = 0;
	if(is_null($Objet['objet_attaque'])){$chkAttaque = false; $nbCol++;}else{$chkAttaque = true;}
	if(is_null($Objet['objet_defense'])){$chkDefense = false; $nbCol++;}else{$chkDefense = true;}
	if(is_null($Objet['objet_distance'])){$chkDistance = false; $nbCol++;}else{$chkDistance = true;}
	
	$txt =  '
					<tr>
						<td rowspan="5" style="width:120px; text-align:center;">'.AfficheInfoObjet($Objet['objet_code'], 120).'</td>
						<th colspan="5">'.$Objet['objet_nom'].' '.($Objet['objet_type'] == 'objet'?AfficheIcone($Objet['objet_code']):'').'</th>
					</tr>'
					.'<tr>'
						.(($Objet['objet_type'] != 'sac' AND $chkAttaque)?'<td>'.AfficheIcone('attaque').' : '.(!is_null($Objet['objet_attaque'])?$Objet['objet_attaque']:'0').'</td>':'')
						.($chkDistance?'<td>'.AfficheIcone('distance').' : '.(!is_null($Objet['objet_distance'])?$Objet['objet_distance']:'0').'</td>':'')
						.($chkDefense?'<td>'.AfficheIcone('defense').' : '.(!is_null($Objet['objet_defense'])?$Objet['objet_defense']:'0').'</td>':'')
						.'<td>'.AfficheIcone('or').' : '.$Objet['objet_prix'].'</td>'
						.'<td style="background:'.($ChkCompetence?'LightGreen':'Tomato').'"'.($nbCol>0?' colspan="'.$nbCol.'"':'').'>'.($Objet['objet_competence']=='tailleurp'?'Tailleur de Pierre':ucfirst($Objet['objet_competence'])).' <i>Niveau '.$Objet['objet_niveau'].'</i></td>'
					.'</tr>'
					.'<tr><td colspan="5">'.AfficheRessourceBesoin($Objet['objet_ressource'], $ChkRessource, $oJoueur).'</td></tr>'
					.'<tr><td colspan="5">'.$Objet['objet_description'].'</td></tr>';
	
	$txtInfoBulle = '<table class="equipement">'
			.'<tr><th><b>1x</b> '.$Objet['objet_nom'].'</th></tr>'
			.'<tr><td>'.AfficheRessourceBesoin($Objet['objet_ressource'], $ChkRessource, $oJoueur, 1).'</td></tr>'
			.'</table>';
	
	$txtAction = null;
	$check = true;
	if(!$ChkCompetence OR !$ChkRessource OR count($oJoueur->GetLstInventaire()) >= $oJoueur->QuelCapaciteMonBolga()){
		$chk = false;
		if(!$ChkCompetence){
			$txtAction .= AfficheIcone('attention').'Vous n\'avez pas le niveau de compétence requis.';
			$chk = true;
		}
		if(!$ChkRessource){
			if($chk){$txtAction .= '<br />';}
			$txtAction .= AfficheIcone('attention').'Vous n\'avez pas assez de ressources.';
			$chk = true;
		}
		if(count($oJoueur->GetLstInventaire()) >= $oJoueur->QuelCapaciteMonBolga()){
			if($chk){$txtAction .= '<br />';}
			$txtAction .= AfficheIcone('attention').'Votre Bolga est plein.';
			$chk = true;
		}
		if($chk){$txtAction .= '<br />';}
		$check = false;
	}else{
		$_SESSION['main']['bricolage'][$numObjet]['prix']	= $Objet['objet_ressource'];
		$_SESSION['main']['bricolage'][$numObjet]['code']	= $Objet['objet_code'];
		$_SESSION['main']['bricolage'][$numObjet]['type']	= $Objet['objet_type'];
		//$_SESSION['main']['bricolage'][$numObjet]['qte']	= $Objet['objet_attaque'];
	}
	
	$txtAction .= '<button 
				type="button" 
				class="bricollage" 
				onclick="window.location=\'./fct/main.php?action=fabriquer&amp;id='.$numObjet.'&amp;qte=1\'" ' 
				.($check?'':'disabled="disabled" ')
				.'onmouseover="montre(\''.CorrectDataInfoBulle($txtInfoBulle).'\');" '
				.'onmouseout="cache();" '
				.'style="background:'.(!$check?'Tomato':'LightGreen').';">
					'.($check?AfficheIcone('accept', 15):AfficheIcone('attention', 15)).' Fabriquer <b>1x</b>'
			.'</button>';
	
	if($Objet['objet_type'] == 'objet'){
		$arQte = array(1, 10, 50, 100);
		For($i = 2; $i <= $oJoueur->GetNiveauCompetence(ucfirst($Objet['objet_competence'])); $i++){
			$ChkRessource = true;
			$txtInfoBulle = '<table class="equipement">'
				.'<tr><th><b>'.$arQte[$i-1].'x</b> '.$Objet['objet_nom'].'</th></tr>'
				.'<tr><td>'.AfficheRessourceBesoin($Objet['objet_ressource'], $ChkRessource, $oJoueur, $arQte[$i-1]).'</td></tr>'
				.'</table>';
			if(!$ChkCompetence OR !$ChkRessource OR count($oJoueur->GetLstInventaire()) >= $oJoueur->QuelCapaciteMonBolga()){
				$check = false;
			}else{$check=true;}
			
			$txtAction .= '<br />
			<button
				type="button" 
				class="bricollage" 
				onclick="window.location=\'./fct/main.php?action=fabriquer&amp;id='.$numObjet.'&amp;qte='.$arQte[$i-1].'\'" '
				.'onmouseover="montre(\''.CorrectDataInfoBulle($txtInfoBulle).'\');" '
				.'onmouseout="cache();" '
				.($check?'':'disabled="disabled" ')
				.'style="background:'.(!$check?'Tomato':'LightGreen').';">'
					.($check?AfficheIcone('accept', 15):AfficheIcone('attention', 15)).' Fabriquer <b>'.$arQte[$i-1].'x</b>'
			.'</button>';
		}
	}
	
	$txt .=			'<tr>
						<td colspan="5" style="text-align:center;">'
							.$txtAction
						.'</td>
					</tr>
					<tr style="background:lightgrey; line-height:5px;"><td colspan="6">&nbsp;</td></tr>';
	
	$objManager->update($oJoueur);
	unset($oJoueur);
	return $txt;
}
function AfficheRessourceBesoin($lstRessources, &$ChkRessource, &$oJoueur, $nb = 1){
	$txt = '<ul style="list-style-type:none; padding:0px; text-align:center;">';
	//$ChkRessource = true;
	$nbRes = 0;
	$arLstRessources = explode(',', $lstRessources);
	$maison = FoundBatiment(1);
	foreach($arLstRessources as $Ressource){
		$arRessource = explode('=', $Ressource);
		$chkFound[$nbRes] = false;
		$ColorPrix = 'red';
		if(!is_null($maison)){
			if(in_array($arRessource['0'], array('ResBoi', 'ResPie', 'ResNou', 'ResOr'))){
				if(	('ResBoi' == $arRessource['0'] AND $maison->GetRessourceBois() >= ($arRessource['1'] * $nb))
					OR
					('ResPie' == $arRessource['0'] AND $maison->GetRessourcePierre() >= ($arRessource['1'] * $nb))
					OR
					('ResNou' == $arRessource['0'] AND $maison->GetRessourceNourriture() >= ($arRessource['1'] * $nb))
					OR
					('ResOr' == $arRessource['0'] AND $oJoueur->GetArgent() >= ($arRessource['1'] * $nb)))
					{
						$chkFound[$nbRes] = true;
						$ColorPrix = 'black';
					}
			}else{
				$chkFound[$nbRes] = $oJoueur->AssezElementDansBolga($arRessource['0'], ($arRessource['1'] * $nb));
				if($chkFound[$nbRes]){$ColorPrix = 'black';}
			}
		}
		//if($chkFound AND !$ChkRessource){$ChkRessource = false;}else{$ChkRessource = true;}
		$txt .= '<li style="display:inline; margin-right:20px;">'
					.'<span style="color:'.$ColorPrix.';'.($ColorPrix == 'red'?'font-weight:bold;':'').'">'.($arRessource['1'] * $nb).'</span>x '
					.AfficheIcone($arRessource['0'])
				.'</li>';
		$nbRes++;
	}
	
	$txt .= '</ul>';
	
	//on vérifie si on a tout
	foreach($chkFound as $chk){
		if(!$chk){
			$ChkRessource = false;
			break;
		}else{$ChkRessource = true;}
	}
	
	return $txt;
}
?>