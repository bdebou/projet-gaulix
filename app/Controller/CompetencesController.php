<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CompetencesController
 *
 * @author bdebou
 */
class CompetencesController extends AppController{
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('listCompetences');
    }
    
    public function listCompetences(){
        $data = $this->Competence->find(
                'all',
                array(
                    'conditions'=>array(
                        'Competence.niveau'=>1
                        ),
                    'recursive'=>1,
                    'order'=>'Competence.cmptype_id ASC'
                    )
                );

        return $data;
    }
}

?>
