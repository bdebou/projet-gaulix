<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Carriere
 *
 * @author bdebou
 */
class Carriere extends AppModel{
    
    public $hasOne = array('Catcarriere');
    public $hasMany = array('User');
    public $belongsTo = array('Civilisation');
    
}

?>
