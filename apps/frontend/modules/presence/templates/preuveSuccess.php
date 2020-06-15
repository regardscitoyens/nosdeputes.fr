<h1><?php echo $titre; ?></h1>
<ul><?php foreach($preuves as $preuve) { ?>
  <li><?php if ($preuve->type === 'compte-rendu') echo 'Liste des présences au bas du ';
       if ($preuve->type === 'compte-rendu' || $preuve->type === 'intervention') echo link_to('Compte-rendu de séance', $preuve['source']);
       else if ($preuve->type === 'scrutin') echo link_to('Scrutin public n°'.end(explode('/', $preuve['source'])), $preuve['source']);
       else if ($preuve->type === 'video') echo link_to('Vidéo de la réunion', $preuve['source']);
       else if ($preuve->type === 'jo' && (preg_match('/(\d{2})\/(\d{2})\/(\d{4})$/', $preuve->source, $match))) echo '<a href="https://www.legifrance.gouv.fr/eli/jo/'.$match[3].'/'.round($match[2]).'/'.round($match[1]).'">'.$preuve->source.'</a>';
       else echo $preuve->source; ?></li>
<?php } ?></ul>
