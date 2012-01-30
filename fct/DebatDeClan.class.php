<?php
class DebatDeClan{
	
	Private $nbReccord;
	Private $arObjReccords;
	
	const COLOR_LIGNE		= '#C0C0C0';
	
	public function __construct($clan){
		$sql = "SELECT * FROM table_chat WHERE clan_chat='".htmlspecialchars($clan, ENT_QUOTES)."' ORDER BY date_chat DESC;";
		$requete = mysql_query($sql) or die (mysql_error());
		
		if(mysql_num_rows($requete)>0){
			while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){
				$this->arObjReccords[] = new ReccordChat($row);
			}
		}
	}
	
	//Les Affichages
	//==============
	Private function AfficheModuleAjout(personnage &$Joueur){
		return '<form method="post">
					<input type="hidden" name="clan" value="'.$Joueur->GetClan().'" />
					<input type="hidden" name="member" value="'.$Joueur->GetLogin().'" />
					<input type="hidden" name="chat" value="addreccord" />
					<table>
						<tr><td>
							<textarea style="width:750px; max-width:750px; height:100px; max-height:100px;" name="text" required="required" placeholder="Introduisez votre texte ici"></textarea>
						</td></tr>
						<tr><td style="text-align:center;"><input type="submit" value="Valider" /></td></tr>
					</table>
				</form>';
	}
	Public Function AfficheDebat(personnage &$Joueur){
		if(count($this->arObjReccords) == 0){
			$LstReccords = '<tr'.AfficheLigneCouleur(self::COLOR_LIGNE, 0).'><th>Aucune Conversation</th></tr>';
		}else{
			$LstReccords = NULL;
			$nbLigne = 0;
			Foreach($this->arObjReccords as $id=>$Reccord){
				$LstReccords .= '<tr'.AfficheLigneCouleur(self::COLOR_LIGNE, $nbLigne).'><th style="width:250px;">'.$Reccord->GetDate().'</th><td rowspan="'.($Reccord->GetAuteur() == $Joueur->GetLogin()?'3':'2').'">'.$Reccord->GetText().'</td></tr>';
				$LstReccords .= '<tr'.AfficheLigneCouleur(self::COLOR_LIGNE, $nbLigne).'><th>'.$Reccord->GetAuteur().'</th></tr>';
				$LstReccords .= ($Reccord->GetAuteur() == $Joueur->GetLogin()?'<tr'.AfficheLigneCouleur(self::COLOR_LIGNE, $nbLigne).'>'.$this->AfficheOption($id).'</tr>':NULL);
				$nbLigne++;
			}
		}
		
		return '<table class="chat_clan">'
					.'<tr><td colspan="3">'.$this->AfficheModuleAjout($Joueur).'</td></tr>'
					.$LstReccords
				.'</table>';
	}
	Private function AfficheOption($id){
		$_SESSION['chat'][$id] = $this->arObjReccords[$id]->GetID();
		
		return '<th>
					<form method="post">
						<input type="hidden" name="id" value="'.$id.'" />
						<input type="hidden" name="chat" value="remove" />
						<button type="submit">'.AfficheIcone('trash').'</button>
					</form>
				</th>';
	}
	//Les GETS
	//========
	
}

?>