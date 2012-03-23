<?php
class InscriptionStepB{
	private	$Village,
			$Carriere,
			$NewVillage,
			$SendCheck,
			$Message;
	
	const NB_MAX_VILLAGEOIS			= 5;
	
	const STYLE_BLANC				= ' style = "background-color: #ffffff" ';
	const STYLE_ROUGE				= ' style = "background-color: #ff0000" ';
		
	public function __construct(){
		$this->SendCheck = false;
		$this->Message = NULL;
	}
	
	//Les CHECKS
	//==========
	private function CheckIfNull($var){
		return (!empty($var))?true:false;
	}
		public function CheckVillage(){
		if($this->Village == 'VillageNew'){
			return false;
		}
		return true;
	}
	public function CheckNewVillage(){
		$sql = "SELECT id FROM table_joueurs WHERE village='".$this->NewVillage."'";
		// On vérifie si ce login existe
		$requete = mysql_query($sql) or die ( mysql_error() );
	
		if(mysql_num_rows($requete) != 0){
			$this->Message .= '<li>Le village "'.$this->NewVillage.'" existe déjà.</li>';
			return false;
		}
		
		$this->Village = $this->NewVillage;
		return true;
	}
	public function CheckCarriere(){
		return true;
	}
	private function envoi_sql(){ //fonction qui envoie la requete SQL
		require('./fct/config.php'); // On réclame le fichier
		$sql = 	"INSERT INTO table_joueurs (
				`id`, 
				`login`, 
				`password`, 
				`mail`, 
				`dates`, 
				`civilisation`, 
				`village`,
				`position`, 
				`carriere`, 
				`last_action`, 
				`date_last_combat`) 
			VALUES (
				NULL, 
				'".$_SESSION['inscription']['login']."', 
				'".$_SESSION['inscription']['password']."', 
				'".$_SESSION['inscription']['mail']."', 
				'".date('Y-m-d H:i:s')."', 
				'".$_SESSION['inscription']['civilisation']."', 
				'".$this->Village."', 
				'".$this->PositionAleatoire($_SESSION['inscription']['civilisation'])."', 
				'".$this->Carriere."', 
				'".date('Y-m-d H:i:s')."', 
				'".date('Y-m-d H:i:s')."');";
		mysql_query($sql) or die ( mysql_error().'<br />'.$sql);
		//echo $sql;
		
	}
	
	public function loadForm($data){
		extract($data);
		$this->Village		= $village;
		$this->NewVillage	= htmlspecialchars($VillageNew, ENT_QUOTES);
		$this->Carriere		= $carriere;
		
		if($this->testForm()){
			$this->envoi_sql();
			$this->printForm();
			$this->SendCheck = true;
			
		}else{
			 $temp = '<div style="padding:5px;border:solid 2px #FF0000;background-color:#FEDFDF;width:600px;color:#ff0000;" >'
				.'<p>Veuillez remplir correctement le formulaire.<p>'
				.'<ul>'
				.$this->Message
				.'</ul>'
				.'</div>';
			 $this->Message = $temp;
		}
	}
	private function testForm(){
		if(
		($this->CheckIfNull($this->Village) OR $this->CheckIfNull($this->NewVillage))
		and $this->CheckIfNull($this->Carriere)){
			if(
			$this->CheckVillage() XOR $this->CheckNewVillage()
			and $this->CheckCarriere()){
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
				<li><b>Votre login : </b>'.$_SESSION['inscription']['login'].'</li>
				<li><b>Votre mail : </b>'.$_SESSION['inscription']['mail'].'</li>
				<li><b>Votre Village : </b>'.$this->Village.'</li>
				<li><b>Votre Carrière : </b>'.$this->Carriere.'</li>
			</ul>
		</div>
		<button type="button" style="width:160px;" onclick="window.location=\'./\'">Retour</button>';
	}
	private function PositionAleatoire($civilisation){
		global $nbLigneCarte, $nbColonneCarte, $nbCarteV, $nbCarteH;
		switch($civilisation){
			case InscriptionStepA::CIVI_GAULOIS:
				$Cartes = array('a','b','c','d','e','f','g','h','i','j','m','n','o');
				break;
			case InscriptionStepA::CIVI_ROMAINS:
				$Cartes = array('k','l','p','q','r','s','t','u','v','w','x','y');
				break;
		}
		//$Cartes = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y');

		$numL = mt_rand(0,$nbLigneCarte);
		$numC = mt_rand(0,$nbColonneCarte);
		//$carteV = mt_rand(0, $nbCarteV);
		//$carteH = mt_rand(0, $nbCarteH);
		return implode(',', array($Cartes[array_rand($Cartes)], $numL, $numC));
	}
	
	//Les GETS
	//========
	public function GetSendCheck(){			return $this->SendCheck;}
	public function GetMessage(){			return $this->Message;}
	private function GetInfoVillage($strVillage){
		$sql = "SELECT login, niveau, carriere FROM table_joueurs WHERE civilisation='".$_SESSION['inscription']['civilisation']."' AND village='".$strVillage."';";
		$rqtVillage = mysql_query($sql) or die ( mysql_error() );
		
		$nbVillageois = 0;
		$txtListVillageois = '<ul class="liste_villageois">';
		while($row = mysql_fetch_array($rqtVillage, MYSQL_ASSOC)){
			$txtListVillageois .= '<li>'.$row['login'].' ('.$row['carriere'].').</li>';
			$nbVillageois++;
			if($nbVillageois > self::NB_MAX_VILLAGEOIS){break;}
		}
		$txtListVillageois .= '</ul>';
		
		$txt  = '<h2>'.ucfirst($strVillage).'</h2>';
		$txt .= '<p>Voici une partie des villageois :</p>';
		$txt .= $txtListVillageois;
		$txt .= '';
		
		return $txt;
	}
	public function GetListeVillages(){
		$sql = "SELECT village FROM table_joueurs WHERE civilisation='".$_SESSION['inscription']['civilisation']."' ORDER BY village ASC;";
		$rqtVillage = mysql_query($sql) or die ( mysql_error() );
		
		if(mysql_num_rows($rqtVillage) > 0){
			$nbVillageois = 0;
			$precedent = NULL;
			$info = NULL;
			
			while($row = mysql_fetch_array($rqtVillage, MYSQL_ASSOC)){
				if($row['village'] != $precedent){
					$info[] = '<tr>
								<td>
									<input required="required" type="radio" name="village" value="'.strtolower($row['village']).'" />
								</td>
								<td>'
									.$this->GetInfoVillage($row['village'])
								.'</td>
							</tr>';
					$precedent = $row['village'];
				}
				
			}
			if(!is_null($info)){
				return $info;
			}
		}
		
		return '<tr><td colspan="2">Aucun Village n\'existe</td></tr>';
		
	}
	public function GetListeCarrieres(){
		$sql = "SELECT carriere_nom, carriere_debouchees FROM table_carrieres_lst WHERE carriere_niveau=0 ORDER BY carriere_nom ASC;";
		$rqtMetier = mysql_query($sql) or die ( mysql_error() );
		
		if(mysql_num_rows($rqtMetier) > 0){
			$info = null;
			while($row = mysql_fetch_array($rqtMetier, MYSQL_ASSOC)){
				$temp  = '<tr>
							<td>
								<input required="required" type="radio" name="carriere" value="'.strtolower($row['carriere_nom']).'" />
							</td>
							<td>'
								.'<h2>'.ucfirst($row['carriere_nom']).'</h2>';
				
				$arDebouchees_a = explode(',', $row['carriere_debouchees']);
				$temp .= '<ul>';
				//for($i = 0; $i <= self::PRESENTATION_NIV_MAX; $i++){
					//$arCompetence = explode(',', $lstDebouchees);
				foreach($arDebouchees_a as $metier_a){
					$arInfoMetier_a = $this->GetInfoMetier($metier_a);
					$temp .= '<li>'.ucfirst($arInfoMetier_a['carriere_nom']).'</li>';
					$temp .= '<ul>';
					$arDebouchees_b = explode(',', $arInfoMetier_a['carriere_debouchees']);
					foreach($arDebouchees_b as $metier_b){
						$arInfoMetier_b = $this->GetInfoMetier($metier_b);
						$temp .= '<li>'.$arInfoMetier_b['carriere_nom'].'</li>';
					}
					$temp .= '</ul>';
				}
				
				$temp .= '</ul>';
				
				$temp .= '</td></tr>';
				
				$info[] = $temp;
			}
			if(!is_null($info)){
				return $info;
			}
		}
		
		return '<tr><td colspan="2">Aucune carrière disponnible.</td></tr>';
	}
	private function GetInfoMetier($code){
		$sql = "SELECT * FROM table_carrieres_lst WHERE carriere_code='".$code."';";
		$rqtMetier = mysql_query($sql) or die ( mysql_error() );
		
		if(mysql_num_rows($rqtMetier) > 0){
			return mysql_fetch_array($rqtMetier, MYSQL_ASSOC);
		}else{
			return null;
		}
	}
}
?>