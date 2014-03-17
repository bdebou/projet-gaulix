<!--on crée la table carte pour son affichage-->
<?php // var_dump($ArGrilles[$numCarte]);die();?>

<?php if($AllCartes):?>
    <table class="carte_petite" onmouseover="montre('<?php // echo CorrectDataInfoBulle('<b>Zone '.strtoupper($numCarte).'</b>');?>');" onmouseout="cache();">
    <?php $size = 8;?>
<?php else:?>
    <table class="carte" style="background-image: url('img/carte/gaule-<?php echo $numCarte;?>.jpg');">
    <?php $size = 29;?>
<?php endif;?>
        <tbody>
<?php for($i = 0; $i <= $nbLignes; $i++):?>
        <tr>
    <?php for($j = 0; $j <= $nbColonnes; $j++):?>
            <td <?php echo (isset($ArGrilles[$numCarte][$i][$j]['batiment'])?$ArGrilles[$numCarte][$i][$j]['batiment']:'');?>>
	<?php 
        if(isset($ArGrilles[$numCarte][$i][$j]['username'])){
            echo $this->Html->image(
                    'carte/'.$ArGrilles[$numCarte][$i][$j]['civilisation'].'-'.($this->Session->read('Auth.User.username')==$ArGrilles[$numCarte][$i][$j]['username']?'a':'b').'.png',
                    array(
                        'alt'=>'Perso '.$ArGrilles[$numCarte][$i][$j]['username'],
                        'title'=>$ArGrilles[$numCarte][$i][$j]['username'],
                        'height'=>$size.'px'
                        )
                    );
//                <img alt="Perso '<?php echo '.$grille[$i][$j]['login'].'" 
//                    src="./img/carte/'.$grille[$i][$j]['civilisation'].'-'.($_SESSION['joueur']==$grille[$i][$j]['login']?'a':'b').'.png" 
//                    height="'.$size.'px" 
//                    width="'.$size.'px" 
//<!--                    onmouseover="montre(\''.
//                    CorrectDataInfoBulle(
//                                    '<table><tr>'
//                                    .($AllCartes?
//                                            '<td rowspan="2">'
//                                            .'<img alt="Perso '.$grille[$i][$j]['login'].'" src="./img/carte/'.$grille[$i][$j]['civilisation'].'-'.($_SESSION['joueur']==$grille[$i][$j]['login']?'a':'b').'.png" height="30px" width="30px" />'
//                                            .'</td>'
//                                            :'')
//                                    .'<th>'.$grille[$i][$j]['login'].'</th></tr>'
//                                    .'<tr><td><img alt="Barre de Vie" src="./fct/fct_image.php?type=VieCarte&amp;value='.$grille[$i][$j]['vie'].'&amp;max='.personnage::VIE_MAX.'" /></td></tr>'
//                                    .'</table>'
//                                    )
//                    .'\');" 
//                    onmouseout="cache();"-->
//                />
        }
        ?>
            </td>
    <?php endfor;?>
        </tr>
<?php endfor;?>
        </tbody>
    </table>
