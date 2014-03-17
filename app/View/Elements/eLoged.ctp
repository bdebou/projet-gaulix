<?php


echo $this->Form->create(
        'User', 
        array(
            'controller'    =>'Users',
            'action'        =>'logout',
            'class'         =>'navbar-form navbar-right',
            'role'          =>'form',
            'inputDefaults' =>array(
                'div'   =>array('class'=>'form-group'),
                'class' =>'form-control'
                )
            )
        );

echo $this->Form->submit(
        'Se déconnecter',
        array(
            'class'=>'btn btn-danger'
            )
        );

echo $this->Form->end();

echo $this->Html->para(
        'navbar-text navbar-right',
        'Logged in as '.$this->Session->read('Auth.User.username')
        );
?>

