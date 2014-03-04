<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ObjetsController
 *
 * @author bdebou
 */
class ObjetsController extends AppController{
    
    public $components  = array('Session', 'Auth');
    
    public function beforeFilter() {
        parent::beforeFilter();
//        $this->Auth->allow();
    }
    
}

?>
