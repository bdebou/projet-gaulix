<?php
$data = $this->requestAction(
        array(
            'controller'=>'Users',
            'action'=>'GetStatistiques'
            )
        );
?>
<div class="row">
    <div class="col-md-12">
        <h1>Gaulix</h1>
        <div class="row">
            <div class="col-md-4">
                <?php echo $this->Html->image(
                       'presentation/carte.png',
                       array(
                           'alt'=>'Carte',
                           'title'=>'Exemple de carte',
                           'class'=>'img-responsive'
                           )
                       );?>
            </div><!--/ .col-md-4 -->
            <div class="col-md-5">
                <p>Vous incarnez un gaulois ou un romain qui doit survivre et évoluer.</p>
                <p>Vous vous déplacerez sur une carte de case en case.</p>
                <p>La carte globale comporte 25x la carte ci-contre. Pour un total de 14x14 cases par carte, 25 cartes donc <strong>4900 cases</strong>.</p>
            </div><!--/ .col-md-5 -->
            <div class="col-md-3">
                <table class="table">
                    <thead>
                        <tr><th>Statistiques</th></tr>
                    </thead>
                    <tbody>
                        <tr class="success">
                            <td>
                                <?php echo $data[PersonnageComponent::CIVILISATION_GAULOIS] . ' ' . PersonnageComponent::CIVILISATION_GAULOIS . ' inscrits';?>
                            </td>
                        </tr>
                        <tr class="danger">
                            <td>
                                <?php echo $data[PersonnageComponent::CIVILISATION_ROMAIN] . ' ' . PersonnageComponent::CIVILISATION_ROMAIN . ' inscrits';?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php
                                echo $this->Html->link(
                                        'Jeu-gratuit.net',
                                        'http://www.jeu-gratuit.net',
                                        array(
                                            'target'=>'_blank'
                                            )
                                        );
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div><!--/ .col-md-3 -->
        </div><!--/ .row -->
        <div class="row">
            <div class="col-md-8">
                <p>Vous avez un nombre X de déplacement. Toutes les <?php echo (PersonnageComponent::TEMP_DEPLACEMENT_SUP / 3600);?> h, connecté ou non, vous gagnerez <?php echo PersonnageComponent::NB_DEPLACEMENT_SUP;?>pt de déplacement.</p> 
                <p>Comme vous pouvez le voir ci-contre, il reste 23 déplacements et plus de 58 min pour en gagner un nouveau.</p>
                <p>Vous aurez plusieurs d'actions possible en fonction de votre niveau, compétences acquises (voir <a href="#competences" data-toggle="tab">Compétences</a>), quêtes terminées (voir <a href="#quetes" data-toggle="tab">Quêtes</a>), ... .</p>
            </div><!--/ .col-md-7 -->
            <div class="col-md-4">
                <?php echo $this->Html->image(
                    'presentation/move.png',
                    array(
                        'alt'=>'Move',
                        'title'=>'Les flèches de directions',
                        'class'=>'img-responsive'
                        )
                    );?>
            </div><!--/ .col-md-5 -->
        </div><!--/ .row -->
        <div class="row">
            <div class="col-md-5">
                <?php echo $this->Html->image(
                    'presentation/actions.png',
                    array(
                        'alt'=>'Actions',
                        'title'=>'Exemple d\'actions',
                        'class'=>'img-responsive'
                        )
                    );?>
            </div><!--/ .col-md-5 -->
            <div class="col-md-7">
                <p>Comme dans cette exemple, vous pouvez attaquer un romain et vous prourriez construire un bâtiment.</p>
                <p>Malheureusement dans ce cas-ci, vous n'avez pas assez de ressources pour construire la "Palissade". <strong>Allez couper du bois!</strong></p>
            </div><!--/ .col-md-7 -->
        </div><!--/ .row -->
    </div><!--/ .col-md-12 -->
</div><!--/ .row -->
