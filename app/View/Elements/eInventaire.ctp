<div class="row">
    <div class="col-md-12">
        <h2>Inventaire</h2>
        <p>Voici la liste des objets de base que vous pourrez peut-être trouver lors de vos déplacements. Tous ces objets serviront à en fabriquer d'autre dont ceux que vous pouvez retrouver dans les <a href="#equipements">équipements</a>.</p>
    </div><!-- /.col-md-12-->
</div><!-- /.row-->

<?php
$LstRessources = $this->requestAction(
        array(
            'controller'=>'Objets',
            'action'=>'listRessource'
            )
        );
?>

<?php ForEach($LstRessources as $type=>$Info):?>
<div class="row">
    <div class="col-md-12">
        <h3><?php echo $type;?></h3>
        <?php foreach($Info as $k=>$v):?>
        <div class="col-md-4">
            <table class="table table-bordered">
                <?php $t=current($v);?>
                <?php // var_dump($v, $t);die();?>
                <thead>
                    <?php 
                    if($v['Civilisation']['name'] == PersonnageComponent::CIVILISATION_GAULOIS)$color='success';
                    elseif($v['Civilisation']['name'] == PersonnageComponent::CIVILISATION_ROMAIN)$color='danger';
                    else $color='warning';
                    ?>
                    <tr class="<?php echo $color;?>">
                        <th colspan="3"><?php echo $t['name'];?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="2">
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
                            Prix de revente
                        </td>
                        <td>
                            <?php
                            echo $t['prix'].' ';
                            echo $this->Html->image('icones/ic_Sesterce.png', array('height'=>'15px'));
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><?php echo $t['description'];?></td>
                    </tr>
                    
                </tbody>
            </table>
        </div><!-- /.col-md-4-->
        <?php endforeach;?>
    </div><!-- /.col-md-12-->
</div><!-- /.row-->
<?php endforeach;?>