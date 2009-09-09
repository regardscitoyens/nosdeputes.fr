<div class="temp">
<?php
$titre = "Graphes d'activité parlementaire";
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
?>
<?php echo include_component('plot', 'parlementairePresence', array('parlementaire' => $parlementaire, 'options' => array('plot' => 'all', 'fonctions' => 'on', 'questions' => 'on', 'session' => $session))); ?>
</div>