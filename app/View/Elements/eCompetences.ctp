<?php
$LstCmp = $this->requestAction(
        array(
            'controller'=>'Competences',
            'action'=>'listCompetences'
            )
        );

$nbBlock = 3;
?>
<div class="row">
    <div class="col-md-12">
        <h2>Compétences</h2>
        <p>Mise à part de l'amélioration de votre attaque et de votre défense, vous pourrez apprendre ou amélioraer vos connaissance dans certain domaines tel que la Métallurgie, la cuisine, la médecine, la magie, ...</p> 
        <p>Voici un apperçu des quelques compétences disponible:</p>
        <?php $nb = 0;?>
        <?php foreach($LstCmp as $k=>$cmp):?>
            <?php if($nb == 0):?>
        <div class="row">
            <?php endif;?>
            <div class="col-md-<?php echo (12/$nbBlock);?>">
                <table class="table table-striped">
                    <thead>
                        <tr><th colspan="2"><?php echo $cmp['Cmptype']['name'];?> - <?php echo $cmp['Competence']['name'];?></th></tr>
                    </thead>
                    <tbody>
                        <tr><td rowspan="3"><img src="" alt="<?php echo $cmp['Cmptype']['name'];?>" title="<?php echo $cmp['Cmptype']['name'];?>" width="100px" /></td></tr>
                        <tr><td><?php echo $cmp['Competence']['description'];?></td></tr>
                        <tr>
                            <td>Compétence pour 
                                <ul>
                                    <?php Foreach($cmp['Catcarriere'] as $k=>$Carriere):?>
                                    <li><?php echo $Carriere['name'];?></li>
                                    <?php endforeach;?>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div><!--/.col-md-<?php echo (12/$nbBlock);?> -->
            <?php $nb++;?>
            <?php if($nb == $nbBlock):?>
            <?php $nb = 0;?>
        </div><!--/.row -->
            <?php endif;?>
        <?php endforeach;?>
        <?php if($nb <> 0):?>
        </div><!--/.row -->
        <?php endif;?>
    </div><!--/.col-md-12 -->
</div><!--/.row -->


