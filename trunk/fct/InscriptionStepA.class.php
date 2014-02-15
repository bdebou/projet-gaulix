<?php
class InscriptionStepA{
	public	$Login,
			$PasswordA,
			$PasswordB,
			$Mail,
			$CaptchaResult,
			$SendCheck,
			$Message,
			$Civilisation;
	
	const SIZE_LOGIN_MIN	= 6;
	const SIZE_LOGIN_MAX	= 20;
	const SIZE_PASS_MIN		= 6;
	const SIZE_PASS_MAX		= 20;
	
	const STYLE_BLANC		= ' style = "background-color: #ffffff" ';
	const STYLE_ROUGE		= ' style = "background-color: #ff0000" ';
		
	public function __construct(){
		$this->SendCheck = false;
		$this->Message = NULL;
	}
	
	private function CheckIfNull($var){
		return (!empty($var))?true:false;
	}
    private function CheckEmail(){
    	if(isset($_POST['next'])){
    		if(preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#',$this->Mail)){
    			return true;
    		}else{
    			$this->Mail = NULL;
    			$this->Message .= '<li>Email invalide</li>';
    			return false;
    		}
    	}else{
			return false;
		}
	}
	private function CheckCaptcha(){
		if(isset($_POST['next'])){
			if($this->CaptchaResult == $_SESSION['captchaResult']){
				return true;
			}else{
				$this->CaptchaResult = NULL;
    			$this->Message .= '<li>Code de vérification invalide</li>';
				return false;
			}
		}else{
			return false;
		}
	}
	private function CheckPassword(){
		if(isset($_POST['next'])){
			if(empty($this->PasswordA) OR empty($this->PasswordB)){
	
				$this->PasswordA = NULL;
				$this->PasswordB = NULL;
    			$this->Message .= '<li>Un champ Password vide</li>';
				return false;
			}elseif($this->PasswordA != $this->PasswordB){
				
				$this->PasswordA = NULL;
				$this->PasswordB = NULL;
    			$this->Message .= '<li>Les 2 passwords introduits ne sont pas identique</li>';
				return false;
				
			}elseif(strlen($this->PasswordA) < self::SIZE_PASS_MIN){
	
				$this->PasswordA = NULL;
				$this->PasswordB = NULL;
    			$this->Message .= '<li>Password trop court (min '.self::SIZE_PASS_MIN.' caractères)</li>';
				return false;
	
			}elseif(strlen($this->PasswordA) > self::SIZE_PASS_MAX){
	
				$this->PasswordA = NULL;
				$this->PasswordB = NULL;
    			$this->Message .= '<li>Password trop long (max '.self::SIZE_PASS_MAX.' caractères)</li>';
				return false;
	
			}else{
				return true;
				
			}
		}else{
			return false;
		}
	}
	private function CheckLogin(){
			// On réclame le fichier
		require_once('./fct/config.php');
		
		$oDB=DBManage::Cast($oDB);
		
		if(isset($_POST['next'])){
			if(empty($this->Login)){
				
				$this->Message .= '<li>Login est vide</li>';
				return false;
				
			}elseif(in_array(strtolower($this->Login), array('romain', 'gaulois'))){
				
				$this->Login = NULL;
    			$this->Message .= '<li>Login existant</li>';
				return false;
				
			}elseif(strlen($this->Login) < self::SIZE_LOGIN_MIN){
				
				$this->Login = NULL;
    			$this->Message .= '<li>Login trop court (min '.self::SIZE_LOGIN_MIN.' caractères)</li>';
				return false;
				
			}elseif(strlen($this->Login) > self::SIZE_LOGIN_MAX){
				
				$this->Login = NULL;
    			$this->Message .= '<li>Login trop long (max '.self::SIZE_LOGIN_MAX.' caractères)</li>';
				return false;
				
			}else{
				
				$sql = "SELECT id FROM table_joueurs WHERE login='".$this->Login."'";
				// On v�rifie si ce login existe
				$requete = $oDB->Select('table_joueurs', array('id'), array("login='".$this->Login."'"));
				
				if(mysql_num_rows($requete) == 0){
					return true;
				}else{
					$this->Login = NULL;
    				$this->Message .= '<li>Login existant</li>';
					return false;
				}
			}
		}else{
			return false;
		}
	}
		
	
	public function loadForm($data){
		extract($data);
		$this->Login			= strtolower(trim(htmlentities($Login, ENT_QUOTES)));
		$this->PasswordA		= $PasswordA;
		$this->PasswordB		= $PasswordB;
		$this->Mail				= strtolower($mail);
		$this->CaptchaResult	= strtoupper($captchaResult);
		$this->Civilisation		= $Civilisation;
		
		if($this->testForm()){
			//$this->envoi_sql();
			$this->sendCheck = true;
			$_SESSION['inscription'] = array('login'=>$this->Login, 'password'=>$this->PasswordA, 'mail'=>$this->Mail, 'civilisation'=>$this->Civilisation);
			unset($_POST);
			echo '<script type="text/javascript">window.location="index.php?page=inscription_b";</script>';
		}else{
			$temp = '<div style="padding:5px;border:solid 2px #FF0000;background-color:#FEDFDF;width:600px;color:#ff0000;" >'
				.'<p>Veuillez remplir correctement le formulaire.</p>'
				.'<ul>'
				.$this->Message
				.'</ul>'
				.'</div>';
			$this->Message = $temp;
		}
	}
	private function testForm(){
		if(
		$this->CheckIfNull($this->Mail) and 
		$this->CheckIfNull($this->Login) and 
		$this->CheckIfNull($this->PasswordA) and
		$this->CheckIfNull($this->PasswordB) and
		$this->CheckIfNull($this->Civilisation)){
			if(
			$this->CheckEmail() and 
			$this->CheckLogin() and 
			$this->CheckPassword() and
			$this->CheckCaptcha()){
				return true;
			}
			return false; 
		}
		return false; 
	}
			
