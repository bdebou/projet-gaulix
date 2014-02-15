<div class="row">
    <div class="col-md-12">
        <h1>La Carte - INDEX</h1>
	<table class="all_cartes">
<?php 
$numCol = 0;

for($i=0; $i <= (count($arCartes) - 1); $i++):?>
    <?php if($numCol == 0):?>
            <tr>
    <?php endif;?>
                <td>
                    <?php // echo AfficheCarte($oDB, $arCartes[$i], true, $arTailleCarte);?>
                    <?php echo $this->Element(
                          'eCarte',
                          array(
                              'AllCartes'=>true,
                              'ArGrilles'=>$Grilles)
                          );?>
                </td>
	
	<?php $numCol++;?>
	
	<?php if($numCol == 5):?>
            </tr>
	<?php 
            $numCol = 0;
        endif;?>
<?php endfor;?>
	</table>
    </div><!-- /.col-md-12 -->
</div><!-- /.row -->

<div class="row"><?php echo $this->fetch('content');?></div><!--/row-->
