<?php $titre = 'Preuve de présence de '.$parlementaire->nom.' à la '.$seance->getTitre(1);
$sf_response->setTitle($titre); ?>
<h1><?php echo $titre; ?></h1>
<ul><?php foreach($preuves as $preuve) { ?>
  <li><?php if ($preuve->type === 'compte-rendu') echo 'Liste des présences au bas du ';
       if ($preuve->type === 'compte-rendu' || $preuve->type === 'intervention') echo link_to('Compte-rendu de séance', $preuve['source']);
       else if ($preuve->type === 'jo' && (preg_match('/(\d{2}\/\d{2}\/\d{4})$/', $preuve->source, $match))) echo '<a href="http://www.journal-officiel.gouv.fr/users.php?date_jo='.$match[1].'">'.$preuve->source.'</a>';
       else echo $preuve->source; ?></li>
<?php } ?></ul>
