<h1>Carte des circonscriptions</h2>
<?php $sf_response->setTitle('Carte des circonscriptions'); ?>
<p><?php CirconscriptionActions::echoCircoMap("full", 900, 0); ?></p>
<p><?php echo link_to('Version texte', '@list_parlementaires_circo'); ?></p>
