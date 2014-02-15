<?php
class InscriptionStepB{
	private	$Village,
			$CarteVillage,
			$Carriere,
			$NewVillage,
			$SendCheck,
			$Message;
	
	const NB_MAX_VILLAGEOIS			= 5;
	const SIZE_MAX_VILLAGE			= 20;
	
	const STYLE_BLANC				= ' style = "background-color: #ffffff" ';
	const STYLE_ROUGE				= ' style = "background-color: #ff0000" ';
		
	public function __construct(){
		$this->SendCheck = false;
		$this->Message = NULL;
		$this->CarteVillage = NULL;
	}
	
	//Les CHECKS
	//==========
	private function CheckIfNull($var){
		return (!empty($var))?true:false;
	}
	public function CheckVillage(){
		if($this->Village == 'VillageNew'){
			$this->CarteVillage = NULL;
			return false;
		}else{
			$this->CarteVillage = $this->GetCarteVillage($this->Village);
		}
		
		return true;
	}
	public function CheckNewVillage(){
		Global $oDB;
		
		if(strlen($this->NewVillage) > self::SIZE_MAX_VILLAGE){
			$this->Message .= '<li>Nom de village trop long (max '.self::SIZE_MAX_VILLAGE.' caractères)</li>';
			return false;
		}elseif(empty($this->NewVillage) or is_null($this->NewVillage)){
			$this->Message .= '<li>Veuillez introduire un nom de village</li>';
			return false;
		}else{
			// On vérifie si ce login existe
			$sql = "SELECT id FROM table_joueurs WHERE village='".$this->NewVillage."'";
			if($oDB->NbLigne($sql) != 0){
				$this->Message .= '<li>Le village "'.$this->NewVillage.'" existe déjà.</li>';
				return false;
			}
		}
		
		
		$this->Village = $this->NewVillage;
		return true;
	}
	public function CheckCarriere(){
		return true;
	}
	private function envoi_sql(){ //fonction qui envoie la requete SQL
		//require('./fct/config.php'); // On réclame le fichier
		Global $oDB;
		
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
					'".sha1($_SESSION['inscription']['password'])."', 
					'".$_SESSION['inscription']['mail']."', 
					'".date('Y-m-d H:i:s')."', 
					'".$_SESSION['inscription']['civilisation']."', 
					'".$this->Village."', 
					'".$this->PositionAleatoire($_SESSION['inscription']['civilisation'])."', 
					'".$this->Carriere."', 
					'".date('Y-m-d H:i:s')."', 
					'".date('Y-m-d H:i:s')."');";
		$oDB->Query($sql);
		
