<div class="commentaire">
<p>
<?php
echo $commentaire;
?>
</p>
</div>
<?php
echo include_component('commentaire', 'form', array('type'=>$type, 'id'=>$id, 'sendButton'=>1, 'form'=>$form, 'follow_talk' => $follow_talk));
?>