<h1><?php if ($orga = $seance->getOrganisme()) echo link_to($orga->getNom(), '@list_parlementaires_organisme?slug='.$orga->getSlug()); else echo "Hémicycle"?></h1>
<h2>Députés présents à la <?php echo link_to($seance->getTitre(1), '@interventions_seance?seance='.$seance->id); ?>&nbsp;:</h2>
<div class="plot_seance">
<?php if (isset($orga)) $titre = $orga->getNom();
else $titre = "Hémicycle";
$titre = $titre.' - Députés présents à la '.$seance->getTitre(1);
$sf_response->setTitle($titre);
if ($seance->type == 'commission') 
  echo include_component('plot', 'groupes', array('plot' => 'seance_com_'.$seance->id, 'nolink' => true)); ?>
</div>
  <div class="photos"><p>
  <?php $deputes = array();
    $ntot = 0;
    foreach ($presents as $presence) {
      $ntot++;
      $deputes[] = $presence->getParlementaire();
    }
    include_partial('parlementaire/photos', array('deputes' => $deputes));
  ?>
  </p></div>
<ul>
<?php $nb = count($intervenants);
$interv = array();
if ($nb > 0) {
  echo '<li>'; if ($nb == 1) echo 'Es'; else echo 'Son'; echo 't intervenu'; if ($nb > 1) echo 's'; echo '&nbsp;:<ul>';
  foreach($intervenants as $presence) {
    $p = $presence->getParlementaire();
    $interv[$p->id] = 1;
    $nbpreuves = $presence->getNbPreuves(); 
    echo '<li>'.link_to($p->nom, '@parlementaire?slug='.$p->getSlug()).', '.$p->getLongStatut(1).'<em><a href="'.url_for('@preuve_presence_seance?seance='.$seance->id.'&slug='.$p->slug).'"> ('; if ($nbpreuves > 1) echo "$nbpreuves preuves"; else echo "1 preuve"; echo ')</a></em></li>';
  }
  echo '</ul></li>';
}
$nb2 = $ntot - $nb;
if ($nb2 > 0) {
  echo '<li>Assistai'; if ($nb2 > 1) echo 'en'; echo 't à la séance&nbsp;:<ul>';
  foreach($presents as $presence) {
    $p = $presence->getParlementaire();
    if (isset($interv[$p->id])) continue;
    $nbpreuves = $presence->getNbPreuves();  
    echo '<li>'.link_to($p->nom, '@parlementaire?slug='.$p->getSlug()).', '.$p->getLongStatut(1).'<em><a href="'.url_for('@preuve_presence_seance?seance='.$seance->id.'&slug='.$p->slug).'">'; if ($nbpreuves != 0) {echo ' ('; if ($nbpreuves > 1) echo "$nbpreuves preuves"; else echo "1 preuve"; echo ')</a></em></li>'; }
  }
  echo '</ul></li>';
}
?>
</ul>
