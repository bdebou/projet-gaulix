<?php
function AfficheRessource($type, personnage &$oJoueur){
	$maison = $oJoueur->GetObjSaMaison();
	
	if(!is_null($maison)){
		switch (ucfirst($type)){
			case 'Nourriture':
				$nb = $maison->GetRessourceNourriture();
				$qte = 50;
				break;
			case 'Bois':
				$nb = $maison->GetRessourceBois();
				$qte = 25;
				break;
			case 'Pierre':
				$nb = $maison->GetRessourcePierre();
				$qte = 25;
				break;
		}
		if ($nb > 500) {
			$_SESSION['LoginStatus'][$type] = $qte;
			
			$InfoBulle = '<table class="equipement"><tr><td>Mettre ' . $qte . 'pts ' . AfficheIcone($type, 15) . ' dans votre Bolga</td></tr></table>';
			$txtBt = '
					<button '
			. 'type="button" '
			. (count($oJoueur->GetLstInventaire()) < $oJoueur->QuelCapaciteMonBolga() ? '' : 'disabled="disabled" ')
			. 'class="LoginStatus" '
			. 'onmouseover="montre(\'' . CorrectDataInfoBulle($InfoBulle) . '\');" '
			. 'onmouseout="cache();" '
			. 'onclick="window.location=\'index.php?page=common&amp;action=MettreBolga&amp;type='.$type.(isset($_GET['page'])?'&amp;retour='.$_GET['page']:'').'\'" '
			. 'alt="Mettre ' . $qte . 'pts de ' . $type . ' dans votre Bolga">'
			. '-' . $qte . 'x'
			. '</button>';
		} else {
			$txtBt = NULL;
		}
	} else {
			$txtBt = NULL;
	}
	
	return AfficheIcone(ucfirst($type)) . ' : ' . $nb . $txtBt;
}


?>