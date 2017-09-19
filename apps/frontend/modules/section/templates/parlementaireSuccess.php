<?php
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
echo include_component('section', 'parlementaire', array('parlementaire'=>$parlementaire));
