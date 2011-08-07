<?php
$surtitre = 'Champ lexical';
if ($last) $titre = 'Sur les 12 derniers mois';
else if ($all) $titre = 'Sur tout son mandat';
else if ($session) $titre = 'Sur la session '.preg_replace('/^(\d{4})/', '\\1-', $session);
$sf_response->setTitle($surtitre.' de '.$parlementaire->nom.' '.strtolower($titre));
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $surtitre));
?>
  <div class="par_session"><p>
<?php if (! $parlementaire->fin_mandat) { 
   if (!$last) 
    echo '<a href="'.url_for('@parlementaire_tags?slug='.$parlementaire->slug).'">';
   else echo '<b>';
   echo 'Les 12 derniers mois';
   if (!$last)
     echo '</a>';
   else echo '</b>';
   echo ', ';  
 } 
if (!$all)
  echo '<a href="'.url_for('@parlementaire_all_tags?slug='.$parlementaire->slug).'">';
else echo '<b>';
echo 'tout son mandat';
if (!$all)
  echo '</a>'; 
else echo '</b>';
foreach ($sessions as $s) {
  echo ', ';
  if ($s['session'] != $session)
    echo '<a href="'.url_for('@parlementaire_session_tags?slug='.$parlementaire->slug.'&session='.$s['session']).'">';
  else echo '<b>';
  echo 'session '.preg_replace('/^(\d{4})/', '\\1-', $s['session']);
  if ($s['session'] != $session)
    echo '</a>';
  else echo '</b>';
}?></p>
</div>
   <?php echo include_component('tag', 'tagcloud', array('tagquery' => $qtag, 'model' => 'Intervention', 'min_tag' => 2, 'route' => '@tag_parlementaire_interventions?parlementaire='.$parlementaire->slug.'&', 'limit'=>1000)); ?>
