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
        <?php ForEach(
                array(
                    ObjArmementComponent::TYPE_ARME,
                    ObjArmementComponent::TYPE_BOUCLIER,
                    ObjArmementComponent::TYPE_CASQUE,
                    ObjArmementComponent::TYPE_CUIRASSE,
                    ObjArmementComponent::TYPE_JAMBIERE
                    ) 
                as $type):?>
        <div class="col-md-4">
            <h3><?php echo $type;?></h3>
            <table class="table table-bordered">
                <?php foreach($LstEquipement as $k=>$v):?>
                <?php if($v['Objtype']['name'] == $type):?>
                <?php $t=current($v);?>
                <?php // var_dump($v, $t);?>
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
                            echo $t['attaque'];
                            echo $this->Html->image('icones/ic_Attaque.png', array('height'=>'15px'));
                            ?>
                        </td>
                        <td>
                            <?php 
                            echo $t['defense'];
                            echo $this->Html->image('icones/ic_Defense.png', array('height'=>'15px'));
                            ?>
                        </td>
                        <td>
                            <?php 
                            echo $t['distance'];
                            echo $this->Html->image('icones/ic_Distance.png', array('height'=>'15px'));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        
                    </tr>
                    <tr>
                        <td colspan="3"><?php echo $t['description'];?></td>
                    </tr>
                    
                </tbody>
                <?php endif;?>
                <?php endforeach;?>
            </table>
        </div><!-- /.col-md-4-->
        <?php endforeach;?>
    </div><!-- /.col-md-12-->
</div><!-- /.row-->