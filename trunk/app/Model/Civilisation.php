<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Civilisation
 *
 * @author bdebou
 */
class Civilisation extends AppModel{
    
    public $hasMany = array('User', 'Objet', 'Village');
}

?>
