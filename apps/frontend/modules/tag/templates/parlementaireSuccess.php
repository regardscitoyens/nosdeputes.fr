<?php
$titre = 'Champ lexical';
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
?>
  <div class="par_session"><p>
<?php if (! $parlementaire->fin_mandat) { 
   if (!$last) 
    echo '<a href="'.url_for('@parlementaire_tags?slug='.$parlementaire->slug).'">';
   echo 'Les 12 derniers mois';
   if (!$last)
     echo '</a>';
   echo ', ';  
 } 
if (!$all)
  echo '<a href="'.url_for('@parlementaire_all_tags?slug='.$parlementaire->slug).'">';
echo 'tout son mandat';
if (!$all)
  echo '</a>'; 
foreach ($sessions as $s) {
  echo ', ';
  if ($s['session'] != $session)
    echo '<a href="'.url_for('@parlementaire_session_tags?slug='.$parlementaire->slug.'&session='.$s['session']).'">';
  echo 'session '.preg_replace('/^(\d{4})/', '\\1-', $s['session']);
  if ($s['session'] != $session)
    echo '</a>';
}?>
</div>
   <?php echo include_component('tag', 'tagcloud', array('tagquery' => $qtag, 'model' => 'Intervention', 'min_tag' => 2, 'route' => '@tag_parlementaire_interventions?parlementaire='.$parlementaire->slug.'&', 'limit'=>1000)); ?>
