<?php
class DebatDeClan{
	
	Private $nbReccord;
	Private $arObjReccords;
	
	const COLOR_LIGNE		= '#C0C0C0';
	
	public function __construct($clan){
		Global $oDB;
		
		$sql = "SELECT * FROM table_chat WHERE clan_chat='".htmlspecialchars($clan, ENT_QUOTES)."' ORDER BY date_chat DESC;";
		$requete = $oDB->Query($sql);
		
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
					<input type="hidden" name="action" value="chataddreccord" />
					<table>
						<tr><td rowspan="3">'
							.str_ireplace('="Attaque"', '="code = '.ReccordChat::CODE_IC_ATTAQUE.'"', ReccordChat::ICONE_ATTAQUE)
							.str_ireplace('="Défense"', '="code = '.ReccordChat::CODE_IC_DEFENSE.'"', ReccordChat::ICONE_DEFENSE)
							.str_ireplace('="Distance"', '="code = '.ReccordChat::CODE_IC_DISTANCE.'"', ReccordChat::ICONE_DISTANCE)
							.str_ireplace('="Ressource Nourriture"', '="code = '.ReccordChat::CODE_IC_NOURRITURE.'"', ReccordChat::ICONE_NOURRITURE)
							.str_ireplace('="Ressource Pierre"', '="code = '.ReccordChat::CODE_IC_PIERRE.'"', ReccordChat::ICONE_PIERRE)
							.str_ireplace('="Ressource Bois"', '="code = '.ReccordChat::CODE_IC_BOIS.'"', ReccordChat::ICONE_BOIS)
							.str_ireplace('="Ressource OR"', '="code = '.ReccordChat::CODE_IC_OR.'"', ReccordChat::ICONE_OR)
							.str_ireplace('="clin d\'oeil"', '="code = '.ReccordChat::CODE_EMO_WINK.'"', ReccordChat::EMOTICON_WINK)
							.str_ireplace('="content"', '="code = '.ReccordChat::CODE_EMO_HAPPY.'"', ReccordChat::EMOTICON_HAPPY)
							.str_ireplace('="pas content"', '="code = '.ReccordChat::CODE_EMO_UNHAPPY.'"', ReccordChat::EMOTICON_UNHAPPY)
						.'</td></tr>
						<tr><td>
							<textarea style="width:700px; max-width:700px; height:100px; max-height:100px;" name="text" required="required" placeholder="Introduisez votre texte ici"></textarea>
						</td></tr>
						<tr><td style="text-align:center;"><input type="submit" value="Envoyer" /></td></tr>
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
						<input type="hidden" name="action" value="chatremovereccord" />
						<button type="submit">'.AfficheIcone('Trash').'</button>
					</form>
				</th>';
	}
	//Les GETS
	//========
	
}

?>