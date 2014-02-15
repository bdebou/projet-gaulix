<?php
    $this->Extend('/common/regles');
    $this->set('title_for_layout', ' - Connection');

    $this->start('LoginPart');
        echo $this->Element('eLogin');
    $this->End('LoginPart');
?>
    