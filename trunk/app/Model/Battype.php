<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Battype
 *
 * @author bdebou
 */
class Battype extends AppModel{
    
    public $hasMany = array('Batiment', 'Carte');
    
}

?>
