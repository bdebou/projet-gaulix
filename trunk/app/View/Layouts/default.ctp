<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>Gaulix<?php echo $title_for_layout;?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <style>
            body {
              padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
            }
          </style>
	<?php
		echo $this->Html->meta('icon');
                echo $this->fetch('meta');
                
		echo $this->Html->css('bootstrap.min');
//                echo $this->Html->css('bootstrap-responsive');
                echo $this->fetch('css');
	?>
</head>
<body>
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#glx-navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <?php echo $this->Html->link(
                      'Gaulix',
                      '/',
                      array(
                          'class'=>'navbar-brand'
                          )
                      );
                ?>
            </div>
            <div class="navbar-collapse collapse" id="glx-navbar-collapse">
                <?php echo $this->fetch('LoginPart');?>
                <?php
//                if($this->Session->read('Auth.User.id')){
//                    echo $this->fetch('NavigationBar');
//                    echo $this->Element('eLoged');
//                }else
//                    echo $this->Element('eLogin');
                ?>
            </div><!--/.navbar-collapse -->
            
        </div><!--/.container -->
    </div><!--/.navbar -->

    <div class="container">
        <?php echo $this->Session->Flash();?>
        <?php // echo $this->Element('InfoBar');?>
        <?php echo $this->fetch('content');?>
        <footer><hr />&copy; Gaulix v5.0</footer>
    </div><!--/container-->
            
    <?php echo $this->element('sql_dump'); ?>
</body>
<?php
    echo $this->html->script('https://code.jquery.com/jquery.js');
    echo $this->Html->script('bootstrap.min');

    echo $this->fetch('script');
?>

</html>
