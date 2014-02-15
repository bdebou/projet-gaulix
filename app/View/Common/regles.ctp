<div class="row">
    <div class="col-md-12">
        <div class="tabbable"> <!-- Only required for left/right tabs -->
            <ul class="nav nav-tabs" id="myTab">
              <li class="active"><a href="#presentation" data-toggle="tab">Présentation</a></li>
              <li><a href="#general" data-toggle="tab">Général</a></li>
              <li><a href="#competences" data-toggle="tab">Compétences</a></li>
              <li><a href="#scores" data-toggle="tab">Scores</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="presentation">
                  <?php
                  echo $this->Element(
                          'ePresentation',
                          array(),
                          array(
                              'cache'=>true
                          )
                          );
                  ?>
                </div><!-- /#presentation -->
                <div class="tab-pane fade" id="general">
                  <?php
                  echo $this->Element(
                          'eGeneral',
                          array(),
                          array(
                              'cache'=>true
                          )
                          );
                  ?>
                </div><!-- /#general -->
                <div class="tab-pane fade" id="competences">
                  <?php
                  echo $this->Element(
                          'eCompetences',
                          array(),
                          array(
                              'cache'=>true
                          )
                          );
                  ?>
                </div><!-- /#competences -->
                <div class="tab-pane fade" id="scores">
                    <?php
                echo $this->Element(
                        'eScores',
                        array(),
                        array(
                            'cache'=>true
                        )
                        );
                ?>
                </div><!-- /#scores -->
            </div><!-- /tab-content -->
        </div><!-- /tabbable -->
    </div><!-- /col-md-12 -->
</div><!-- /row -->
<?php echo $this->fetch('content');?>