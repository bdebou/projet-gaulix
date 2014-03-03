<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ObjCategory
 *
 * @author bdebou
 */
class Objcategory extends AppModel{
    
    public $hasMany = array('Objet');
    
}

?>
