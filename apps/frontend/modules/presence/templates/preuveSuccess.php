<?php $titre = 'Preuve de présence de '.$parlementaire->nom.' à la '.$seance->getTitre(1);
$sf_response->setTitle($titre); ?>
<h1><?php echo $titre; ?></h1>
<ul><?php foreach($preuves as $preuve) { ?>
  <li><?php if (preg_match('/http/', $preuve->source)) echo link_to('Compte-rendu de séance', $preuve->source);
       else echo $preuve->source; ?></li>
<?php } ?></ul>