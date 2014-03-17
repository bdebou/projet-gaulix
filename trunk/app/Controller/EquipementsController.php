<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EquipementController
 *
 * @author bdebou
 */
class EquipementsController extends AppController{
    
    public $components  = array('Session', 'Auth');
    public $uses        = array('Equipement', 'Objet');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('listEquipement');
    }
    /**
    * Retourne la liste des équipementes
    * @param String <p>Array du ou des types d'équipement</p>
    * @return <array>
    */
    public function listEquipement($strType = NULL){
        if(is_null($strType))
            $strType=array(
                ObjArmementComponent::TYPE_ARME, 
                ObjArmementComponent::TYPE_BOUCLIER,
                ObjArmementComponent::TYPE_CASQUE,
                ObjArmementComponent::TYPE_CUIRASSE,
                ObjArmementComponent::TYPE_JAMBIERE);
        
	$data = $this->Objet->find(
                'all',
                array(
                    'conditions'=>array(
                        'Objtype.name'=>$strType
                        ),
                    'recursive'=>-1,
                    'fields'=>array(
                        'name',
                        'description',
                        'attaque', 
                        'defense',
                        'distance',
                        'Civilisation.name',
                        'Objtype.name'
                        ),
                    'joins' => array(
                        array(
                            'table' => 'civilisations',
                            'alias' => 'Civilisation',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Civilisation.id = Objet.civilisation_id'
                                )
                            ),
                        array(
                            'table' => 'objtypes',
                            'alias' => 'Objtype',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'Objtype.id = Objet.objtype_id'
                                ))
                        )
                    )
                );
        
        return $data;
    }
    
}

?>
