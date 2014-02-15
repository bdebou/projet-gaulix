<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Competence
 *
 * @author bdebou
 */
class Competence extends AppModel{
    
    public $belongsTo = array(
        'Cmptype'=>array(
            'fields'        => 'Cmptype.name',
            'counterCache'  => true)
        );
    public $hasAndBelongsToMany = array('Catcarriere');
}

?>
