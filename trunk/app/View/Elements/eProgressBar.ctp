
<?php
$Level = NULL;

if($bStatus){
    if($PourcentProgress >=70){$Level = 'progress-bar-success';}
    elseif($PourcentProgress >=40){$Level = 'progress-bar-warning';}
    else{$Level = 'progress-bar-danger';}
}
?>

<div class="progress">
    <div 
        class="progress-bar <?php echo $Level;?>" 
        role="progressbar"
        aria-valuenow="<?php echo $PourcentProgress;?>" 
        aria-valuemin="0" 
        aria-valuemax="<?php echo $MaxValue;?>"
        style="width: <?php echo $PourcentProgress;?>%"
        >
    <?php if(isset($Label) AND !empty($Label)):?>
        <span class="sr-only"><?php echo $Label;?></span>
    <?php endif;?>
    </div>
</div>
