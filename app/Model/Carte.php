<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Carte
 *
 * @author bdebou
 */
class Carte extends AppModel{
    
//    public $hasOne = array('Battype', 'User');
    public $belongsTo = array('Battype', 'User');
    
}

?>
