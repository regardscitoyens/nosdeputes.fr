============ Alerte NosDeputes.fr ============

Voici les dernières alertes de votre abonnement : « <?php echo $alerte->titre; ?> »

<?php
foreach ($results['docs'] as $res) 
{
  echo "- ".$res['object_name'].' de '.$res['object']->getPersonne()."\n";
  $printable = array();
  foreach($res['text'] as $text) {
    if (!preg_match('/=/', $text))
      array_push($printable , $text);
  }
  echo "  ".implode('...', $printable)."\n";
  echo sfConfig::get('app_base_url').preg_replace('/symfony\/?/', '', $res['object']->getLink())."\n\n";
}

?>
===============================================
Pour éditer cette alerte :
<?php echo sfConfig::get('app_base_url').preg_replace('/symfony\/?/', '', url_for('alerte/edit?verif='.$alerte->getVerif())); ?>

Pour supprimer cette alerte :
<?php echo sfConfig::get('app_base_url').preg_replace('/symfony\/?/', '', url_for('alerte/delete?verif='.$alerte->getVerif())); ?>

L'interface vous permettant de gérer vos alertes :
<?php echo sfConfig::get('app_base_url').preg_replace('/symfony\/?/', '', url_for('alerte/list')); ?>
