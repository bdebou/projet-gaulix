<?php
$InfoUser = $this->requestAction(
        array(
            'controller'=>'Users',
            'action'=>'InfoBar'
            )
        );
var_dump($InfoUser);
?>

<div class="col-md-3">
<?php
echo $this->Element(
        'eProgressBar', 
        array(
            'PourcentProgress'  => intval(($InfoUser['User']['vie']/PersonnageComponent::VIE_MAX)*100),
            'MaxValue'          => intval(PersonnageComponent::VIE_MAX),
            'Label'             => 'Vie'.$InfoUser['User']['vie'].' / '.PersonnageComponent::VIE_MAX,
            'bStatus'          => True
            )
        );
?>
</div><!-- /.col-md-3-->
<div class="col-md-3">
            <?php
echo $this->Element(
        'eProgressBar',
        array(
            'PourcentProgress'  => intval($InfoUser['User']['experience'] / $InfoUser['User']['maxExperience']*100),
            'MaxValue'          => intval($InfoUser['User']['maxExperience']),
            'Label'             => 'Experience: '.$InfoUser['User']['experience'].' / '.$InfoUser['User']['maxExperience'],
            'bStatus'          => False
            )
        );
?>
</div><!-- /.col-md-3-->
<div class="col-md-2">
    Force: <?php echo $InfoUser['User']['attaque'];?>(<?php echo $InfoUser['Objet']['Attaque'];?>)
</div><!-- /.col-md-2-->
<div class="col-md-2">
    Defense: <?php echo $InfoUser['User']['defense'];?>(<?php echo $InfoUser['Objet']['Defense'];?>)
</div><!-- /.col-md-2-->
<div class="col-md-2">
    Distance: <?php echo $InfoUser['Objet']['Distance'];?>
</div><!-- /.col-md-2-->
<!--<div class="col-md-2">
    Liste des objets
</div> /.col-md-2-->
