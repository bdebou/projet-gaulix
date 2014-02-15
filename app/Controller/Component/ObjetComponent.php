<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ObjectComponent
 *
 * @author bdebou
 */
class ObjetComponent extends Component{
    
    private $ID;
    private $Quantite;
    private $Civilisation;
    private $Rarete;
    private $IDQuete;
    private $Code;
    private $Nom;
    private $Description;
    private $Ressource;
    private $Prix;
    private $Cout;
    private $Niveau;
    Private $Competence;

    Const TAUX=3;
    
    public function Hydrate($data, $nb){
            date_default_timezone_set('Europe/Brussels');

            $this->Quantite = (int)$nb;

            foreach ($data as $key => $value){
                    switch ($key){
                            case 'id_objet':		$this->ID		= intval($value);								break;
                            case 'objet_civilisation':  $this->Civilisation	= strval($value);								break;
                            case 'objet_rarete':	$this->Rarete		= intval($value);								break;
                            case 'objet_quete':		$this->IDQuete		= (is_null($value)?NULL:intval($value));		break;
                            case 'objet_code':		$this->Code		= strval($value);								break;
                            case 'objet_nom':		$this->Nom		= strval($value);								break;
                            case 'objet_description':	$this->Description	= (is_null($value)?NULL:strval($value));		break;
                            case 'objet_niveau':	$this->Niveau		= intval($value);								break;
                            case 'objet_ressource':	$this->Ressource	= (is_null($value)?NULL:explode(',', $value));	break;
                            case 'objet_prix':		$this->Prix		= intval($value);								break;
                            case 'objet_cout':		$this->Cout		= (is_null($value)?NULL:explode(',', $value));	break;
                            case 'objet_competence':	$this->Competence	= (is_null($value)?NULL:strval($value));		break;
                    }
            }

    }

    //Les Affichages
    //==============
    public function AfficheInfoObjet($intHeightImg = 50) {

            $InfoBulle = '<table class="InfoBulle">'
                                    . '<tr><th>' . $this->Nom . '</th></tr>'
                                    . ((!is_null($this->Description))?'<tr><td style="text-align:left;">' . $this->Description . '</td></tr>':'')
                                    . '<tr><td>' . AfficheIcone(personnage::TYPE_RES_MONNAIE) . ' = ' . $this->Prix . '</td></tr>'
                                    . '</table>';

            return '<img 
                                    style="vertical-align:middle;" 
                                    alt="' . $this->Nom . '" 
                                    src="./img/objets/' . $this->Code . '.png" 
                                    onmouseover="montre(\'' . CorrectDataInfoBulle($InfoBulle) . '\');" 
                                    onmouseout="cache();" 
                                    height="'.$intHeightImg.'px"
                             />';
    }
    //Les GET
    //=======
    public function GetRessource(){
            return $this->Ressource;
    }
    public function GetNiveau(){
            return $this->Niveau;
    }
    public function GetQuantite(){
            return $this->Quantite;
    }
    public function GetCode(){
            return $this->Code;
    }
    public function GetPrix(){
            return $this->Prix;
    }
    public function GetNom(){
            return $this->Nom;
    }
    public function GetDescription(){
            return $this->Description;
    }
    public function GetInfoBulle(){
            return '<table>'
                            .'<tr><th colspan="2">'.$this->Nom.'</th></tr>'
                            .'<tr><td>Prix</td><td>'.$this->Prix.AfficheIcone(personnage::TYPE_RES_MONNAIE).'</td></tr>'
                            .'</table>';
    }
    public function GetCoutFabrication(){
            return $this->Cout;
    }
    public function GetType(){
            return NULL;
    }
    /**
     * V�rifie si dans le cout de fabrication de l'objet, il n'y a pas besoin de comp�tence dont le joueur n'aura jamais � cause de la restriction de sa carri�re choisie.
     * @param PersonnageComponent $oJoueur
     * @return boolean
     */
    Public function CheckIfAvailable(PersonnageComponent $oJoueur){
            foreach($this->Cout as $Cout)
            {
                    $arCout = explode('=', $Cout);
                    if(QuelTypeObjet($arCout[0]) == PersonnageComponent::TYPE_COMPETENCE)
                    {
                            if(!$oJoueur->CheckIfCompetenceAvailable($arCout[0]))
                            {
                                    return false;
                            }
                    }
            }
            return true;
    }
    /**
    * Retourne le code de la Comp�tence requise pour s'�quiper de l'objet
    * @return string|NULL
    */
    public function GetCompetence(){
            if(is_null($this->Competence))
            {
                    return NULL;
            }

            $arTemp = explode('=', $this->Competence);

            return $arTemp[0];
    }
}

?>
