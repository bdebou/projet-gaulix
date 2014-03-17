<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author bdebou
 */
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class User extends AppModel{
    //put your code here
    public $validate = array(
        'name' => array(
            'unique'=>array(
                'rule'=>'isUnique',
                'message'=>'Ce personnage existe déjà.'
                ),
            'vide'=>array(
                'rule'=>array('allowEmpty' => false),
                'message' => 'Nom de personnage requis'
                )
            ),
        'password' => array(
            'rule' => array('minLength', '8'),
            'message' => 'Un mot de passe de minimum 8 caractères est requis'
            ),
        'email'=>'email',
        'date'=>array(
            'rule'=>array(
                'datetime', 'ymd')
            )
            );
    
    public $belongsTo = array(
        'Civilisation' => array(
            'counterCache' => true
            ),
        'Village'=>array(
            'counterCache'=>true
            ),
        'Carriere'
        );
    
    public $hasAndBelongsToMany = array('Objet');
    public $hasMany = array('ObjetU', 'Carte');
    
    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new SimplePasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash($this->data[$this->alias]['password']);
        }
        return true;
    }
    
}

?>
