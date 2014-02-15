<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ReglesController
 *
 * @author bdebou
 */
class ReglesController extends AppController {
    
    public $helpers = array('Html', 'Form');
    public $components = array('Session', 'Auth');
    
    
    public function beforeFilter(){
        parent::beforeFilter();
//        $this->Auth->allow('main');
    }
    
    public function view(){
        
    }
}

?>
