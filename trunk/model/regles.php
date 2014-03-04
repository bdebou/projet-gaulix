<?php

/**
 * Retourne un tableau (class = points)avec la liste des points. 
 * @return string
 */
function AfficheTableauGainScores($lstPoints){

	$txt = '<table class="points">
		<tr style="background:grey;"><th style="width:40px;">Pts</th><th style="width:300px;">Action</th></tr>';
	foreach($lstPoints as $arPts){
		$txt .= '<tr><td style="background:'.($arPts[0] > 0?'LightGreen':'LightSalmon').';">'.($arPts[0] > 0?'+'.$arPts[0]:$arPts[0]).'</td><td>'.$arPts[1].'</td></tr>';
	}
	$txt .= '</table>';

	return $txt;
}
/**
 * Cr�e une liste de DIV pour chaque comp�tence de niveau 1. La class de ces DIV est <strong>regles_competences</strong>
 * @param DBManage <p>Instance de la Class de gestion de DB</p>
 * @return string  <i>Liste des DIV</i> 
 */
function AfficheListeCompetences(DBManage &$db){
	$txtDIV = NULL;
	
	$sql = "SELECT cmp_lst_type, cmp_lst_nom, cmp_lst_description, cmp_lst_acces 
				FROM table_competence_lst 
				WHERE cmp_lst_niveau=1 
				ORDER BY cmp_lst_type ASC;";
	
	$rqtLstCmp = $db->Query($sql);
	
	while($cmp = mysql_fetch_array($rqtLstCmp, MYSQL_ASSOC)){
		
		$txtDIV .= '
		<div class="regles_competences">
			<table class="regles_competences">
				<tr><th colspan="2">'.$cmp['cmp_lst_type'].' - '.ucfirst($cmp['cmp_lst_nom']).'</th></tr>
				<tr><td rowspan="3"><img src="" alt="'.$cmp['cmp_lst_type'].'" title="'.$cmp['cmp_lst_type'].'" width="100px" /></td></tr>
				<tr><td>'.$cmp['cmp_lst_description'].'</td></tr>
				<tr><td>Comp�tence pour '.ListCarriere($cmp['cmp_lst_acces']).'</td></tr>
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
/**
 * Retourne le nombre de joueur faisant partie d'une civilisation donn�e
 * @param DBManage $db
 * @param string $civilisation
 * @return number
 */
function NombreDeJoueurs(DBManage &$db, $civilisation){
	
	//On ex�cute sa query
	return $db->NbLigne("SELECT id FROM table_joueurs WHERE civilisation='".$civilisation."';");
}

?>