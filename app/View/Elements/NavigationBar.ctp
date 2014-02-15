<?php // $this->start('NavigationBar');?>

<ul class="nav navbar-nav">
    <li<?php if($Active=='main')echo ' class="active"';?>>
<?php 
echo $this->Html->link(
        'Actions',
        array(
            'controller'=>'pages'
            )
        );
?>
    </li>
    <li<?php if($Active=='cartes')echo ' class="active"';?>>
<?php 
echo $this->Html->link(
        'Cartes',
        array(
            'controller'=>'cartes',
            'action'=>'view'
            )
        );
?>
    </li>
    <li<?php if($Active=='regles')echo ' class="active"';?>>
<?php 
echo $this->Html->link(
        'Règles',
        array(
            'controller'=>'regles',
            'action'=>'view'
            )
        );
?>
    </li>
</ul>

<?php // $this->end();?>
