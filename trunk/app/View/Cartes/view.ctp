<?php
$this->Start('LoginPart');
    echo $this->Element('NavigationBar', array('Active'=>'cartes'));
    echo $this->Element('eLoged');
$this->end('LoginPart');

$this->set('title_for_layout', ' - Cartes');

?>
<div class="row">
    <div class="col-md-12">
        <h1>La Carte</h1>
	<table class="all_cartes">
<?php 
$this->Start('css');
echo $this->Html->css('cartes');
$this->end();

$numCol = 0;
//var_dump($Grilles);die();
//for($i=0; $i <= (count($arCartes) - 1); $i++):
foreach($arCartes as $numCarte):
    ?>
    <?php if($numCol == 0):?>
            <tr>
    <?php endif;?>
                <td>
                    <?php // echo AfficheCarte($oDB, $arCartes[$i], true, $arTailleCarte);?>
                    <?php echo $this->Element(
                          'eCarte',
                          array(
                              'AllCartes'=>true,
                              'ArGrilles'=>$Grilles,
                              'numCarte'=>$numCarte,
                              'nbLignes'=>$nbLignes,
                              'nbColonnes'=>$nbColonnes)
                          );?>
                </td>
	
	<?php $numCol++;?>
	
	<?php if($numCol == 5):?>
            </tr>
	<?php 
            $numCol = 0;
        endif;
endforeach;?>
            
	</table>
    </div><!-- /.col-md-12 -->
</div><!-- /.row -->

<div class="row"><?php echo $this->fetch('content');?></div><!--/row-->
