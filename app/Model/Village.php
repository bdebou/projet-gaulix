<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Village
 *
 * @author bdebou
 */
class Village extends AppModel{
    
    public $hasMany = array('User');
    public $belongsTo = array('Civilisation');
    
}

?>
