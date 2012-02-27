<?php
class Legionnaire{
	
	Private $Attaque,
			$Defense,
			$ValeurCombat;
	
	//Const ;
	
	public function __construct($Niveau){
		$this->Attaque = (10 * $Niveau) + mt_rand(1,15);
		$this->Defense = (10 * $Niveau) + mt_rand(1,15);
		
		$this->ValeurCombat = intval($this->Attaque * 1.35) + $this->Defense;
	}
	public function CombatLegionnaire(personnage &$Joueur){
		global $lstPoints;
		
		$arAttaqueJoueur = $Joueur->GetAttPerso();
		$arDefenseJoueur = $Joueur->GetDefPerso();
		$ValeurJoueur = intval(($arAttaqueJoueur[0] + $arAttaqueJoueur[1]) * 1.35) + ($arDefenseJoueur[0] + $arDefenseJoueur[1]);
		
		if($ValeurJoueur > $this->ValeurCombat){		//Le joueur gagne le combat contre le Légionnaire
			$Joueur->UpdatePoints($lstPoints['CombatGagné'][0]);
			$Joueur->UpdateScores(1, 0);
			$txtMessage = '<p>Vous avez gagné le combat contre le légionnaire romain. Vous avez gagné '.abs($lstPoints['CombatGagné'][0]).'pts et 5pts d\'expérience.</p>';
		}elseif($ValeurJoueur == $this->ValeurCombat){	//Le Légionnaire et le joueur ont la meme valeur de combat
			$txtMessage = '<p>Même valeur de combat. Vous n\'avez gagné que 5pts d\'expérience.</p>';
		}else{											//Le Légionnaire gagne le combat contre le joueur		
			$Joueur->UpdatePoints($lstPoints['CombatPerdu'][0]);
			$Joueur->UpdateScores(0, 1);
			$Joueur->PerdreVie(($this->ValeurCombat - $arDefenseJoueur[0] - $arDefenseJoueur[1]), 'legionnaire');
			$txtMessage = '<p>Vous avez perdu le combat contre le légionnaire romain. Vous avez donc perdu '.abs($lstPoints['CombatPerdu'][0]).'pts et '.($this->ValeurCombat - $arDefenseJoueur[0] - $arDefenseJoueur[1]).'pts '.AfficheIcone('vie').' mais quand même gagné 5 pts d\'expérience.</p>';
		}
		$Joueur->AddExperience(5);
		
		AddHistory($Joueur->GetLogin(), $Joueur->GetCarte(), $Joueur->GetPosition(), 'combat', 'Légionnaire', NULL, $txtMessage);
		
		return $txtMessage;
	}
	// Les Get
	public function GetValeurCombat(){	return $this->ValeurCombat;}
}
?>