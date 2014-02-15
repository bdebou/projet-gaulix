<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ScoresController
 *
 * @author bdebou
 */
class ScoresController extends AppController{
    
    public $components = array('Session', 'Auth');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('listPoints');
    }
    
    public function index(){
        
    }
    Public Function listPoints(){
        return array(
            'CombatGagné'=>array(
                    abs(PersonnageComponent::POINT_COMBAT),
                    'Combat gagné'
                    ),
            'CombatPerdu'=>array(
                    (abs(PersonnageComponent::POINT_COMBAT) * -1),
                    'Combat perdu'
                    ),
	    'AttBatAdvers'=>array(
                    abs(BatimentComponent::POINT_BATIMENT_ATTAQUE),
                    'Attaque de batiment adverse'
                    ),
	    'BatAbimé'=>array(
                    (abs(BatimentComponent::POINT_BATIMENT_ATTAQUE) * -1),
                    'Un de vos batiment a été attaqué'
                    ),
	    'AttTour'=>array(
                    (abs(TourComponent::POINT_TOUR_ATTAQUE) * -1),
                    'Attaque d\'une tour'
                    ),
	    'CmpTerminé'=>array(
                    abs(PersonnageComponent::POINT_COMPETENCE_TERMINE),
                    '1 Niveau de compétence terminé'
                    ),
	    'NivTerminé'=>array(
                    abs(PersonnageComponent::POINT_NIVEAU_TERMINE),
                    'Passé 1 niveau'
                    ),
	    'BatRéparé'=>array(
                    abs(BatimentComponent::POINT_BATIMENT_REPARE),
                    'Batiment réparé à 100%'
                    ),
	    'ObjFabriqué'=>array(
                    abs(PersonnageComponent::POINT_OBJET_FABRIQUE),
                    'Objet fabriqué'
                    ),
	    'BatDetruit'=>array(
                    abs(BatimentComponent::POINT_BATIMENT_DETRUIT),
                    'Vous avez détruit un batiment adverse'
                    ),
	    'PersoTué'=>array(
                    (abs(PersonnageComponent::POINT_PERSO_TUE) * -1),
                    'Personnage tué'
                    )
            );
    }
    
}

?>
