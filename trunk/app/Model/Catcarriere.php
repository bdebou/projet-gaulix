<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Catcarriere
 *
 * @author bdebou
 */
class Catcarriere extends AppModel{
    
    public $hasAndBelongsToMany = array('Competence');
    public $hasMany = array('Carriere');
    
}

?>
