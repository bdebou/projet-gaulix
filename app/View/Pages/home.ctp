<?php
if(!$this->Session->read('Auth.User.id')){
    $this->Extend('/common/regles');
    $this->set('title_for_layout', ' - Home');
    
    $this->start('LoginPart');
        echo $this->Element('eLogin');
    $this->End('LoginPart');
}else{
    $this->extend('/common/main');
    $this->set('title_for_layout', ' - Actions');
    
    $this->Start('LoginPart');
        echo $this->Element('NavigationBar', array('Active'=>'main'));
        echo $this->Element('eLoged');
    $this->end('LoginPart');
}
?>
