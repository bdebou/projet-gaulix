<?php
class ChangeEmail{
	public $email;
	public $ChangeCheck = null;
	
	public function verif_null($var){		return (!empty($var))?$var:null;}
	public function verif_mail($var){		return (preg_match('#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#',$var))?$var:null;}
	
	public function inputTrue($input){
		$style_blanc = ' style = "border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ffffff" ';
		$style_rouge = ' style = "border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ff0000" '; 
		$test = null;
		if(isset($_POST['chg_email'])){
			$test = $this->verif_null($input);
			$test = $this->verif_mail($input);
			if(empty($test)){
				return $style_rouge;
			}else{
				return $style_blanc;
			}
		}
	}
	function envoi_sql(){ //fonction qui envoie la requete SQL
		//require('config.php'); // On réclame le fichier
		global $oDB;
		
		$sql = "UPDATE table_joueurs SET mail='".$this->email."' WHERE login='".$_SESSION['joueur']."';";
		$oDB->Query($sql);
	}
	public function loadForm($data){
		extract($data);
		$this->email	= $email;
		$test = $this->testForm();
		if(!empty($test)){
			$this->envoi_sql();
			$this->ChangeCheck = 1;
			echo '<p>E-mail changé.</p>';
		}else{echo '<p>Erreur. Veuillez vérifier.</p>';  }
	}
	public function testForm(){
		if($this->verif_null($this->email)){
			if($this->verif_mail($this->email)){return 1;}
			return NULL; 
		}
		return NULL; 
	}
}
?>