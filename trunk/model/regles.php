<?php
/**
 * Retourne une liste de DIV de tous les objets sélectionnés
 * @param DBManage <p>Instance de la Class de gestion de DB</p>
 * @param string <p>String du type d'élément</p>
 * @return <NULL, string>
 */
function ReglesAfficheTableauEquipements(DBManage $db, $equipement){
	$bckColor='#c8c8c8';
	$txt=null;

	$sql="SELECT objet_code
			FROM table_objets 
			WHERE objet_type='".($equipement==objRessource::TYPE_RESSOURCE?$equipement:"Armement")."'
			AND objet_quete IS NULL;";

	$requete = $db->Query($sql);
	
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
 * Crée une liste de DIV pour chaque compétence de niveau 1. La class de ces DIV est <strong>regles_competences</strong>
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
/**
 * Retourne le nombre de joueur faisant partie d'une civilisation donnée
 * @param DBManage $db
 * @param string $civilisation
 * @return number
 */
function NombreDeJoueurs(DBManage &$db, $civilisation){
	
	//On exécute sa query
	return $db->NbLigne("SELECT id FROM table_joueurs WHERE civilisation='".$civilisation."';");
}

?>