<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ObjetsController
 *
 * @author bdebou
 */
class ObjetsController extends AppController{
    
    public $components  = array('Session', 'Auth', 'ObjRessource');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('listRessource');
    }
    /**
    * Retourne la liste des objets de la catégorie 'Ressources'
    * @return <array>
    */
    public function listRessource(){
        $data = $this->Objet->find(
                'all',
                array(
                    'conditions'=>array(
                        'ObjCategory.name'=>  ObjRessourceComponent::TYPE_RESSOURCE,
                        'quete_id'=>NULL
                        ),
                    'recursive'=>-1,
                    'fields'=>array(
                        'name',
                        'description',
                        'prix',
                        'Civilisation.name',
                        'Objtype.name',
                        'Objcategory.name'
                        ),
                    'joins' => array(
                        array(
                            'table'     => 'civilisations',
                            'alias'     => 'Civilisation',
                            'type'      => 'LEFT',
                            'conditions'=> array(
                                'Civilisation.id = Objet.civilisation_id'
                                )
                            ),
                        array(
                            'table'     => 'objtypes',
                            'alias'     => 'Objtype',
                            'type'      => 'LEFT',
                            'conditions'=> array(
                                'Objtype.id = Objet.objtype_id'
                                )
                            ),
                        array(
                            'table'     => 'objcategories',
                            'alias'     => 'Objcategory',
                            'type'      => 'LEFT',
                            'conditions'=> array(
                                'Objcategory.id = Objet.objcategory_id'
                                )
                            )
                        )
                    )
                );
        
        return $this->reorgData($data, 'Objtype');
    }
    Private function reorgData($input, $groupBy){
        $data=null;
        foreach($input as $k1=>$v1){
            if(isset($v1[$groupBy]['name']))
            {
                foreach($v1 as $k2=>$v2)
                {
                    if($k2 != $groupBy)
                    {
                        $data[$v1[$groupBy]['name']][$k1][$k2]=$v2;
                    }
                }
            }
        }
        return $data;
    }
}

?>
