<div class="main">
<h1>Votre Bolga <dfn>(Sac)</dfn></h1>
<?php
global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);
?>
<table class="inventaire">
	<tr>
	<td rowspan="4" class="corp">
		<table class="corps">
			<tr><td colspan="2"></td><td class="membre" style="height:30px;"><?php echo AfficheEquipement(1, $oJoueur);?></td><td colspan="2">&nbsp;</td></tr>
			<tr><td class="membre" style="height:80px; width: 15px;"><?php echo AfficheEquipement(5, $oJoueur);?></td><td colspan="3" class="membre" style=""><?php echo AfficheEquipement(4, $oJoueur);?></td><td class="membre" style="height:80px; width: 15px;"><?php echo AfficheEquipement(2, $oJoueur);?></td></tr>
			<tr><td>&nbsp;</td><td class="membre" style="height: 80px;"><?php echo AfficheEquipement(3, $oJoueur);?></td><td>&nbsp;</td><td class="membre" style="height: 80px;"><?php echo AfficheEquipement(3, $oJoueur);?></td><td>&nbsp;</td></tr>
			<tr><td colspan="5">&nbsp;</td></tr>
			<tr><td class="membre"><?php echo AfficheEquipement(7, $oJoueur);?></td><td colspan="3">&nbsp;</td><td class="membre"><?php echo AfficheEquipement(6, $oJoueur);?></td></tr>
		</table>
	</td>
	</tr>
<?php
$id = 0;
$numC = 0;
$numL = 0;
if(!is_null($oJoueur->GetLstInventaire())){
	foreach($oJoueur->GetLstInventaire() as $Objet){
		$arObjet = explode('=', $Objet);
		
		if(substr($arObjet['0'], 0, 5) == 'Tissu'){$arObjet['0'] = 'Tissu';}
		
		$sql = "SELECT * FROM table_objets WHERE objet_code='".strval($arObjet['0'])."';";
		$requete = mysql_query($sql) or die (mysql_error().'<br />'.$sql);
		$result = mysql_fetch_array($requete, MYSQL_ASSOC);
		
		if($numC == 0){
			echo '<tr style="vertical-align:top;"><td>'.AfficheObjetInventaire($arObjet['0'], $result, $id, $arObjet['1'], $oJoueur).'</td>';
			$numC=1;
			$numL++;
		}elseif($numC == 1){
			echo '<td>'.AfficheObjetInventaire($arObjet['0'], $result, $id, $arObjet['1'], $oJoueur).'</td>';
			if($numL < 4){
				$numC = 0;
				echo '</tr>';
			}else{$numC = 2;}
		}elseif($numC == 2){
			echo '<td>'.AfficheObjetInventaire($arObjet['0'], $result, $id, $arObjet['1'], $oJoueur).'</td></tr>';
			$numC = 0;
		}
		$id++;
	}
}
For($i=$id; $i<=($oJoueur->QuelCapaciteMonBolga() -1);$i++){
	if($numC == 0){
		echo '<tr style="vertical-align:top;"><td>'.AffichePlaceVide().'</td>';
		$numC = 1;
		$numL++;
	}elseif($numC == 1){
		echo '<td>'.AffichePlaceVide().'</td>';
		if($numL < 4){
			$numC = 0;
			echo '</tr>';
		}else{$numC = 2;}
	}elseif($numC == 2){
		echo '<td>'.AffichePlaceVide().'</td></tr>';
		$numC = 0;
	}
}

$objManager->update($oJoueur);
unset($oJoueur);
?>
	</tr>
</table>
</div>