<?php $this->set('title_for_layout', ' - Nouveau');?>
<div class="row">
    <div class="col-md-6">
    <?php
echo $this->Form->create(
        'User',
        array(
            'class'=>'form-horizontal',
            'role'=>'form',
            'inputDefaults'=>array(
                'div'=>array(
                    'class'=>'form-group'
                    ),
                'class'=>'form-control',
                'label'=>array(
                    'class'=>'col-sm-2 control-label'
                    )
                )
            )
        );
echo $this->Form->input(
        'username',
        array(
            'label'=>'Nom du personnage',
            'class'=>'form-control'
            )
        );
echo $this->Form->input(
        'password', 
        array(
            'label'=>'Mots de passe'
            )
        );
echo $this->Form->input(
        'email', 
        array(
            'label'=>'eMail'
            )
        );
echo $this->Form->input(
        'civilisation_id',
        array(
            'label'=>'Votre civilisation',
            'options'=>$lstCivilisation
            )
        );

echo $this->Form->end('Valider');

?>
    </div><!--/.col-md-10 -->
</div><!--/.row -->
