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
    
    public $belongsTo = array('Civilisation');
    
    public $hasAndBelongsToMany = array('User');
}

?>
