<?php if ($article) {
  echo $article->corps;
  echo '<p style="text-align:right"><a href="'.link_tof('doc_organisme_edit', array('article_id' => $article->id)).'">Editer</a> &mdash; <a href="'.link_tof('article_delete', array('article_id' => $article->id)).'">Supprimer</a></p>';
} ?>
<div class="sf_admin_list"><h2>
<?php if (!count($seances)) echo "Pas de séance associée</h2>";
else {
  echo count($seances).' séances</h2>';
  if (isset($nofuse)) $options = array('seances' => $seances, 'nofuse' => $nofuse);
  else $options = array('seances' => $seances, 'orga' => $orga);
  include_partial('seance/listCommission', $options);
}
echo '<h2>';
$ct = count($deputes);
if ($ct == 0) echo 'Aucun député associé';
else {
  echo '<h2>'.$ct.' députés inscrits&nbsp;:</h2><p style="text-align:center;">';
  for ($i=0; $i<$ct; $i++) {
    echo '<a href="'.link_tof('parlementaire', array('slug' => $deputes[$i]['slug'])).'">'.$deputes[$i]['nom'].'</a>';
    if ($i != $ct-1) echo ' &nbsp;&mdash ';
  }
  echo '</p>';
} ?>

