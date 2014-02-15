<?php
/**
 * Description of BatimentComponent
 *
 * @author bruno.deboubers@gmail.com
 */
class BatimentComponent extends Component{
        
    Const NIVEAU_MAX				= 4;                // Niveau Maximum pour chaque batiment.
    Const TAUX_ATTAQUANT			= 1.15;             // Taux utiliser pour calculer l'effet de surprise

    const CODE_ESCLAVE				= 'Esclave';

    //Les points
    const POINT_BATIMENT_ATTAQUE	= 3;
    const POINT_BATIMENT_DETRUIT	= 20;
    const POINT_BATIMENT_REPARE		= 8;
    
    Protected $PrixReparation   = array('Sesterce'=>5, 'BC'=>10);   // Prix des réparation par point de vie
    
    //--- fonction qui est lancer lors de la création de l'objet. ---
//    public function __construct(array $carte = NULL, array $batiment){
//            $this->Hydrate($carte, $batiment);
//
//    }
    //--- on rempli l'objet avec les valeurs correspondant. ---
//    public function Hydrate(array $carte = NULL, array $batiment){
//        date_default_timezone_set('Europe/Brussels');
//
//        foreach ($batiment as $key => $value){
//                switch ($key){
//                        case 'batiment_type':			$this->Type = strval($value); break;
//                        case 'batiment_nom':			$this->Nom = strval($value); break;
//                        case 'batiment_description':	$this->Description = strval($value); break;
//                        case 'batiment_attaque':		$this->Attaque = (is_null($value)?NULL:intval($value)); break;
//                        case 'batiment_defense':		$this->Defense = (is_null($value)?NULL:intval($value)); break;
//                        case 'batiment_distance':		$this->Distance = (is_null($value)?NULL:intval($value)); break;
//                        case 'batiment_vie':			$this->EtatMax = (is_null($value)?NULL:intval($value)); break;
//                        case 'batiment_prix':			$this->LstPrix = (is_null($value)?NULL:explode(',', $value)); break;
//                        case 'batiment_points':			$this->NbPoints = intval($value); break;
//                        case 'id_type':					$this->IDType = (int)$value; break;
//                }
//        }
//
//        if(!is_null($carte))
//        {
//                foreach ($carte as $key => $value){
//                        switch ($key){
//                                case 'coordonnee':			$this->Coordonnee = explode(',', $value); break;
//                                case 'login':				$this->Login = strval($value); break;
//                                case 'id_case_carte':		$this->IDCaseCarte = intval($value); break;
//                                case 'etat_batiment':		$this->Etat = (is_null($value)?NULL:intval($value)); break;
//                                case 'date_last_attaque':	$this->DateLastAction = (is_null($value)?NULL:strtotime($value)); break;
//                                case 'date_action_batiment':$this->DateAction = (is_null($value)?NULL:strtotime($value)); break;
//                                case 'detruit':				$this->Detruit = (is_null($value)?false:true); break;
//                                case 'contenu_batiment':	$this->Contenu = (is_null($value)?NULL:$value); break;
//                                case 'niveau_batiment':		$this->Niveau = (is_null($value)?NULL:intval($value)); break;
//                                case 'date_amelioration':	$this->DateAmelioration = (is_null($value)?NULL:strtotime($value)); break;
//                                case 'tmp_amelioration':	$this->TmpAmelioration = (is_null($value)?NULL:intval($value)); break;
//                        }
//                }
//        }
//
//    }
    
    public function GetImgName(){
        return get_class($this).'-'.($this->Login == $_SESSION['joueur']?'a':'b');
        
    }
}

?>
