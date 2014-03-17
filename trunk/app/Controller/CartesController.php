<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CartesController
 *
 * @author bdebou
 */
class CartesController extends AppController{
    
    public $components = array(
        'Session', 
        'Auth',
        'Cartes');
    public $helpers = array('Html', 'Form');
    
    public function beforeFilter() {
        parent::beforeFilter();
//        $this->Auth->allow('add', 'logout', 'GetStatistiques');
    }
    
    public function index(){
        //$arGrille=NULL;
        
        $data = $this->Carte->find(
                'all',
                array(
                    'conditions'=>array(
                        'user_id'=>$this->Session->read('Auth.User.id')
                        ),
                    'recursive'=>1
                    ));
        
        
        
        var_dump($data);
        die();
    }
    public function view($numCarte = 'all'){
        
        $arGrille = array();
        
//        if(strtolower($numCarte) == 'all'){
            $this->Set('AllCartes',true);
            
            $this->Set('arCartes', CartesComponent::GetListCartes());
            $this->Set('nbLignes', CartesComponent::NB_LIGNES);
            $this->Set('nbColonnes', CartesComponent::NB_COLONNES);
            $this->Set(compact($numCarte));
            
//            for($i=0; $i <= (count($arCartes) - 1); $i++){
//                $this->AffichageJoueurSurGrille($arGrille[$arCartes[$i]], $arCartes[$i]);
                $this->AffichageJoueurSurGrille($arGrille, $numCarte);
//                $this->AffichageBatimentSurGrille($arGrille[$arCartes[$i]], $arCartes[$i]);
//                $this->AffichageBatimentSurGrille($arGrille, $numCarte);
//            }
//        }elseif(is_int($numCarte)){
//            $this->Set('AllCartes', false);
//        }
       
        $this->Set('Grilles', $arGrille);
    }
    
