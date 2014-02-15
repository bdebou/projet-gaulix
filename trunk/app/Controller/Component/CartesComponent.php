<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CarteComponent
 *
 * @author bdebou
 */
class CartesComponent  extends Component{
    
    public $components = array('Session', 'Auth');
    public $controller = false;
    
    private $lstCartes = array( 'a', 'b', 'c', 'd', 'e',
                                'f', 'g', 'h', 'i', 'j',
                                'k', 'l', 'm', 'n', 'o',
                                'p', 'q', 'r', 's', 't',
                                'u', 'v', 'w', 'x', 'y');
    
    Const NB_LIGNES         = 13;
    Const NB_COLONNES       = 13;
        
    public function __construct(ComponentCollection $collection, $settings){
        
        $this->controller = $collection->getController();
    }
    Public function GetListCartes(){
        return $this->lstCartes;
    }
    
    /**
    * Retourne la liste des cases libres d'une carte X ($carte = X) ou toutes les cartes ($carte = NULL)
    * @param string $carte Default value : NULL
    * @return array(string) Liste de coordonnées (c,x,y)
    */
   public function FreeCaseCarte($carte = NULL) {
        $data = $this->controller->Carte->find(
                'list',
                array(
                    'fields'=>array(
                        'coordonnee'
                        ),
                    'conditions'=>array(
                        'detruit'=>NULL,
                        array(
                            'NOT'=>array(
                                'battype_id'=>array(
                                    '11'
                                    )
                                )
                            )
                        )
                    )
                );

        foreach($data as $coordonnee){
            $arCoordonnee = explode(',', $coordonnee);
            if (is_null($carte) OR $arCoordonnee[0] == $carte) {
                $arBusy[$arCoordonnee[0]][$arCoordonnee[1]][$arCoordonnee[2]] = true;
            }
        }
        
        if (is_null($carte)) {
		$carte = $this->lstCartes[array_rand($this->lstCartes)];
	}
        
        for ($i = 0; $i <= self::NB_LIGNES; $i++) {
		for ($j = 0; $j <= self::NB_COLONNES; $j++) {
			if (!isset($arBusy[$carte][$i][$j])) {
				$arFree[] = implode(',', array($carte, $i, $j));
			}
		}
	}
        
        return $arFree;
    }
}

?>
