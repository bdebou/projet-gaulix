<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users
 *
 * @author bdebou
 */
class UsersController extends AppController {
    
    public $components = array('Session', 'Auth');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add', 'logout', 'GetStatistiques', 'SecondStep');
    }

    public function index(){
        $data = $this->User->find(
                'first',
                array(
                    'conditions'=>array(
                        'user.id'=>$this->Session->read('Auth.User.id')
                        ),
                    'recursive'=>1
                    )
                );
        var_dump($data);
        die();
    }
    public function login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                /// Si c'est un POST et que c'est dans l'action Login on vérifie si on a bien terminé son inscription.
                /// Il faut que la acrrière et le village soit sélectionné.
                if( is_null($this->Session->read('Auth.User.village_id'))
                   || is_null($this->Session->read('Auth.User.carriere_id'))){
                    return $this->redirect(array('controler'=>'user','action'=>'SecondStep'));
                }
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Session->setFlash(
                        'Pseudo ou mot de passe invalide, réessayez',
                        'FlashAlertMessage',
                        array(
                            'type'=>'Erreur',
                            'level'=>'danger'
                            )
                        );
            }
        }
    }  
    public function InfoBar(){
         
        $lstCodeEquipement = array(
            'code_casque',
            'code_arme',
            'code_bouclier',
            'code_jambiere',
            'code_cuirasse');
        
         $data = $this->User->find(
                'first',
                array(
                    'fields'=>array_merge(
                            $lstCodeEquipement,
                            array(
                                'user.vie', 
                                'user.experience',
                                'user.niveau',
                                'user.attaque',
                                'user.defense'
                                )
                            ),
                    'conditions'=>array(
                        'user.id'=>$this->Session->read('Auth.User.id')
                        )
                    )
                );
        $data['Objet']['Attaque'] = $data['Objet']['Defense'] = $data['Objet']['Distance'] = 0;
                
        foreach($lstCodeEquipement as $code){
            if(!is_null($data['User'][$code])){
                $tmp = $this->User->Objet->find(
                        'first',
                        array(
                            'fields'=>array(
                                'objet.attaque',
                                'objet.defense',
                                'objet.distance'
                                ),
                            'conditions'=>array(
                                'objet.code'=>$data['User'][$code]
                                )
                            )
                        );
                $data['Objet']['Attaque'] += $tmp['Objet']['attaque'];
                $data['Objet']['Defense'] += $tmp['Objet']['defense'];
                $data['Objet']['Distance'] += $tmp['Objet']['distance'];
            }
        }
        
        $data['User']['maxExperience'] = PersonnageComponent::GetMaxExperience($data['User']['niveau']);

        return $data;
    }
    public function add(){
        if($this->Session->read('Auth.User.id')){
            $this->Session->SetFlash(
                    'Accès refusé à cette page.', 
                    'FlashAlertMessage', 
                    array(
                        'type'=>'Error',
                        'level'=>'danger'
                        )
                    );
            $this->redirect(
                    array(
                        'controller'=>'pages'
                        )
                    );
        }
        
        $lstCivilisation = $this->User->Civilisation->find('list',array('conditions'=>array('id >'=>1)));
        $this->Set(compact('lstCivilisation'));

        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->saveAssociated($this->request->data)) {
                $this->Session->setFlash(
                        'Le personnage a bien été créé. Veuillez vous connecter pour terminer votre inscription en choissisant votre village et carrière.',
                        'FlashAlertMessage',
                        array(
                            'type'=>'Ok',
                            'level'=>'success'
                            )
                        );
                return $this->redirect(array('controller' => 'pages'));
            } else {
                $this->Session->setFlash(
                        'Le personnage n\'a pas été créé. Merci de réessayer.',
                        'FlashAlertMessage',
                        array(
                            'type'=>'Attention',
                            'level'=>'warning'
                            )
                        );
            }
        }
    }
    public function SecondStep(){
        if ($this->request->is('post')) {
            
            $this->request->data['User']['id']                  = $this->Session->read('Auth.User.id');
            $this->request->data['Village']['civilisation_id']  = $this->Session->read('Auth.User.civilisation_id');
            
            if($this->request->data['User']['village_id'] != 0)
                unset($this->request->data['Village']);
            else
                unset($this->request->data['User']['village_id']);
            
            //var_dump($this->Session->read());die();            
            
            if ($this->User->saveAssociated($this->request->data)) {
                
                $this->Session->setFlash(
                        'Le personnage a bien été créé. Bonne chance!',
                        'FlashAlertMessage',
                        array(
                            'type'=>'Ok',
                            'level'=>'success'
                            )
                        );
                return $this->redirect(array('controller' => 'pages'));
            } else {
                $this->Session->setFlash(
                        'Le personnage n\'a pas été créé. Merci de réessayer.',
                        'FlashAlertMessage',
                        array(
                            'type'=>'Attention',
                            'level'=>'warning'
                            )
                        );
            }
        }else{
            if(is_null($this->Session->read('Auth.User.village_id'))){
                $lstVillage = $this->User->Village->find(
                        'list',
                        array(  
                            'conditions'=>array(
                                'civilisation_id'=>$this->Session->Read('Auth.User.civilisation_id'))));
                
                ksort($lstVillage);
                $lstVillage[0] = 'Nouveau';
                
                $this->Set(compact('lstVillage'));
            }
            if(is_null($this->Session->read('Auth.User.carriere_id'))){
                $lstCarriere = $this->User->Carriere->find(
                        'list',
                        array(
                            'conditions'=>array(
                                'civilisation_id'=>$this->Session->read('Auth.User.civilisation_id'),
                                'niveau'=>0)));
                $this->Set(compact('lstCarriere'));
            }
        }
    }
    
    public function logout() {
        
        return $this->redirect($this->Auth->logout());
    }
    
    public function GetStatistiques(){
        
        $arStat = array(
            PersonnageComponent::CIVILISATION_GAULOIS    => $this->_ViewStat(PersonnageComponent::CIVILISATION_GAULOIS),
            PersonnageComponent::CIVILISATION_ROMAIN     => $this->_ViewStat(PersonnageComponent::CIVILISATION_ROMAIN)
                );
        
        
        return array_merge($arStat);
    }
    protected function _ViewStat($strCivilisation){
        
        $data = $this->User->Civilisation->find(
                'first',
                array(
                    'conditions'=>array(
                        'Civilisation.name'=>$strCivilisation
                        )
                    )
                );

        return intval($data['Civilisation']['user_count']);
        
//        return $db->NbLigne("SELECT id FROM table_joueurs WHERE civilisation='".$civilisation."';");
    }
}

?>
