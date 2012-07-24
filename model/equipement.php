<?php
function AfficheEquipement($type, personnage &$oJoueur) {
	$SizeHeight = 50;

	switch ($type) {
		case '1': $CodeObjet = $oJoueur->GetCasque();	break;
		case '2': $CodeObjet = $oJoueur->GetBouclier();	break;
		case '3': $CodeObjet = $oJoueur->GetJambiere();	break;
		case '4':
			$CodeObjet = $oJoueur->GetCuirasse();
			$SizeHeight = 150;
			break;
		case '5':
			$CodeObjet = $oJoueur->GetArme();
			$SizeHeight = 150;
			break;
		case '6': $CodeObjet = $oJoueur->GetSac();		break;
		case '7': $CodeObjet = $oJoueur->GetLivre();	break;
	}

	if (is_null($CodeObjet)) {
		return '&nbsp;';
	}

	//if ($_SESSION['main']['uri'] == 3
	if ($_GET['page'] == 'inventaire'
	AND count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga()
	AND !in_array($type, array(7))) {
		$chkLink = true;
	} else {
		$chkLink = false;
	}

	if (in_array($type, array(7))) {
		if ($CodeObjet == 'NoBook') {
			if (is_null($oJoueur->GetLstSorts())) {
				return '&nbsp;';
			} else {
				$arSort = explode('=', current($oJoueur->GetLstSorts()));
				$CodeObjet = $arSort[0];
			}
		} else {
			//on affiche le livre
			if(!is_null($oJoueur->GetLstSorts())){
				$InfoBulle = '<table class="equipement">';
				foreach ($oJoueur->GetLstSorts() as $Sort) {
					$arSort = explode('=', $Sort);
					$sql = "SELECT * FROM table_objets WHERE objet_code='" . strval($arSort[0]) . "';";
					$requete = mysql_query($sql) or die(mysql_error() . '<br />' . $sql);
					$result = mysql_fetch_array($requete, MYSQL_ASSOC);

					$InfoBulle .= '<tr><th colspan="2">' . $result['objet_nom'] . '</th></tr>'
					. '<tr>'
					. '<td><img src="./img/objets/' . $arSort['0'] . '.png" alt="' . $result['objet_nom'] . '" height="50px" /></td>'
					. '<td>' . $result['objet_description'] . '</td>'
					. '</tr>';
				}
				$InfoBulle .= '</table>';
			}else{$InfoBulle = '<table class="equipement"><tr><th>Votre livre est vide.</th></tr></table>';
			}
		}

		return ($chkLink ?'
			<a href="./fct/main.php?action=unuse&amp;id=' . $type . '">' : '')
		. '<img src="./img/objets/' . $CodeObjet . '.png" '
		. 'height="' . $SizeHeight . '" '
		. 'alt="Livre de sort" '
		. 'onmouseover="montre(\'' . CorrectDataInfoBulle($InfoBulle) . '\');" '
		. 'onmouseout="cache();" '
		. '/>'
		. ($chkLink ?'</a>' : '');
	} else {
		return ($chkLink ?'
    		<a href="./fct/main.php?action=unuse&amp;id=' . $type . '">' : '')
		.AfficheInfoObjet($CodeObjet, $SizeHeight)
		. ($chkLink ?'</a>' : '');
	}
}
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