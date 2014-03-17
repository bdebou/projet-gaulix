<?php

$LstEquipement = $this->requestAction(
        array(
            'controller'=>'Equipements',
            'action'=>'listEquipement'
            )
        );
?>
<div class="row">
    <div class="col-md-12">
        <h2>Equipement</h2>
        <p>Vous présente votre équipement. Chaque équipement augmente votre pouvoir d'attaque ou de défense.</p>
        <p>Si vous cliquez sur un élément, il sera remis dans votre inventaire.</p>
        <p>Voici la liste des équipements possibles avec leurs caractéristiques et valeurs.</p>
    </div><!-- /.col-md-12-->
</div><!-- /.row-->

<?php ForEach(
        array(
            ObjArmementComponent::TYPE_ARME,
            ObjArmementComponent::TYPE_BOUCLIER,
            ObjArmementComponent::TYPE_CASQUE,
            ObjArmementComponent::TYPE_CUIRASSE,
            ObjArmementComponent::TYPE_JAMBIERE
            ) 
        as $type):?>
<div class="row">
    <div class="col-md-12">
        <h3><?php echo $type;?></h3>
        <?php foreach($LstEquipement as $k=>$v):?>
            <?php if($v['Objtype']['name'] == $type):?>
        <div class="col-md-4">
            <table class="table table-bordered">
                <?php $t=current($v);?>
                <thead>
                    <?php 
                    if($v['Civilisation']['name'] == PersonnageComponent::CIVILISATION_GAULOIS)$color='success';
                    elseif($v['Civilisation']['name'] == PersonnageComponent::CIVILISATION_ROMAIN)$color='danger';
                    else $color='warning';
                    ?>
                    <tr class="<?php echo $color;?>">
                        <th colspan="4"><?php echo $t['name'];?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="3">
                            <?php 
                            echo $this->Html->image(
                                    $t['link'], 
                                    array(
                                        'title'=>$t['name'],
                                        'heigh'=>'50px'
                                        )
                                    );
                            ?>
                        </td>
                        <td>
                            <?php 
                            echo is_null($t['attaque'])?'0':$t['attaque'];
                            echo $this->Html->image('icones/ic_Attaque.png', array('height'=>'15px'));
                            ?>
                        </td>
                        <td>
                            <?php 
                            echo is_null($t['defense'])?'0':$t['defense'];
                            echo $this->Html->image('icones/ic_Defense.png', array('height'=>'15px'));
                            ?>
                        </td>
                        <td>
                            <?php 
                            echo is_null($t['distance'])?'0':$t['distance'];
                            echo $this->Html->image('icones/ic_Distance.png', array('height'=>'15px'));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"><?php echo $t['description'];?></td>
                    </tr>
                    
                </tbody>
            </table>
        </div><!-- /.col-md-4-->
            <?php endif;?>
        <?php endforeach;?>
    </div><!-- /.col-md-12-->
</div><!-- /.row-->
<?php endforeach;?>