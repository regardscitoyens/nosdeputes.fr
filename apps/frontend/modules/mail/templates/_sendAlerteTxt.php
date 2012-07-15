============ Alerte NosDeputes.fr ============

Voici les dernières alertes de votre abonnement : <?php echo $alerte->titre; ?> 

<?php
foreach ($results['response']['docs'] as $res) 
{
  $citoyen = "";
  $titre = $res['object']->getTitre();
  if ($res['object_name'] === "Commentaire") {
    foreach ($res['tag'] as $tag) if (preg_match("/Citoyen=(.*)$/", $tag, $match))
      $citoyen .= " par ".$match[1];
    echo "Commentaire$citoyen : ";
  } else if ($res['object_name'] === "Section")
    echo "Dossier : ";
  else if ($res['object_name'] === "QuestionEcrite")
    $titre = str_replace('Question', 'Question écrite', $titre);
  echo $titre."\n";
 if ($res['object_name'] != 'Texteloi') {
  echo "------------------------------------------------\n";
  $printable = array();
  $brut = $res['text'];
  foreach($brut as $text) {
    if (!preg_match('/=/', $text))
      array_push($printable , $text);
  }

  $text = '';
  if (!isset($nohuman) || !$nohuman) {
    $text = preg_replace('/^[^a-z]/i', '...', strip_tags(preg_replace('/<\/?em>/', '*', preg_replace('/ *\n+ */', ' ', implode('...', $results['highlighting'][$res['id']]['text'])))));
  }

  if (!$text) {
    $text = preg_replace('/ *\n+ */', ' ', implode('...', $printable));
  }

  $text = html_entity_decode($text);
  $text = preg_replace('/\&\#[0-9]+\;/', '', $text);
  $text = preg_replace('/\«\W/', ' " ', $text);

  if (strlen($text) > 700) {
	$text = preg_replace('/[^ ]*$/', '', substr($text, 0, 700)).'...';
  }

  $text = str_replace($titre, '', $text);
  if ($nohuman && $res['object_name'] === "Commentaire" && $citoyen)
    $text = substr($text, strlen($citoyen)-4);
  echo "$text\n";
 }
  echo sfConfig::get('app_base_url').preg_replace('/symfony\/?/', '', $res['object']->getLink())."\n\n";
}

?>
===============================================
<?php
if (!isset($nohuman) || !nohuman) {
echo "Visualiser cette alerte sur le site :\n";
echo sfConfig::get('app_base_url').preg_replace('/symfony\/?/', '', url_for('@recherche_solr?sort=1&query='.$alerte->query))."\n";
}
?>
Pour éditer cette alerte :
<?php 
echo sfConfig::get('app_base_url').preg_replace('/symfony\/?/', '', url_for('alerte/edit?verif='.$alerte->getVerif()));
?>

Pour supprimer cette alerte :
<?php echo sfConfig::get('app_base_url').preg_replace('/symfony\/?/', '', url_for('alerte/delete?verif='.$alerte->getVerif())); ?>

<?php if ($alerte->citoyen_id) : ?>
L'interface vous permettant de gérer vos alertes :
<?php echo sfConfig::get('app_base_url').preg_replace('/symfony\/?/', '', url_for('alerte/list')); ?>
<?php endif; ?>
