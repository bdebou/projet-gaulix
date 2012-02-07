<?php
global $objManager;
$oJoueur = $objManager->GetPersoLogin($_SESSION['joueur']);

$CheckRetour = false;

$chgpassword = new ChangePassword();

if(isset($_POST['chg_pass'])){
	$chgpassword->loadForm($_POST);
	$PasswordChanged = $chgpassword->ChangeCheck;
	$CheckRetour = true;
}else{
	$PasswordChanged = null;
}
//======================
$chgemail = new ChangeEmail();

if(isset($_POST['chg_email'])){
	$chgemail->loadForm($_POST);
	$MailChanged = $chgemail->ChangeCheck;
	$CheckRetour = true;
}else{
	$MailChanged = null;
}
//========================
if(isset($_POST['SupprimerCpt']) and $_POST['SupprimerCpt'] == 'Supprimer'){
	include('model/options.php');
	Supprimer_Compte($oJoueur);
	header('location: index.php?page=unconnect');
}
//=========================
if(isset($_POST['ChgNot'])){
	include('model/options.php');
	ChangeNotification($oJoueur);
	$CheckRetour = true;
}

$objManager->update($oJoueur);
unset($oJoueur);

if($CheckRetour){
	header('location: index.php?page=options');
}
?>