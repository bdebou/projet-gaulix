<?php
/**
 * Class de personnage Gaulois et Romain
 * @author bruno.deboubers@gmail.com
 *
 */
class PersonnageComponent extends Component{
    
    public $components = array('Session', 'Auth');
    
    Const TAILLE_MINIMUM_BOLGA		= 20;                           //La taille minimum du bolga
    Const DUREE_SORT			= 432000;                       // Limite de temp pour l'utilisation d'un sort (3600 * 24 *5).
    Const DEPLACEMENT_MAX		= 300;                          // Limite le nombre déplacement maximum
    Const TEMP_DEPLACEMENT_SUP		= 3600;                         // Temp d'attente pour avoir de nouveau du déplacement
    Const NB_DEPLACEMENT_SUP		= 1;                            // Nombre de point de déplacement gagné tout les x temp
    Const VIE_MAX			= 300;                          // Limite de vie maximum
    Const TAUX_ATTAQUANT		= 1.15;                         // Taux d'augmentation de l'attaquant
    Const TAUX_VOL_ARGENT		= 0.10;                         // Taux pour le montant de vol d'argent lors d'un combat perdu
    Const TEMP_COMBAT                   = 10800;                        // Temps entre 2 combats entre même joueurs

    const TYPE_RES_MONNAIE		= 'Sesterce';                   //
    const TYPE_COMPETENCE		= 'Compétence';                 //

    Const TYPE_COMBAT			= 'Combat';                     //
//    Const TYPE_PERFECT_ATTAQUE		= ObjArmementComponent::TYPE_ATTAQUE;   //
//    Const TYPE_PERFECT_DEFENSE		= ObjArmementComponent::TYPE_DEFENSE;   //

    Const TYPE_EXPERIENCE		= 'Expérience';                 //
    Const TYPE_VIE			= 'Vie';                        //

    const CIVILISATION_ROMAIN		= 'Romains';                    //
    Const ID_CIVILISATION_ROMAIN        = 3;
    const CIVILISATION_GAULOIS		= 'Gaulois';                    //
    const ID_CIVILISATION_GAULOIS       = 2;

    Const CARRIERE_CLASS_GUERRIER	= 'Guerrier';                   //
    Const CARRIERE_CLASS_DRUIDE		= 'Druide';                     //
    Const CARRIERE_CLASS_ARTISAN	= 'Artisan';                    //

    //Les points
    const POINT_COMBAT			= 10;                           //
    const POINT_NIVEAU_TERMINE		= 15;                           //
    const POINT_COMPETENCE_TERMINE	= 5;                            //
    const POINT_OBJET_FABRIQUE		= 1;                            //
    const POINT_PERSO_TUE		= 150;                          //

    public function GetMaxExperience($intNiveau){
        
        if(empty($intNiveau)){return false;}
        
        $nb = 0;
        for ($i = 0; $i <= $intNiveau; $i++)
        {
                $nb += ($i + 1) * 100;
        }
        return $nb;
    }
    
    
}
?>