		$sql = "INSERT INTO table_villages (
					`villages_nom`, 
					`villages_civilisation`, 
					`villages_citoyen`)
				VALUES (
					'".$this->Village."', 
					'".$_SESSION['inscription']['civilisation']."', 
					'".$_SESSION['inscription']['login']."');";
		$oDB->Query($sql);
		
		
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
				<li><b>Votre Carrière : </b>'.GetInfoCarriere($this->Carriere, 'carriere_nom').'</li>
			</ul>
		</div>
		<button type="button" style="width:160px;" onclick="window.location=\'./\'">Retour</button>';
		
		unset($_SESSION['inscription']);
	}
	private function PositionAleatoire($civilisation){
		if(is_null($this->CarteVillage)){
			switch($civilisation){
				case personnage::CIVILISATION_GAULOIS:
					$Cartes = array('a','b','c','d','e','f','g','h','i','j','m','n','o');
					break;
				case personnage::CIVILISATION_ROMAIN:
					$Cartes = array('k','l','p','q','r','s','t','u','v','w','x','y');
					break;
			}
		}else{
			$Cartes = array($this->CarteVillage);
		}
		
		$arCaseLibre = FreeCaseCarte($Cartes[array_rand($Cartes)]);
		
		return $arCaseLibre[array_rand($arCaseLibre)];
	}
	
	//Les GETS
	//========
	public function GetSendCheck(){			return $this->SendCheck;}
	public function GetMessage(){			return $this->Message;}
	private function GetInfoVillage($strVillage){
		Global $oDB;
		Global $objManager;
		
		$sql = "SELECT villages_citoyen FROM table_villages WHERE villages_civilisation='".$_SESSION['inscription']['civilisation']."' AND villages_nom='".$strVillage."';";
		$rqtVillage = $oDB->Query($sql);
		
		$nbVillageois = 0;
		$txtListVillageois = '<ul class="liste_villageois">';
		while($row = mysql_fetch_array($rqtVillage, MYSQL_ASSOC)){
			
			$oVillageois = $objManager->GetPersoLogin($row['villages_citoyen']);
			
			$txtListVillageois .= '<li>'.$row['villages_citoyen'].' ('.GetInfoCarriere($oVillageois->GetCodeCarriere(), 'carriere_nom').').</li>';
			
			$nbVillageois++;
			
			if($nbVillageois > self::NB_MAX_VILLAGEOIS){break;}
		}
		$txtListVillageois .= '</ul>';
		
		$txt  = '<h2 onmouseover="montre(\''.CorrectDataInfoBulle('<p>Voici une partie des villageois :</p>'.$txtListVillageois).'\');" onmouseout="cache();">'.ucfirst($strVillage).'</h2>';
		//$txt .= '<p>Voici une partie des villageois :</p>';
		//$txt .= $txtListVillageois;
		//$txt .= '';
		
		return $txt;
	}
	public function GetListeVillages(){
		Global $oDB;
		
		$sql = "SELECT villages_nom FROM table_villages WHERE villages_civilisation='".$_SESSION['inscription']['civilisation']."' ORDER BY villages_nom ASC;";
		$rqtVillage = $oDB->Query($sql);
		
		//Si le village n'existe pas
		if(mysql_num_rows($rqtVillage) <= 0){
			return '<tr><td colspan="2">Aucun Village n\'existe</td></tr>';
		}
		
		//Si le village existe, on liste les villageois
		$nbVillageois = 0;
		$precedent = NULL;
		$info = NULL;
		
		while($row = mysql_fetch_array($rqtVillage, MYSQL_ASSOC)){
			if($row['villages_nom'] != $precedent){
				$info[] = '<tr>
							<td>
								<input required="required" type="radio" name="village" value="'.strtolower($row['villages_nom']).'" />
							</td>
							<td>'
								.$this->GetInfoVillage($row['villages_nom'])
							.'</td>
						</tr>';
				$precedent = $row['villages_nom'];
			}
			
		}
		if(!is_null($info)){
			return $info;
		}
	}
	public function GetListeCarrieres(){
		Global $oDB;
		$sql = "SELECT carriere_nom, carriere_debouchees, carriere_code FROM table_carrieres_lst WHERE carriere_niveau=0 AND carriere_civilisation='".$_SESSION['inscription']['civilisation']."' ORDER BY carriere_nom ASC;";
		$rqtMetier = $oDB->Query($sql);
		
		//Si aucune carrière n'est disponible
		if(mysql_num_rows($rqtMetier) <= 0){
			return '<tr><td colspan="2">Aucune carrière disponnible.</td></tr>';
		}
		
		//si la carrière est disponible, on liste les débouchées.
		$info = null;
		while($row = mysql_fetch_array($rqtMetier, MYSQL_ASSOC)){
			$temp  = '<tr>
						<td>
							<input required="required" type="radio" name="carriere" value="'.strtolower($row['carriere_code']).'" />
						</td>
						<td>'
							.$this->GetDeboucheeCarrire($row['carriere_code'])
						.'</td>
					</tr>';
			
			$info[] = $temp;
		}
		if(!is_null($info)){
			return $info;
		}
	}
	private function GetDeboucheeCarrire($CarriereCode){
		$txt = '<ul>';
		
		foreach(explode(',', GetInfoCarriere($CarriereCode, 'carriere_debouchees')) as $metier_a){
				
			$txt .= '<li>'.ucfirst(GetInfoCarriere($metier_a, 'carriere_nom')).'</li>';
			$txt .= '<ul>';
				
			foreach(explode(',', GetInfoCarriere($metier_a, 'carriere_debouchees')) as $metier_b){
		
				$txt .= '<li>'.ucfirst(GetInfoCarriere($metier_b, 'carriere_nom')).'</li>';
			}
			$txt .= '</ul>';
		}
		
		$txt .= '</ul>';
		
		return '<h2 onmouseover="montre(\''.CorrectDataInfoBulle($txt).'\');" onmouseout="cache();">'.ucfirst(GetInfoCarriere($CarriereCode, 'carriere_nom')).'</h2>';
	}
	private function GetCarteVillage($Village){
		Global $oDB;
		
		$sql = "SELECT maison_installe FROM table_joueurs WHERE civilisation='".$_SESSION['inscription']['civilisation']."' AND village='".$Village."';";
		$rqt = $oDB->Query($sql);
		
		while($row = mysql_fetch_array($rqt, MYSQL_ASSOC)){
			if(!is_null($row['maison_installe'])){
				$temp = explode(',', $row['maison_installe']);
				if(!is_null($temp[0])){
					return $temp[0];
				}
			}
		}
		
		return null;
		
	}
}
?>