<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ObjArmementComponent
 *
 * @author bdebou
 */
class ObjArmementComponent extends Component{
    
    private $Attaque;
    private $Defense;
    private $Distance;

    const TYPE_ATTAQUE		= 'Attaque';
    const TYPE_DEFENSE		= 'Defense';
    const TYPE_DISTANCE		= 'Distance';

    const TYPE_BOUCLIER		= 'Bouclier';
    const TYPE_JAMBIERE		= 'Jambiere';
    const TYPE_ARME		= 'Arme';
    const TYPE_CASQUE		= 'Casque';
    const TYPE_CUIRASSE		= 'Cuirasse';

//    public function __construct(array $data, $nb){
//        date_default_timezone_set('Europe/Brussels');
//
//        parent::Hydrate($data, $nb);
//
//        foreach($data as $key=>$value)
//        {
//            switch($key)
//            {
//                case 'objet_attaque':
//                    $this->Attaque = (is_null($value)?0:intval($value));
//                    break;
//                case 'objet_defense':
//                    $this->Defense = (is_null($value)?0:intval($value));
//                    break;
//                case 'objet_distance':
//                    $this->Distance = (is_null($value)?0:intval($value));
//                    break;
//            }
//        }
//    }

    //Les Affichages
    //==============
    public function AfficheInfoObjet($intHeightImg = 50) {

            $nbInfo = 0;
            $txtInfo = '<tr>';

            if($this->Attaque != 0){
                    $txtInfo .= '<td>' . AfficheIcone(objArmement::TYPE_ATTAQUE) . ' = ' . $this->Attaque . '</td>';
                    $nbInfo++;
            }

            if($this->Distance != 0){
                    $txtInfo .= '<td>' . AfficheIcone(objArmement::TYPE_DISTANCE) . ' = ' . $this->Distance . '</td>';
                    $nbInfo++;
            }

            if($this->Defense != 0){
                    $txtInfo .= '<td>' . AfficheIcone(objArmement::TYPE_DEFENSE) . ' = ' . $this->Defense . '</td>';
                    $nbInfo++;
            }

            $txtInfo .= '</tr>';

            $InfoBulle = '<table class="InfoBulle">'
                                    . '<tr><th' . (isset($txtInfo) ? ' colspan="'.$nbInfo.'"' : '') . '>' . $this->GetNom() . '</th></tr>'
                                    . ((!is_null($this->GetDescription()))?'<tr><td'.(isset($txtInfo)?' colspan="'.$nbInfo.'"':'').' style="text-align:left;">' . $this->GetDescription() . '</td></tr>':'')
                                    . (isset($txtInfo) ? $txtInfo : '')
                                    . '<tr><td' . (isset($txtInfo) ? ' colspan="'.$nbInfo.'"' : '') . '>' . AfficheIcone(personnage::TYPE_RES_MONNAIE) . ' = ' . $this->GetPrix() . '</td></tr>'
                                    . '</table>';

            return '<img
                                    style="vertical-align:middle;" 
                                    alt="' . $this->GetNom() . '" 
                                    src="./img/objets/' . $this->GetCode() . '.png" 
                                    onmouseover="montre(\'' . CorrectDataInfoBulle($InfoBulle) . '\');" 
                                    onmouseout="cache();" 
                                    height="'.$intHeightImg.'px"
                             />';
    }
    /**
     * Retourne une ligne d'un tableau pour afficher les infos de l'armement
     * @param integer $ColSpan
     * @param boolean $Ligne
     * @return string
     */
    Public Function AfficheInfoTd($ColSpan = NULL, $Ligne = false){
            $txt = NULL;

            if($Ligne){$txt .= '<tr>';}

            $txt .= '<td'.(!is_null($ColSpan)?' colspan="'.$ColSpan.'"':NULL).'>'.AfficheIcone(self::TYPE_ATTAQUE).' : '.$this->GetAttaque().'</td>';
            $txt .= '<td'.(!is_null($ColSpan)?' colspan="'.$ColSpan.'"':NULL).'>'.AfficheIcone(self::TYPE_DEFENSE).' : '.$this->GetDefense().'</td>';
            $txt .= '<td'.(!is_null($ColSpan)?' colspan="'.$ColSpan.'"':NULL).'>'.AfficheIcone(self::TYPE_DISTANCE).' : '.$this->GetDistance().'</td>';

            if($Ligne){$txt .= '</tr>';}

            return $txt;
    }
    //Les GETS
    //========
    Public function GetType(){
        switch(substr($this->GetCode(), 0, 3))
        {
            case 'Bcl':	return self::TYPE_BOUCLIER;
            case 'Arm':	return self::TYPE_ARME;
            case 'Crs':	return self::TYPE_CUIRASSE;
            case 'Csq':	return self::TYPE_CASQUE;
            case 'Jbr':	return self::TYPE_JAMBIERE;
        }
        return NULL;
    }
    public function GetAttaque(){
            return $this->Attaque;
    }
    public function GetDefense(){
            return $this->Defense;
    }
    public function GetDistance(){
            return $this->Distance;
    }
}

?>
