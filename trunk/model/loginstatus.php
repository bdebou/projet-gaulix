<?php
function AfficheRessource($type, personnage &$oJoueur){
	$maison = $oJoueur->GetObjSaMaison();
	
	if(!is_null($maison)){
		$nb = $maison->GetRessource($type);
		
		switch ($type){
			case maison::TYPE_RES_NOURRITURE:
				$qte = 50;
				break;
			case maison::TYPE_RES_BOIS:
				$qte = 25;
				break;
			case maison::TYPE_RES_PIERRE:
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
	
	return AfficheIcone($type) . ' : ' . $nb . $txtBt;
}


?>