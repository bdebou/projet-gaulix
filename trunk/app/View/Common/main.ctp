<div class="row">
    <div class="col-md-12 well well-sm">
        <?php echo $this->Element('InfoBar');?>
    </div><!--/.col-md-12 well well-sm--> 
</div><!--/.row--> 
<div class="row">
    <div class="col-md-5 well">
        <?php echo $this->Element('eCarte');?>
    </div><!--/col-md-5-->
    <div class="col-md-7">
        <div class="row">
            La carte
            
        </div><!--/row-->
        <div class="row">
            <?php echo $this->Element('ePub');?>
        </div><!--/row-->
    </div><!--/col-md-7-->
</div><!-- /.row -->
<div class="row">
    Et encore ici on mettra les actions possible
    <?php echo $this->fetch('ActionList');?>
</div><!--/row-->           
<div class="row">
    et le reste
    <?php echo $this->fetch('content');?>
</div><!--/row-->
