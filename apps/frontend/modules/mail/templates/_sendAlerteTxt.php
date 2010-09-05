============ Alerte NosDeputes.fr ============

Voici les dernières alertes : « <?php echo $alerte->titre; ?> »

<?php
foreach ($results['docs'] as $res) 
{
  echo $res['object_name'].' de '.$res['object']->getPersonne()."\n\n";
  echo implode('...', $res['text'])."\n";
  echo $res['object']->getLink()."\n\n";
}

?>

===============================================
Pour supprimer cette alerte :
<?php echo $alerte->giveVerif(); ?>