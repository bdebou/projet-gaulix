<?php
$this->Extend('/common/regles');
$this->set('title_for_layout', ' - Règles');

if(!$this->Session->read('Auth.User.id')){
    $this->start('LoginPart');
        echo $this->Element('eLogin');
    $this->End('LoginPart');
}else{
    $this->Start('LoginPart');
        echo $this->Element('NavigationBar', array('Active'=>'regles'));
        echo $this->Element('eLoged');
    $this->end('LoginPart');
}

