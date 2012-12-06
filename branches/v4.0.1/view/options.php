<?php
include_once('control/forms/options.php');
include_once('model/options.php');
?>
<div class="main">
<h1>Options</h1>
<table class="options">
<tr>
<?php include('view/forms/change_password.php');?>
<?php include('view/forms/change_notification.php');?>
</tr>
<tr>
<?php include('view/forms/change_email.php');?>
<?php include('view/forms/supprimer_compte.php');?>
</tr>
</table>
</div>