    Protected function AffichageJoueurSurGrille(&$grille, $numCarte){
	//$sql="SELECT vie, position, login, civilisation FROM table_joueurs;";
	       
        $data = $this->Carte->User->find(
                'all',
                array(
                    'recursive'=>0,
                    'fields'=>array('User.vie', 'User.position', 'User.name', 'Civilisation.name'),
                    'conditions'=>array('User.id >'=>1)
                    )
                );
//        $grille = array();
        
        ForEach($data as $User){
            
            $position = explode(',', $User['User']['position']);
            
            if( $numCarte == $position[0]
                or strtolower($numCarte) == 'all'){
                if( empty($grille[$position[0]][$position[1]][$position[2]]['name'])
                    OR $User['User']['name'] == $this->Session->read('Auth.User.id')){

                    $grille[$position[0]][$position[1]][$position[2]]['name']         = $User['User']['name'];
                    $grille[$position[0]][$position[1]][$position[2]]['vie']          = $User['User']['vie'];
                    $grille[$position[0]][$position[1]][$position[2]]['civilisation'] = $User['Civilisation']['name'];

//                }elseif(){
//
//                    $grille[$position[1]][$position[2]]['name']     = $User['User']['name'];
//                    $grille[$position[1]][$position[2]]['vie']          = $User['User']['vie'];
//                    $grille[$position[1]][$position[2]]['civilisation'] = $User['Civilisation']['name'];

                }
            }
        }
//        var_dump($numCarte);var_dump($grille);
//        return $grille;
    }
    Protected function AffichageQueteSurGrille(&$grille, $numCarte, $AllCartes){
            foreach($_SESSION['QueteEnCours'] as $Quete){

                    if($numCarte == $Quete->GetCarte() AND $Quete->GetVisibilite()){

                            $arPosition = $Quete->GetPosition();
                            $InfoBulle = '<b>'.$Quete->GetNom().'</b>';
                            $grille[intval($arPosition[0])][intval($arPosition[1])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($InfoBulle).'\');" onmouseout="cache();" style="background: url(\'./img/quetes/icones/'.($AllCartes?'mini/':'').$Quete->GetImgNom().'.png\') no-repeat center;"';

                    }
            }
    }
    Protected function AffichageBatimentSurGrille(&$grille, $numCarte/*, $AllCartes*/){
            //global $lstNonBatiment, $lstBatimentsNonConstructible;

//        $grille = array();
        
        $db = $this->Carte->Battype->getDataSource();
        $subQuery = $db->buildStatement(
                array(
                    'fields'=>array('Battype.id'),
                    'table'=>$db->fullTableName($this->Carte->Battype),
                    'alias'=>'Battype',
                    'conditions'=>array(
                        'Battype.constructible'=>0
                        )
                    ),
                $this->Carte->Battype
                );
        $subQuery = ' Carte.Battype_id NOT IN ('.$subQuery.') AND Carte.detruit IS NULL ';
        $subQueryExpression = $db->expression($subQuery);
        $options['conditions'][] = $subQueryExpression;
        $options['fields'] = array('*');

        $data = $this->Carte->find('all', $options);

        ForEach($data as $Case){

            $position = explode(',', $Case['Carte']['coordonnee']);

            if($numCarte == $position[0]){
                $dataBatiment = $this->Carte->Battype->find(
                    'first',
                    array(
                        'conditions'=>array(
                            'Battype.id'=>$Case['Carte']['battype_id']
                            ),
                        'fields'=>array('Battype.name')
                        )
                    );
//
//                            $objBatiment = FoundBatiment($row['id_type_batiment'], $row['login'], $row['coordonnee']);
//
//                            if(!is_null($objBatiment)){
//
                        //on ajoute l'infobulle à l'icone du batiment
                //$grille[$position[1]][$position[2]]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($objBatiment->GetInfoBulle($AllCartes)).'\');" onmouseout="cache();"';
                

                        //on ajout l'icone du batiment
                $grille[$position[1]][$position[2]]['batiment-mini'] = ' style="background: url(\'./img/carte/mini/'.$dataBatiment['Battype']['name'].'-'.($Case['Carte']['user_id'] == $this->Session->read('Auth.User.id')?'a':'b').'.png\') no-repeat center;"';
                $grille[$position[1]][$position[2]]['batiment'] = ' style="background: url(\'./img/carte/'.$dataBatiment['Battype']['name'].'-'.($Case['Carte']['user_id'] == $this->Session->read('Auth.User.id')?'a':'b').'.png\') no-repeat center;"';
//
//                            }
                }
        }
//        var_dump($numCarte);var_dump($grille);
//        return $grille;
    }
    Protected function AffichageRessourceSurGrille(DBManage &$db, &$grille, $numCarte, $AllCartes){
            global $lstBatimentsNonConstructible;

            $sql="SELECT login, coordonnee, id_type_batiment FROM table_carte WHERE id_type_batiment IN (".implode(', ', $lstBatimentsNonConstructible).") AND detruit IS NULL;";
            $requete = $db->Query($sql);

            while($row = mysql_fetch_array($requete, MYSQL_ASSOC)){

                    $position = explode(',', $row['coordonnee']);

                    if($numCarte == $position['0']){

                            //$objBatiment = FoundBatiment($row['id_type_batiment'], $row['login'], $row['coordonnee']);
                            $objRessource = FoundBatiment(NULL, NULL, $row['coordonnee']);

                            if(!is_null($objRessource)){

                                    //on ajoute l'infobulle � l'icone du batiment
                                    $grille[intval($position[1])][intval($position[2])]['batiment'] = ' onmouseover="montre(\''.CorrectDataInfoBulle($objRessource->GetInfoBulle($AllCartes)).'\');" onmouseout="cache();"';

                                    //on ajout l'icone du batiment
                                    $grille[intval($position[1])][intval($position[2])]['batiment'] .= ' style="background: url(\'./img/carte/'.($AllCartes?'mini/':'').$objRessource->GetImgName().'.png\') no-repeat center;"';

                            }
                    }
            }
    }
    protected function AfficheCarte(DBManage &$db, $numCarte, $AllCartes = false, $arTailleCarte){
	
	$txt = null;

	//On ajoute les joueurs
	AffichageJoueurSurGrille($db, $grille, $numCarte);
	
	//On ajoute les quetes sur la carte.
	if(isset($_SESSION['QueteEnCours'])){
		AffichageQueteSurGrille($grille, $numCarte, $AllCartes);
	}
	
	//on ajoute les ressources
	AffichageRessourceSurGrille($db, $grille, $numCarte, $AllCartes);
	
	//on cache les quetes par les batiments
	//on ajoute les infos des batiments
	AffichageBatimentSurGrille($db, $grille, $numCarte, $AllCartes);

//	//on cr�e la table carte pour son affichage
//	if($AllCartes){
//		$txt .= '<table class="carte_petite" onmouseover="montre(\''.CorrectDataInfoBulle('<b>Zone '.strtoupper($numCarte).'</b>').'\');" onmouseout="cache();">';
//		$size = 8;
//	}else{
//		$txt .= '<table class="carte" style="background-image: url(\'img/carte/gaule-'.$numCarte.'.jpg\');">';
//		$size = 29;
//	}
//	
//	for($i=0;$i<=$arTailleCarte['NbLigne'];$i++){
//		$txt .= '
//				<tr>
//					';
//		for($j=0;$j<=$arTailleCarte['NbColonne'];$j++){
//			$txt .= '<td'.(isset($grille[$i][$j]['batiment'])?$grille[$i][$j]['batiment']:'').'>';
//			if(isset($grille[$i][$j]['login'])){
//				$txt .= '<img alt="Perso '.$grille[$i][$j]['login'].'" 
//							src="./img/carte/'.$grille[$i][$j]['civilisation'].'-'.($_SESSION['joueur']==$grille[$i][$j]['login']?'a':'b').'.png" 
//							height="'.$size.'px" 
//							width="'.$size.'px" 
//							onmouseover="montre(\''.
//								CorrectDataInfoBulle(
//									'<table><tr>'
//									.($AllCartes?
//										'<td rowspan="2">'
//										.'<img alt="Perso '.$grille[$i][$j]['login'].'" src="./img/carte/'.$grille[$i][$j]['civilisation'].'-'.($_SESSION['joueur']==$grille[$i][$j]['login']?'a':'b').'.png" height="30px" width="30px" />'
//										.'</td>'
//										:'')
//									.'<th>'.$grille[$i][$j]['login'].'</th></tr>'
//									.'<tr><td><img alt="Barre de Vie" src="./fct/fct_image.php?type=VieCarte&amp;value='.$grille[$i][$j]['vie'].'&amp;max='.personnage::VIE_MAX.'" /></td></tr>'
//									.'</table>'
//									)
//							.'\');" 
//							onmouseout="cache();" />';
//			}
//			$txt .= '</td>';
//		}
//		$txt .= '
//				</tr>';
//	}
//	$txt .= '
//			</table>';

	return $txt;
}
}

?>
