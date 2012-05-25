<?php
class PersonnagesManager{
	
	private $db; // Instance de PDO
        
	public function __construct($dbe){
		$this->db = $dbe;
	}
	public function count(){
		return $this->db->query('SELECT COUNT(*) FROM table_joueurs')->fetchColumn();
	}
	public function get($info){
		if (is_int($info)){
			return new personnage(
				$this->db->query('SELECT * FROM table_joueurs WHERE id = '.$info)
						->fetch(PDO::FETCH_ASSOC)
				);
		}
	}
	public function GetBatiment($Coordonnee){
		$sql = "SELECT * FROM table_carte WHERE coordonnee = '$Coordonnee' AND detruit IS NULL;";
		$requete = mysql_query($sql) or die ( mysql_error().'<br />'.$sql);
		$carte = mysql_fetch_array($requete, MYSQL_ASSOC);
		
		$sql = "SELECT * FROM table_batiment WHERE id_batiment=".$carte['id_type_batiment'].";";
		$requete = mysql_query($sql) or die ( mysql_error().'<br />'.$sql);
		$batiment = mysql_fetch_array($requete, MYSQL_ASSOC);
		
		return new $batiment['batiment_type']($carte, $batiment);
	}
	public function GetPersoLogin($login){
		return new personnage(
				$this->db->query("SELECT * FROM table_joueurs WHERE login = '".$login."'")
						->fetch(PDO::FETCH_ASSOC)
				);
	}
	
