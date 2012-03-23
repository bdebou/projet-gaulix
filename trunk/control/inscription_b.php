<?php
$objStepTwo = new InscriptionStepB();

$StepTwo = false;
 
if(isset($_POST['carriere']) AND isset($_POST['village'])){
	$objStepTwo->loadForm($_POST);
	$StepTwo = $objStepTwo->GetSendCheck();
}

if(!$StepTwo){
	include('view/forms/inscription_b.php');
}
?>