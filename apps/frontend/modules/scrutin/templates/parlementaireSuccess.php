<?php
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => "Votes") );
?>

<?php
echo include_component('scrutin', 'parlementaire', array('parlementaire' => $parlementaire));
?>