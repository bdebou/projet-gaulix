<?php
/**
 * Retourne une liste de DIV de tous les objets sélectionnés
 * @param string <p>String du type d'élément</p>
 * @return <NULL, string>
 */
function ReglesAfficheTableauEquipements($equipement){
	$bckColor='#c8c8c8';
	$txt=null;

	$sql="SELECT objet_code
			FROM table_objets 
			WHERE objet_type='".($equipement==objRessource::TYPE_RESSOURCE?$equipement:"Armement")."'
			AND objet_quete IS NULL;";

	$requete = mysql_query($sql) or die (mysql_error());
	while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
		if($bckColor=='#c8c8c8')
		{
			$bckColor='white';
		}else{
			$bckColor='#c8c8c8';
		}
		
		$oEquipement = FoundObjet($row['objet_code']);
		
		if(	$equipement == objRessource::TYPE_RESSOURCE
			OR (	$equipement != objRessource::TYPE_RESSOURCE
					AND $oEquipement->GetType() == $equipement
				)
			)
		{
			
			$txt .= '
						<div class="equipements" style="background:'.$bckColor.';">
							<table>
								<tr><td>'.$oEquipement->AfficheInfoObjet(100).'</td></tr>
								<tr><td>'.$oEquipement->GetNom().'</td></tr>
							</table>
						</div>';
		}
		
	}

	return $txt;
}
/**
 * Retourne un tableau (class = points)avec la liste des points. 
 * @return string
 */
function AfficheTableauGainScores(){
	global $lstPoints;

	$txt = '<table class="points">
		<tr style="background:grey;"><th style="width:40px;">Pts</th><th style="width:300px;">Action</th></tr>';
	foreach($lstPoints as $arPts){
		$txt .= '<tr><td style="background:'.($arPts[0] > 0?'LightGreen':'LightSalmon').';">'.($arPts[0] > 0?'+'.$arPts[0]:$arPts[0]).'</td><td>'.$arPts[1].'</td></tr>';
	}
	$txt .= '</table>';

	return $txt;
}
/**
 * Crée une liste de DIV pour chaque compétence de niveau 1. La class de ces DIV est <strong>regles_competences</strong>
 * @return string  <i>Liste des DIV</i> 
 */
function AfficheListeCompetences(){
	$txtDIV = NULL;
	
	/* $NomPrecedent = null;
	$nbComp = 0;
	$NumCol = 0;
	$CheckA = false;
	$CheckB = false; */
	
	//$sqlLstCmp = "SELECT * FROM table_competence_lst WHERE cmp_lst_niveau =1 ORDER BY cmp_lst_nom ASC;";
	$sqlLstCmp = "SELECT cmp_lst_type, cmp_lst_nom, cmp_lst_description, cmp_lst_acces 
					FROM table_competence_lst 
					WHERE cmp_lst_niveau =1 
					ORDER BY cmp_lst_type ASC;";
	$rqtLstCmp = mysql_query($sqlLstCmp) or die (mysql_error().'<br />'.$sqlLstCmp);
	
	while($cmp = mysql_fetch_array($rqtLstCmp, MYSQL_ASSOC)){
		
		$txtDIV .= '
		<div class="regles_competences">
			<table class="regles_competences">
				<tr><th colspan="2">'.$cmp['cmp_lst_type'].' - '.ucfirst($cmp['cmp_lst_nom']).'</th></tr>
				<tr><td rowspan="3"><img src="" alt="'.$cmp['cmp_lst_type'].'" title="'.$cmp['cmp_lst_type'].'" width="100px" /></td></tr>
				<tr><td>'.$cmp['cmp_lst_description'].'</td></tr>
				<tr><td>Compétence pour '.ListCarriere($cmp['cmp_lst_acces']).'</td></tr>
			</table>
		</div>';
		
	}
	
	return $txtDIV;
}
function ListCarriere($strAcces){
	
	if($strAcces == 'Tous')
	{
		return "tout le monde";
	}
	
	$arAcces = explode(',', $strAcces);
	
	return 'les '.strtolower(implode('s et les ', $arAcces)).'s';
}
function NombreDeJoueurs($civilisation){
	$sql = "SELECT id FROM table_joueurs WHERE civilisation='".$civilisation."';";
	$rqt = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
	
	return mysql_num_rows($rqt);
}

?>