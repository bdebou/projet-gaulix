<?php
function AfficheDescriptifEquipement(personnage &$oJoueur){
	$txt = '
	<table class="equipement">
		<tr style="background:lightgrey;">
			<td colspan="8">&nbsp;</td>
		</tr>';
	for($i=1;$i<=5;$i++){
		switch($i){
			case 1: $CodeObjet = $oJoueur->GetCasque();		$txtNom = 'Casque';		break;
			case 2: $CodeObjet = $oJoueur->GetArme();		$txtNom = 'Arme';		break;
			case 3: $CodeObjet = $oJoueur->GetCuirasse();	$txtNom = 'Cuirasse';	break;
			case 4: $CodeObjet = $oJoueur->GetBouclier();	$txtNom = 'Bouclier';	break;
			case 5: $CodeObjet = $oJoueur->GetJambiere();	$txtNom = 'Jambière';	break;
		}
		if(!is_null($CodeObjet)){
			//$sql = "SELECT * FROM table_bricolage WHERE objet_code='".$CodeObjet."';";
			$sql = "SELECT * FROM table_objets WHERE objet_code='".$CodeObjet."';";
			$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
			$result = mysql_fetch_array($requete, MYSQL_ASSOC);
			$txt .= '
		<tr>
			<td rowspan="3" style="width:80px;">
				<a href="index.php?page=equipement&amp;action=unuse&amp;id='.$i.'">'.AfficheInfoObjet($result['objet_code'], 100).'</a>
			</td>
			<td>'.$result['objet_nom'].'</td>
			<td colspan="2">'.AfficheIcone('attaque').' : '.$result['objet_attaque'].'</td>
			<td colspan="2">'.AfficheIcone('defense').' : '.$result['objet_defense'].'</td>
			<td colspan="2">'.AfficheIcone('distance').' : '.$result['objet_distance'].'</td>
		</tr>
		<tr>
			<td rowspan="2">'.$result['objet_description'].'</td>
			<td colspan="3">Niv = '.$result['objet_niveau'].'</td>
			<td colspan="3">'.AfficheIcone('or').' : '.$result['objet_prix'].'</td>
		</tr>
		<tr>
			<td colspan="6"><a href="index.php?page=equipement&amp;action=unuse&amp;id='.$i.'">Remettre dans mon Bolga</a></td>
		</tr>
		<tr style="background:lightgrey;">
			<td colspan="8">&nbsp;</td>
		</tr>';
		}else{
			$txt .= '
		<tr>
			<td rowspan="3" style="width:100px;">'
			.$txtNom.'</td><td>Nom</td><td colspan="2">Attaque</td><td colspan="2">Defense</td><td colspan="2">Distance</td>
		</tr>
		<tr>
			<td rowspan="2">Description</td><td colspan="3">Niveau</td><td colspan="3">Prix</td>
		</tr>
		<tr>
			<td colspan="6">Actions</td>
		</tr>
		<tr style="background:lightgrey;">
			<td colspan="8">&nbsp;</td>
		</tr>';
		}
	}
	$txt .= '
	</table>';
	return $txt;
}
//+---------------------------------+
//|				ACTIONS				|
//+---------------------------------+
function ActionUnuse(&$check, personnage &$oJoueur){
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

?>