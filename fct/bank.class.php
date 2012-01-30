<?php
class bank extends batiment{
	private $Contenu;
	
	//--- fonction qui est lancer lors de la cr�ation de l'objet. ---
	public function __construct(array $carte, array $batiment){
		$this->Hydrate($carte, $batiment);
	}
	public function Hydrate(array $carte, array $batiment){
		date_default_timezone_set('Europe/Brussels');
		
		parent::Hydrate($carte, $batiment);
		
		foreach ($carte as $key => $value){
			switch ($key){
				case 'contenu_batiment':
					if(is_null($value)){
						$this->Contenu = NULL;
					}else{
						$this->Contenu = $value;
					}
					break;
			}
		}
	}
	
	//--- on retir de l'argent de la banque ---
	public function RetraitBank($montant){
		$this->Contenu -= $montant;
	}
	//--- on d�pose de l'argent en banque ---
	public function DepotBank($montant){
		$this->Contenu += $montant;
	}
	
	//Les Affichages
	//==============
	public function AfficheContenu(&$PageVillage, personnage &$oJoueur){
		
		$PositionBatiment	= implode(',', array_merge(array(parent::GetCarte()), parent::GetCoordonnee()));
		$PositionJoueur		= implode(',', array_merge(array($oJoueur->GetCarte()), $oJoueur->GetPosition()));

		return '
		<form action="./fct/main.php" method="post">
			Montant du <b>d�pot</b>: <input type="text" name="depot" value="'.$oJoueur->GetArgent().'" />
			<input type="submit" value="Ex�cuter"'.($PositionBatiment != $PositionJoueur?' disabled="disabled"':'').' />
		</form>
		<form action="./fct/main.php" method="post">
			Montant du <b>retrait</b>: <input type="text" name="retrait" value="'.(is_null($this->GetContenu())?'0':$this->GetContenu()).'" />
			<input type="submit" value="Ex�cuter"'.((is_null($this->GetContenu()) OR $PositionBatiment != $PositionJoueur)?'disabled="disabled"':'').' />
		</form>';
	}

	//Les GETS
	//========
	public function GetContenu(){				return $this->Contenu;}
}
?>