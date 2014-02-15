<?php
$LstPoints = $this->requestAction(
        array(
            'controller'=>'Scores',
            'action'=>'ListPoints'
            )
        );
?>
<div class="row">
    <div class="col-md-12">
        <h2>Scores</h2>
        <p>Vous trouverez la liste des joueurs avec un aperçu de leur status, le nombre de combats gagnés et perdus.</p>
        <p>Pour info, les combats dont les résultats sont nuls ne sont pas comptabilisés.</p>
        <div class="row">
            <div class="col-md-4">
                <table class="table">
                    <thead>
                        <tr><th>Pts</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($LstPoints as $k=>$arPts):?>
                        <tr class="<?php echo ($arPts[0] > 0?'success':'danger');?>"><td><?php echo ($arPts[0] > 0?'+'.$arPts[0]:$arPts[0]);?></td><td><?php echo $arPts[1];?></td></tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div><!--/ .col-md-4 -->
            <div class="col-md-8">
                <p>Pour certaines actions, un nombre X de points vous sera attribué et un tableau des scores sera créé.</p>
                <p>Pour la liste des points attribué pour chaque action, voir tableau ci-contre.</p>
                <p>Chaque <span class="underline">construction de batiment</span> vous rapportera un nombre spécifique de points, mais vous les perdrez si il est détruit.</p>
                <p>Chaque <span class="underline">Quête</span> vous apporte un nombre spécifique de points.</p>
            </div><!--/ .col-md-8 -->
        </div><!--/ .row -->
    </div><!--/ .col-md-12 -->
</div><!--/ .row -->
