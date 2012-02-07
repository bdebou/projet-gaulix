<?php
$contact = new MailMP();
if(isset($_POST['captchaResult'])){
	$contact->loadForm($_POST);
	if($_POST['captchaResult'] === $_SESSION['captchaResult']){$send = $contact->sendCheck;}
}
if(empty($send)){
	include('view/forms/mp_oublie.php');
}
?>
