<?php
class Legionnaire{
	
	Private $Attaque;
	Private $Defense;
	Private $ValeurCombat;
	
	Protected $DB;
	
	//Const ;
	
	public function __construct($Niveau){
		$this->Attaque = (10 * $Niveau) + mt_rand(1,15);
		$this->Defense = (10 * $Niveau) + mt_rand(1,15);
		
		$this->ValeurCombat = intval($this->Attaque * 1.35) + $this->Defense;
		
		$this->DB = new DBManage();
	}
	public function CombatLegionnaire(personnage &$Joueur){
		global $lstPoints;
		
		$arAttaqueJoueur = $Joueur->GetAttPerso();
		$arDefenseJoueur = $Joueur->GetDefPerso();
		$ValeurJoueur = intval(($arAttaqueJoueur[0] + $arAttaqueJoueur[1]) * 1.35) + ($arDefenseJoueur[0] + $arDefenseJoueur[1]);
		
		if($ValeurJoueur > $this->ValeurCombat){		//Le joueur gagne le combat contre le L�gionnaire
			$Joueur->UpdatePoints(abs(personnage::POINT_COMBAT));
			$Joueur->UpdateScores(1, 0);
			$txtMessage = '<p>Vous avez gagn� le combat contre le l�gionnaire romain. Vous avez gagn� '.abs(personnage::POINT_COMBAT).'pts et 5pts d\'exp�rience.</p>';
		}elseif($ValeurJoueur == $this->ValeurCombat){	//Le L�gionnaire et le joueur ont la meme valeur de combat
			$txtMessage = '<p>M�me valeur de combat. Vous n\'avez gagn� que 5pts d\'exp�rience.</p>';
		}else{											//Le L�gionnaire gagne le combat contre le joueur		
			$Joueur->UpdatePoints((abs(personnage::POINT_COMBAT) * -1));
			$Joueur->UpdateScores(0, 1);
			$Joueur->PerdreVie(($this->ValeurCombat - $arDefenseJoueur[0] - $arDefenseJoueur[1]), 'legionnaire');
			$txtMessage = '<p>Vous avez perdu le combat contre le l�gionnaire romain. Vous avez donc perdu '.abs(personnage::POINT_COMBAT).'pts et '.($this->ValeurCombat - $arDefenseJoueur[0] - $arDefenseJoueur[1]).'pts '.AfficheIcone('vie').' mais quand m�me gagn� 5 pts d\'exp�rience.</p>';
		}
		$Joueur->AddExperience(5);
		
		$this->DB->InsertHistory($Joueur->GetLogin(), $Joueur->GetCarte(), $Joueur->GetPosition(), 'combat', 'L�gionnaire', NULL, $txtMessage);
		
		return $txtMessage;
	}
	// Les Get
	public function GetValeurCombat(){	return $this->ValeurCombat;}
}
?>