<?php echo include_component('tag', 'tagcloud', array('tagquery' => $itag, 'model'=>'Intervention', 'min_tag'=>2, 'limit'=>90, 'route'=>'@tag_interventions?')); ?>
