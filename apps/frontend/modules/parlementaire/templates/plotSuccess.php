<div class="temp">
<?php
$titre = "Graphes d'activité parlementaire";
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
?>
<?php echo include_component('plot', 'parlementaire', array('parlementaire' => $parlementaire, 'options' => array('plot' => 'all', 'fonctions' => 'on', 'questions' => 'on', 'session' => $session))); ?>
  <div class="explications" id="explications">
    <h2>Explications :</h2>
    <?php echo link_to("Présence en séances de commission et d'hémicycle",'@parlementaire_presences?slug='.$parlementaire->getSlug()); ?>
    <p><a href="/faq">plus de détails dans la section FAQ</a>
  </div>
</div>