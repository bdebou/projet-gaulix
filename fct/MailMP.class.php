<?php
class MailMP{
	//private $login;
	private $password;
	//public $nom;
	//public $prenom;
	public $mail;
	public $captchaResult;
	public $sendCheck = null;
	
	private function verif_null($var){
		return (!empty($var))?$var:null;
	}
    private function verif_mail($var){
		return (preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#',$var))?$var:null;
	}
	private function verif_captcha($var){
		return ($var==$_SESSION['captchaResult'])?$var:null;
	}
	private function verif_login($var){
		//require('config.php'); // On réclame le fichier
		Global $oDB;
		if($var=='login existant'){
			return null;
		}else{
			$sql = "SELECT * FROM table_joueurs WHERE login='".$var."'";
			// On vérifie si ce login existe
			return ($oDB->NbLigne($sql) == 0)?$var:null;
		}
	}
	private function Found_Password(){
		Global $oDB;
		$sql = "SELECT login, password FROM table_joueurs WHERE mail='".$this->mail."'";
		$requete = $oDB->Query($sql);
		
		if(mysql_num_rows($requete) > 0){
			$result = mysql_fetch_array($requete, MYSQL_ASSOC);
			$this->password = $result['password'];
			return true;
		}else{
			return false;
		}
	}
	
	public function inputTrue($input, $type = '1'){
		$style_blanc = ' style = "font-family: verdana;border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ffffff" ';
		$style_rouge = ' style = "font-family: verdana;border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ff0000" '; 
		$test = false;
		if(isset($_POST['mail'])){
			switch($type){
				case '1': $test = $this->verif_null($input);
					break;
				case '2':
					$test = $this->verif_mail($input);
					if(!is_null($test)){$test = $this->Found_Password();}
					break;
				case '3':
					$test = $this->verif_captcha($input);
					break;
			}
			if(is_null($test)){
				echo $style_rouge;
			}else{
				echo $style_blanc;
			}
		}
	}
	
	//fonction qui envoie le mail
	private function envoi_mail(){
		$Sujet = 'Gaulix - Mot de passe oublié';
		
		$Message = '
	<html>
		<head>
		</head>
		<body>
			<p>Bonjour,</p>
			<p>Vous avez demandé que l\'on vous envoie votre mot de passe pour <a href="http://www.gaulix.be">www.gaulix.be</a>.</p>
			<p>Le voici :</p>
			<p style="text-align:center; font-weight:bold; color:red; font-size:20px;">'.$this->password.'</p>
			<p>Nous vous conseillons de le changer au plus vite si besoin est.</p>
			<p>Nous vous remercions pour votre fidélité,</p>
			<p style="margin-left:50px; ;">L\'équipe Gaulix</p>
		</body>
	</html>';
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: Admin Gaulix<admin@gaulix.be>' . "\r\n";
		
		//echo $Message;
		mail($this->mail, $Sujet, $Message, $headers);
		
	}
	public function loadForm($data){
		extract($data);
		
		$this->mail          = $this->verif_mail($mail);
		$this->captchaResult = $this->verif_captcha($captchaResult);
		$test = $this->testForm();
		if(!empty($test)){
			$this->envoi_mail();
			$this->printForm();
			$this->sendCheck = 1;
		}else{
			echo '<div style="padding:5px;border:solid 2px #FF0000;background-color:#FEDFDF;width:600px;color:#ff0000;" >';
			echo 'Veuillez correctement remplir les champs en rouge.';
			echo '</div>';  
		}
	}
	public function testForm(){
		if($this->verif_null($this->mail)){
			if($this->verif_mail($this->mail) and $this->verif_captcha($this->captchaResult)){
				if($this->Found_Password()){
					return 1;
				}else{
					echo '
					<div style="padding:5px;border:solid 2px #FF0000;background-color:#FEDFDF;width:600px;color:#ff0000;" >
					Email non trouvé!
					</div>';
					return NULL;
				}
			}else{return NULL;}
		}else{return NULL;}
		return NULL;
	}
	private function printForm(){
		echo '
	<h3>Mot de passe envoyé</h3>
	<p>Un email contenant votre mot de passe vous a été envoyé à l\'adresse : '.$this->mail.'</p>
	<p><a href="./">Retour à la page d\'accueil</a></p>';
	}
	
}
?>