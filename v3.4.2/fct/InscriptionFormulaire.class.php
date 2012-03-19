<?php
class InscriptionFormulaire{
	public	$login,
			$password,
			$mail,
			$captchaResult;
	
	public $sendCheck = false;
	
	private function verif_null($var){
		return (!empty($var))?true:false;
	}
    private function verif_mail($var){
		return (preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#',$var))?true:false;
	}
	private function verif_captcha($var){
		return ($var==$_SESSION['captchaResult'])?true:false;
	}
	private function verif_login($var){
		require('./fct/config.php'); // On réclame le fichier
		if($var == 'login existant'){
			return false;
		}elseif(in_array($var, array('romain'))){
			$this->login='login existant';
			return false;
		}else{
			$sql = "SELECT * FROM table_joueurs WHERE login='".$var."'";
			// On vérifie si ce login existe
			$requete_1 = mysql_query($sql) or die ( mysql_error() );
			
			if(mysql_num_rows($requete_1)==0){
				return true;
			}else{
				$this->login='login existant';
				return false;
			}
		}
	}
	public function inputTrue($input, $type = '1'){
		$style_blanc = ' style = "font-family: verdana;border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ffffff" ';
		$style_rouge = ' style = "font-family: verdana;border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ff0000" '; 
		
		$chk = false;
		if(isset($_POST['envoyer'])){
			switch($type){
				case '1': $chk = $this->verif_null($input);
					break;
				case '2':
					if($this->verif_null($input) AND $this->verif_mail($input)){$chk = true;}
					break;
				case '3':
					if($this->verif_null($input) AND $this->verif_login($input)){$chk = true;}
					break;
				case '4': if($this->verif_null($input) AND $this->verif_captcha($input)){$chk = true;}
					break;
			}
			if($chk){
				echo $style_blanc;
			}else{
				echo $style_rouge;
			}
		}
	}
	private function envoi_sql(){ //fonction qui envoie la requete SQL
		require('./fct/config.php'); // On réclame le fichier
		$sql = 	"INSERT INTO table_joueurs (
				`id`, 
				`login`, 
				`password`, 
				`mail`, 
				`dates`, 
				`position`, 
				`last_action`, 
				`date_last_combat`) 
			VALUES (
				NULL, 
				'".$this->login."', 
				'".$this->password."', 
				'".$this->mail."', 
				'".date('Y-m-d H:i:s')."', 
				'".$this->PositionAleatoire()."', 
				'".date('Y-m-d H:i:s')."', 
				'".date('Y-m-d H:i:s')."');";
		mysql_query($sql) or die ( mysql_error().'<br />'.$sql);
	}
	public function loadForm($data){
		extract($data);
		$this->login         = trim(htmlentities($login, ENT_QUOTES));
		$this->password      = $password; //trim(htmlentities($password, ENT_QUOTES));
		$this->mail          = $mail;
		$this->captchaResult = $captchaResult;
		
		if($this->testForm()){
			$this->envoi_sql();
			$this->printForm();
			$this->sendCheck = true;
		}else{
			echo '<div style="padding:5px;border:solid 2px #FF0000;background-color:#FEDFDF;width:600px;color:#ff0000;" >';
			echo 'Veuillez correctement remplir les champs en rouge.';
			echo '</div>';  
		}
	}
	private function testForm(){
		if(
		$this->verif_null($this->mail) and 
		$this->verif_null($this->login) and 
		$this->verif_null($this->password)){
			if(
			$this->verif_mail($this->mail) and 
			$this->verif_login($this->login) and 
			$this->verif_captcha($this->captchaResult)){
				return true;
			}
			return false; 
		}
		return false; 
	}
	private function printForm(){
		echo '
<div style="padding:2px;margin:2px;" >
	<h3>Vous êtes inscrit</h3>
</div>
<div style="padding:2px;border:solid 2px #000000;background-color:#000001;width:600px;color:#ffffff;" >
	Contenu de votre inscription
</div>
<div style="padding:2px;border:solid 2px #000000;background-color:#CDE9E5;width:600px;" >
	<ul>
		<li><b>login : </b>'.$this->login.'</li>
		<li><b>Votre mail : </b>'.$this->mail.'</li>
	</ul>
</div>
<button type="button" style="width:160px;" onclick="window.location=\'./\'">Retour</button>';
	}
	private function PositionAleatoire(){
		global $nbLigneCarte, $nbColonneCarte, $nbCarteV, $nbCarteH;
		$Cartes = array('a','b','c','d','e','f','g','h','i','j','k','l','n','o','p','q','r','s','t','u','v','w','x','y');
		//ATTENTION la carte M est retirée car c'est la carte du camp romain
		$numL = mt_rand(0,$nbLigneCarte);
		$numC = mt_rand(0,$nbColonneCarte);
		//$carteV = mt_rand(0, $nbCarteV);
		//$carteH = mt_rand(0, $nbCarteH);
		return implode(',', array($Cartes[array_rand($Cartes)], $numL, $numC));
	}
}
?>