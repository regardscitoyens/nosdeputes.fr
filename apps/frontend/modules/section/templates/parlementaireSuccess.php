<h1>Interventions de <?php echo link_to($parlementaire->nom, '@parlementaire?slug='.$parlementaire->slug); ?></h1>
<?php
echo include_component('section', 'parlementaire', array('parlementaire'=>$parlementaire)); 