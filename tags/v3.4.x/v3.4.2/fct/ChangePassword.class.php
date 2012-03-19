<?php
class ChangePassword{
	public $old_password;
	public $password_1;
	public $password_2;
	public $ChangeCheck = null;
	
	private function verif_null($var){		return (!empty($var))?$var:null;}
	
    private function verif_new_password($pass1,$pass2){return($pass1==$pass2)?$pass1:null;}
	
	private function verif_old_password($old){
		require('config.php'); // On réclame le fichier
		$sql = "SELECT password FROM table_joueurs WHERE login='".$_SESSION['joueur']."'";
		$requete = mysql_query($sql) or die ( mysql_error() );
		$result = mysql_fetch_array($requete, MYSQL_ASSOC);
		if($result['password'] == $old){return $old;}
		else{return null;}
	}
	
	public function inputTrue($input, $type = '1'){
		$style_blanc = ' style = "border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ffffff" ';
		$style_rouge = ' style = "border: solid #000000 1px;font-size: 8pt;color: #000000;background-color: #ff0000" '; 
		$test = null;
		if(isset($_POST['chg_pass'])){
			switch($type){
				case '1': $test = $this->verif_null($input);break;
				case '2': $test = $this->verif_mail($input);break;
			}
			if(empty($test)){
				return $style_rouge;
			}else{
				return $style_blanc;
			}
		}
	}
	function envoi_sql(){ //fonction qui envoie la requete SQL
		require('config.php'); // On réclame le fichier
		$sql = "UPDATE table_joueurs SET password='".$this->password_1."' WHERE login='".$_SESSION['joueur']."';";
		mysql_query($sql) or die ( mysql_error() );
	}
	public function loadForm($data){
		extract($data);
		$this->old_password	= $old_password;
		$this->password_1	= $password_1;
		$this->password_2	= $password_2;
		$test = $this->testForm();
		if(!empty($test)){
			$this->envoi_sql();
			$this->ChangeCheck = 1;
			echo '<p>Password changé.</p>';
		}else{
			echo '<p>Erreur. Veuillez vérifier.</p>';  
		}
	}
	public function testForm(){
		if(
		$this->verif_null($this->old_password) and 
		$this->verif_null($this->password_1) and 
		$this->verif_null($this->password_2)){
			if(
			$this->verif_new_password($this->password_1, $this->password_2) and
			$this->verif_old_password($this->old_password)){
				return 1;
			}
			return NULL; 
		}
		return NULL; 
	}
}
?>