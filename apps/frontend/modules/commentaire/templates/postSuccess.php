<?php
echo $commentaire;
echo include_component('commentaire', 'form', array('type'=>$type, 'id'=>$id, 'sendButton'=>1, 'form'=>$form));