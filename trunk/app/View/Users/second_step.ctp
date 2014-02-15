<?php $this->set('title_for_layout', ' - Finalisation inscription');?>
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
///Si on n'a pas de village, on fournit la liste pour en choisir un ou créer.
if(isset($lstVillage)){
    echo $this->Form->radio(
            'village_id',
            $lstVillage,
            array(
                'separator'=>'<br />',
                'legend'=>'Choisissez votre village'
                )
            );
    echo $this->Form->input('Village.name',array('label'=>'Nouveau village'));
}
// Si on n'a pas de carrière plannifiée, on fournit la liste pour la choisir.
if(isset($lstCarriere)){
    echo $this->Form->radio(
            'carriere_id',
            $lstCarriere,
            array(
                'separator'=>'<br />',
                'legend'=>'Choisissez votre carrière'
                )
            );
}

echo $this->Form->end('Valider');
?>
    </div><!--/.col-md-10 -->
</div><!--/.row -->