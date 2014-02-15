<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CarteComponent
 *
 * @author bdebou
 */
class CartesComponent  extends Component{
    
    public $components = array('Session', 'Auth');
    
    Const NB_LIGNES         = 14;
    Const NB_COLONNES       = 14;
        
    Public function GetListCartes(){
        return array(    'a', 'b', 'c', 'd', 'e',
                         'f', 'g', 'h', 'i', 'j',
                         'k', 'l', 'm', 'n', 'o',
                         'p', 'q', 'r', 's', 't',
                         'u', 'v', 'w', 'x', 'y');
    }
}

?>