	public function update(personnage $perso){
		$q = $this->db->prepare('UPDATE table_joueurs SET 
				position = :position, 
				vie = :vie, 
				civilisation = :civilisation, 
				village = :village, 
				carriere = :carriere, 
				maison_installe = :maison_installe, 
				val_attaque = :val_attaque, 
				val_defense = :val_defense, 
				experience = :experience, 
				niveau = :niveau, 
				argent = :argent, 
				deplacement = :deplacement, 
				last_action = :last_action, 
				date_last_combat = :date_last_combat, 
				attaque_tour = :attaque_tour, 
				date_perf_attaque = :date_perf_attaque, 
				tmp_perf_attaque = :tmp_perf_attaque, 
				date_perf_defense = :date_perf_defense, 
				tmp_perf_defense = :tmp_perf_defense, 
				chk_chasse = :chk_chasse, 
				chk_object = :chk_object, 
				chk_legion = :chk_legion, 
				last_object = :last_object, 
				code_casque = :code_casque, 
				code_arme = :code_arme, 
				code_bouclier = :code_bouclier, 
				code_jambiere = :code_jambiere, 
				code_cuirasse = :code_cuirasse, 
				code_sac = :code_sac, 
				livre_sorts = :livre_sorts, 
				inventaire = :strInventaire, 
				nb_combats = :nb_combats, 
				nb_victoire = :nb_victoire, 
				nb_mort = :nb_mort,  
				clan = :clan, 
				date_last_msg_lu = :date_last_msg_lu, 
				nb_points = :nb_points, 
				not_attaque = :not_attaque, 
				not_combat = :not_combat, 
				nb_vaincu = :nb_vaincu 
				WHERE id = :id');
		
		$q->bindValue(':position', implode(',', array_merge(array($perso->GetCarte()), $perso->GetPosition())), PDO::PARAM_STR);
		$q->bindValue(':vie', $perso->GetVie(), PDO::PARAM_STR);
		$q->bindvalue(':civilisation', $perso->GetCivilisation(), PDO::PARAM_STR);
		$q->bindvalue(':village', $perso->GetVillage(), PDO::PARAM_STR);
		$q->bindvalue(':carriere', $perso->GetCodeCarriere(), PDO::PARAM_STR);
		$arAtt = $perso->GetAttPerso();
		$q->bindValue(':val_attaque', $arAtt['0'], PDO::PARAM_INT);
		$arDef = $perso->GetDefPerso();
		$q->bindValue(':val_defense', $arDef['0'], PDO::PARAM_INT);
		$q->bindValue(':experience', $perso->GetExpPerso(), PDO::PARAM_INT);
		$q->bindValue(':niveau', $perso->GetNiveau(), PDO::PARAM_INT);
		$q->bindValue(':deplacement', $perso->GetDepDispo(), PDO::PARAM_INT);
		$q->bindValue(':last_action', date('Y-m-d H:i:s',$perso->GetLastAction()), PDO::PARAM_INT);
		$q->bindValue(':date_last_combat', date('Y-m-d H:i:s',$perso->GetLastCombat()), PDO::PARAM_INT);
		$q->bindValue(':attaque_tour', ($perso->GetAttaqueTour()?'1':NULL), PDO::PARAM_STR);
		$q->bindValue(':chk_chasse', ($perso->GetChkChasse()?'1':NULL), PDO::PARAM_STR);
		$q->bindValue(':chk_object', ($perso->GetChkObject()?'1':NULL), PDO::PARAM_STR);
		$q->bindValue(':chk_legion', ($perso->GetChkLegionnaire()?'1':NULL), PDO::PARAM_STR);
		$q->bindValue(':last_object', (is_null($perso->GetLastObject())?NULL:$perso->GetLastObject()), PDO::PARAM_INT);
		$q->bindValue(':code_casque', ($perso->GetCasque()?$perso->GetCasque():NULL), PDO::PARAM_INT);
		$q->bindValue(':code_arme', ($perso->GetArme()?$perso->GetArme():NULL), PDO::PARAM_STR);
		$q->bindValue(':code_bouclier', ($perso->GetBouclier()?$perso->GetBouclier():NULL), PDO::PARAM_STR);
		$q->bindValue(':code_jambiere', ($perso->GetJambiere()?$perso->GetJambiere():NULL), PDO::PARAM_STR);
		$q->bindValue(':code_cuirasse', ($perso->GetCuirasse()?$perso->GetCuirasse():NULL), PDO::PARAM_STR);
		$q->bindValue(':code_sac', ($perso->GetSac()?$perso->GetSac():NULL), PDO::PARAM_STR);
		$q->bindValue(':livre_sorts', implode(',', array_merge(array($perso->GetLivre()), (is_null($perso->GetLstSorts())?array():$perso->GetLstSorts()))), PDO::PARAM_STR);
		$q->bindValue(':nb_combats', $perso->GetNbCombats(), PDO::PARAM_INT);
		$q->bindValue(':nb_victoire', $perso->GetNbVictoire(), PDO::PARAM_INT);
		$q->bindValue(':nb_vaincu', $perso->GetNbVaincu(), PDO::PARAM_INT);
		$q->bindValue(':nb_mort', $perso->GetNbMort(), PDO::PARAM_INT);
		$q->bindValue(':strInventaire', (is_null($perso->GetLstInventaire())?NULL:implode(',', $perso->GetLstInventaire())), PDO::PARAM_STR);
		$q->bindValue(':id', $perso->GetId(), PDO::PARAM_INT);
		$q->bindValue(':argent', $perso->GetArgent(), PDO::PARAM_INT);
		$q->bindValue(':date_perf_attaque', ($perso->GetDatePerfAttaque()?date('Y-m-d H:i:s',$perso->GetDatePerfAttaque()):NULL), PDO::PARAM_INT);
		$q->bindValue(':tmp_perf_attaque', ($perso->GetTmpPerfAttaque()?$perso->GetTmpPerfAttaque():NULL), PDO::PARAM_INT);
		$q->bindValue(':date_perf_defense', ($perso->GetDatePerfDefense()?date('Y-m-d H:i:s',$perso->GetDatePerfDefense()):NULL), PDO::PARAM_INT);
		$q->bindValue(':tmp_perf_defense', ($perso->GetTmpPerfDefense()?$perso->GetTmpPerfDefense():NULL), PDO::PARAM_INT);
		$q->bindValue(':maison_installe', (is_null($perso->GetMaisonInstalle())?NULL:implode(',', $perso->GetMaisonInstalle())), PDO::PARAM_STR);
		$q->bindValue(':clan', (is_null($perso->GetClan())?NULL:htmlspecialchars($perso->GetClan(), ENT_QUOTES)), PDO::PARAM_STR);
		$q->bindvalue(':date_last_msg_lu', date('Y-m-d H:i:s', $perso->GetDateLasMessageLu()), PDO::PARAM_INT);
		$q->bindValue(':not_attaque', ($perso->GetNotifAttaque()?'1':NULL), PDO::PARAM_STR);
		$q->bindValue(':not_combat', ($perso->GetNotifCombat()?'1':NULL), PDO::PARAM_STR);
		$q->bindValue(':nb_points', $perso->GetNbPoints(), PDO::PARAM_INT);
		
		return $q->execute();
	}
	public function UpdateQuete(quete $quete){
		$q = $this->db->prepare('UPDATE table_quetes SET 
				quete_position = :quete_position, 
				quete_vie = :quete_vie, 
				quete_reussi = :quete_reussi, 
				date_start = :date_start, 
				date_end = :date_end, 
				last_combat = :last_combat 
				WHERE id_quete_en_cours = :id_quete_en_cours');
		
		$q->bindValue(':id_quete_en_cours', $quete->GetIDQuete(), PDO::PARAM_INT);
		$q->bindValue(':quete_position', implode(',',array_merge(array($quete->GetCarte()),$quete->GetPosition())), PDO::PARAM_STR);
		$q->bindValue(':quete_vie', ($quete->GetVie()?$quete->GetVie():NULL), PDO::PARAM_INT);
		$q->bindValue(':quete_reussi', ($quete->GetStatus()?$quete->GetStatus():NULL), PDO::PARAM_INT);
		$q->bindValue(':date_start', ($quete->GetDateStart()?date('Y-m-d H:i:s',$quete->GetDateStart()):NULL), PDO::PARAM_STR);
		$q->bindValue(':date_end', ($quete->GetDateEnd()?date('Y-m-d H:i:s',$quete->GetDateEnd()):NULL), PDO::PARAM_STR);
		$q->bindValue(':last_combat', ($quete->GetDateCombat()?date('Y-m-d H:i:s',$quete->GetDateCombat()):NULL), PDO::PARAM_STR);

		return $q->execute();
	}
	public function UpdateMarche(marchant $marche){
		$q = $this->db->prepare('UPDATE table_marche SET 
				contenu_vendeur = :contenu_vendeur 
				WHERE type_vendeur = "marchant"');
		$contenu = implode(',', $marche->GetLstContenu());
		$q->bindValue(':contenu_vendeur', $contenu, PDO::PARAM_STR);
		
		return $q->execute();
	}
	public function UpdateBatiment(batiment $batiment){
		$result = null;
		
		$q = $this->db->prepare('UPDATE table_carte SET 
				etat_batiment = :etat_batiment, 
				login = :login, 
				contenu_batiment = :contenu_batiment, 
				date_last_attaque = :date_last_attaque, 
				res_pierre = :res_pierre, 
				res_bois = :res_bois, 
				res_nourriture = :res_nourriture, 
				res_eau = :res_eau, 
				niveau_batiment = :niveau_batiment, 
				date_amelioration = :date_amelioration, 
				tmp_amelioration = :tmp_amelioration, 
				date_action_batiment = :date_action_batiment, 
				detruit = :detruit 
				WHERE id_case_carte = :id_case_carte');
		
		$q->bindValue(':id_case_carte', $batiment->GetIDCase(), PDO::PARAM_INT);
		$q->bindValue(':etat_batiment', $batiment->GetEtat(), PDO::PARAM_INT);
		if(is_null($batiment->GetContenu())){
			$Contenu = null;
		}else{
			switch(get_class($batiment)){
				case 'bank':
				case 'maison':
				case 'ressource':
					$Contenu = $batiment->GetContenu();
					break;
				case 'mine':
				case 'carriere':
				case 'ferme':
				case 'potager':
				case 'entrepot':
					$Contenu = implode(',', $batiment->GetContenu());
					break;
			}
		}
		$q->bindValue(':contenu_batiment', $Contenu, PDO::PARAM_STR);
		$q->bindValue(':login', (is_null($batiment->GetLogin())?NULL:$batiment->GetLogin()), PDO::PARAM_STR);
		$q->bindValue(':date_last_attaque', (!is_null($batiment->GetDateLastAction())?date('Y-m-d H:i:s',$batiment->GetDateLastAction()):NULL), PDO::PARAM_INT);
		$q->bindValue(':date_action_batiment', (!is_null($batiment->GetDateAction())?date('Y-m-d H:i:s',$batiment->GetDateAction()):NULL), PDO::PARAM_INT);
		$q->bindValue(':detruit', ($batiment->GetDetruit()?'1':NULL), PDO::PARAM_STR);
		
		$q->bindValue(':res_pierre', null, PDO::PARAM_INT);
		$q->bindValue(':res_bois', null, PDO::PARAM_INT);
		$q->bindValue(':res_nourriture', null, PDO::PARAM_INT);
		$q->bindValue(':res_eau', null, PDO::PARAM_INT);
		switch(get_class($batiment))
		{
			case 'maison':
				$q->bindValue(':res_eau', $batiment->GetRessource(maison::TYPE_RES_EAU_POTABLE), PDO::PARAM_INT);
				$q->bindValue(':res_nourriture', $batiment->GetRessource(maison::TYPE_RES_NOURRITURE), PDO::PARAM_INT);
				break;
			case 'ressource':
				$q->bindValue(':res_pierre', ((in_array($batiment->GetNomType(), array(ressource::NOM_RESSOURCE_PIERRE, ressource::NOM_RESSOURCE_OR)) AND !is_null($batiment->GetEtatRessource()))?$batiment->GetEtatRessource():NULL), PDO::PARAM_INT);
				$q->bindValue(':res_bois', (($batiment->GetNomType() == ressource::NOM_RESSOURCE_BOIS AND !is_null($batiment->GetEtatRessource()))?$batiment->GetEtatRessource():NULL), PDO::PARAM_INT);
				break;
		}
		
		$q->bindValue(':niveau_batiment', $batiment->GetNiveau(), PDO::PARAM_INT);
		$q->bindValue(':date_amelioration', (!is_null($batiment->GetDateAmelioration())?date('Y-m-d H:i:s',$batiment->GetDateAmelioration()):NULL), PDO::PARAM_INT);
		$q->bindValue(':tmp_amelioration', (is_null($batiment->GetTmpAmelioration())?NULL:$batiment->GetTmpAmelioration()), PDO::PARAM_INT);
		
		return $q->execute() or die(print_r($q->errorInfo(), true));
	}
	/* Public function UpdateRessource(ressource $oRessource){
		$q = $this->db->prepare("UPDATE table_carte SET 
			login = :login, 
			contenu_batiment = :contenu_batiment, 
			res_pierre = :res_pierre, 
			res_bois = :res_bois, 
			detruit = :detruit, 
			date_action_batiment = :date_action_batiment 
			WHERE coordonnee = :coordonnee");
		
		$q->bindValue(':login', (is_null($oRessource->GetCollecteur())?NULL:$oRessource->GetCollecteur()), PDO::PARAM_STR);
		$q->bindValue(':contenu_batiment', (is_null($oRessource->GetTypeContenu())?NULL:$oRessource->GetTypeContenu()), PDO::PARAM_INT);
		$q->bindValue(':res_pierre', ((in_array($oRessource->GetNomType(), array(ressource::NOM_RESSOURCE_PIERRE, ressource::NOM_RESSOURCE_OR)) AND !is_null($oRessource->GetEtat()))?$oRessource->GetEtat():NULL), PDO::PARAM_INT);
		$q->bindValue(':res_bois', (($oRessource->GetNomType() == ressource::NOM_RESSOURCE_BOIS AND !is_null($oRessource->GetEtat()))?$oRessource->GetEtat():NULL), PDO::PARAM_INT);
		$q->bindValue(':detruit', ($oRessource->GetDetruit()?true:NULL), PDO::PARAM_INT);
		$q->bindValue(':date_action_batiment', date('Y-m-d H:i:s', $oRessource->GetDateDebutAction()), PDO::PARAM_STR);
		$q->bindValue(':coordonnee', $oRessource->GetCoordonnee(), PDO::PARAM_STR);
		
		return $q->execute();
	} */
}
?>