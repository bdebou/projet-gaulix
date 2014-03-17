<?php
echo $this->Form->create(
        'User', 
        array(
            'controller'    =>'Users',
            'action'        =>'login',
            'class'         =>'navbar-form navbar-right',
            'role'          =>'form',
            'inputDefaults' =>array(
                'div'   =>array('class'=>'form-group'),
                'label' =>false,
                'class' =>'form-control'
                )
            )
        );
echo $this->Form->input(
        'username',
        array(
            'placeholder'=>'Nom perso'
            )
        );
echo $this->Form->input(
        'password',
        array(
            'placeholder'=>'Mot de passe'
            )
        );
echo $this->Form->submit(
        'Se connecter',
        array(
            'class'=>'btn btn-primary',
            'div'=>false
            )
        );
echo $this->Html->link(
        'Nouveau ?', 
        array(
            'controller'=>'users',
            'action'=>'add'
            ),
        array(
            'class'=>'btn btn-success'
            )
        );
echo $this->Form->end();

?>