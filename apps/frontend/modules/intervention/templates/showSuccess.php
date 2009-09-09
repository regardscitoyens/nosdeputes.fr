<?php echo include_component('intervention', 'parlementaireIntervention', array('intervention' => $intervention, 'complete' => true));

echo include_component('commentaire', 'show', array('object'=>$intervention));
echo include_component('commentaire', 'form', array('object'=>$intervention));
