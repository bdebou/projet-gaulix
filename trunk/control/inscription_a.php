<?php
$contact = new InscriptionStepA();

$StepOne = false;

if(isset($_POST['captchaResult']) AND isset($_POST['next'])){
	$contact->loadForm($_POST);
	if($_POST['captchaResult'] === $_SESSION['captchaResult']){
		$StepOne = $contact->GetSendCheck();
	}
}

if(!$StepOne){
	include('view/forms/inscription_a.php');
}else{
	echo '<script type="text/javascript">window.location="index.php?page=inscription_b";</script>';
}
?>