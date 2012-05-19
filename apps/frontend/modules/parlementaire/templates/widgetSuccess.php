<?php if ($parl) include_component('parlementaire', 'widget', array('slug' => $parl, 'options' => $options));
else echo '<span>Aucun député trouvé pour « '.$search.' ».</span>'; ?>