	//Les GETS
	//========
	public function GetLogin(){				return $this->Login;}
	public function GetPassword(){			return $this->Password;}
	public function GetMail(){				return $this->Mail;}
	public function GetCaptchaResult(){		return $this->CaptchaResult;}
	public function GetSendCheck(){			return $this->SendCheck;}
	public function GetMessage(){			return $this->Message;}
	public function GetStyleCheckLogin(){
		if(isset($_POST['envoyer'])){
			if(!$this->CheckLogin()){
				return self::STYLE_ROUGE;
			}
		}
		return self::STYLE_BLANC;
	}
	public function GetStyleCheckPassword(){
		if(isset($_POST['envoyer'])){
			if(!$this->CheckPassword()){
				return self::STYLE_ROUGE;
			}
		}
		return self::STYLE_BLANC;
	}
	public function GetStyleCheckEmail(){
		if(isset($_POST['envoyer'])){
			if(!$this->CheckEmail()){
				return self::STYLE_ROUGE;
			}
		}
		return self::STYLE_BLANC;
	}
	public function GetStyleCheckCaptcha(){
		if(isset($_POST['envoyer'])){
			if(!$this->CheckCaptcha()){
				return self::STYLE_ROUGE;
			}
		}
		return self::STYLE_BLANC;
	}
	public function GetSelectCivilisation($civilisation){
		Global $oDB;
		
		$sqlG = "SELECT id FROM table_joueurs WHERE civilisation='".personnage::CIVILISATION_GAULOIS."';";
		//$rqtG = $oDB->Select('table_joueurs', array('id'), array("civilisation='".personnage::CIVILISATION_GAULOIS."'"));
		
		$sqlR = "SELECT id FROM table_joueurs WHERE civilisation='".personnage::CIVILISATION_ROMAIN."';";
		//$rqtR =$oDB->Select('table_joueurs', array('id'), array("civilisation='".personnage::CIVILISATION_ROMAIN."'"));
		
		//if(mysql_num_rows($rqtG) >= mysql_num_rows($rqtR)){
		if($oDB->NbLigne($sqlG) >= $oDB->NbLigne($sqlR)){
			switch($civilisation){
				case personnage::CIVILISATION_GAULOIS:	return NULL;
				case personnage::CIVILISATION_ROMAIN:	return ' selected="selected"';
			}
		}else{
			switch($civilisation){
				case personnage::CIVILISATION_GAULOIS:	return ' selected="selected"';
				case personnage::CIVILISATION_ROMAIN:	return NULL;
			}
		}
	}
}
?>