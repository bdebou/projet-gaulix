<?php
$contact = new InscriptionFormulaire();
if(isset($_POST['captchaResult'])){
	$contact->loadForm($_POST);
	if($_POST['captchaResult'] === $_SESSION['captchaResult']){$send = $contact->sendCheck;}
}
if(!$contact->sendCheck){
	include('view/forms/inscription.php');
}
?>