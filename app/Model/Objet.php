<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Object
 *
 * @author bdebou
 */
class Objet extends AppModel{
    
    public $belongsTo = array('Civilisation', 'Objtype', 'Objcategory');
    
    public $hasAndBelongsToMany = array('User');
    
    public function beforeFind($query){
        if(isset($query['fields'])){
            if(!in_array('id', $query['fields']))
                    $query['fields'][]='id';
        }
        return $query;
    }
    
    public function afterFind($results, $primary = false){
        foreach($results as $k=>$v){
            if (isset($v['Objet']['id']))
                $results[$k]['Objet']['link'] = 'objets/'.str_pad($v['Objet']['id'], 5, '0', STR_PAD_LEFT) . '.png';
        }
        return $results;
    }
    
}

?>
