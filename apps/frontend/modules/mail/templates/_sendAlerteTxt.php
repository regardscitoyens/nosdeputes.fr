============ Alerte NosDeputes.fr ============

Voici les dernières alertes de votre abonnement : <?php echo $alerte->titre; ?> 

<?php
foreach ($results['response']['docs'] as $res) 
{
  $titre = $res['object']->getTitre();
  if ($res['object_name'] === "Commentaire")
    echo "Commentaire : ";
  else if ($res['object_name'] === "Section")
    echo "Dossier : ";
  else if ($res['object_name'] === "QuestionEcrite")
    $titre = str_replace('Question', 'Question écrite', $titre);
  echo $titre."\n";
  echo "------------------------------------------------\n";
 if ($res['object_name'] != 'Texteloi') {
  $printable = array();
  $brut = $res['text'];
  foreach($brut as $text) {
    if (!preg_match('/=/', $text))
      array_push($printable , $text);
  }

  $text = '';
  if (!isset($nohuman) || !$nohuman) {
    $text = preg_replace('/^[^a-z]/i', '...', strip_tags(preg_replace('/ *\n+ */', ' ', implode('...', $results['highlighting'][$res['id']]['text']))));
  }

  if (!$text) {
    $text = preg_replace('/ *\n+ */', ' ', implode('...', $printable));
  }

  $text = html_entity_decode($text);
  $text = preg_replace('/\&\#[0-9]+\;/', '', $text);

  if (strlen($text) > 700) {
	$text = preg_replace('/[^ ]*$/', '', substr($text, 0, 700)).'...';
  }

  $text = str_replace($titre, '', $text);
  echo "$text\n";
 }
  echo sfConfig::get('app_base_url').'/'.preg_replace('/symfony\/?/', '', $res['object']->getLink())."\n\n";
}

?>
===============================================
Pour éditer cette alerte :
<?php echo sfConfig::get('app_base_url').'/'.preg_replace('/symfony\/?/', '', url_for('alerte/edit?verif='.$alerte->getVerif())); ?>

Pour supprimer cette alerte :
<?php echo sfConfig::get('app_base_url').'/'.preg_replace('/symfony\/?/', '', url_for('alerte/delete?verif='.$alerte->getVerif())); ?>

<?php if ($alerte->citoyen_id) : ?>
L'interface vous permettant de gérer vos alertes :
<?php echo sfConfig::get('app_base_url').'/'.preg_replace('/symfony\/?/', '', url_for('alerte/list')); ?>
<?php endif; ?>
