<h1><?php if ($orga = $seance->getOrganisme()) echo link_to($orga->getNom(), '@list_parlementaires_organisme?slug='.$orga->getSlug()); ?></h1>
<h2>Députés présents à la <?php echo link_to($seance->getTitre(1), '@interventions_seance?seance='.$seance->id); ?>&nbsp;:</h2>
<div class="plot_seance">
<?php $titre = 'Députés présents à la '.$seance->getTitre(1);
if (isset($orga)) $titre = $orga->getNom().$titre;
$sf_response->setTitle($titre);
if ($seance->type == 'commission') 
  echo include_component('plot', 'groupes', array('plot' => 'seance_com_'.$seance->id)); ?>
</div>
  <div class="photos"><p>
  <?php $ntot = count($presences); $line = floor($ntot/(floor($ntot/16)+1)); $ct = 0; foreach ($presences as $presence) {
    $depute = $presence->getParlementaire();
    $titre = $depute->nom.', '.$depute->groupe_acronyme;
    echo '<a href="'.url_for($depute->getPageLink()).'"><img width="50" height="64" title="'.$titre.'" alt="'.$titre.'" src="'.url_for('@resized_photo_parlementaire?height=70&slug='.$depute->slug).'" /></a>&nbsp;';
    if ($ct != 0 && !($ct % $line)) echo '<br/>'; $ct++;
  } ?></p></div>

<ul>
<?php $titre = 0; foreach($presences as $presence) : ?>
<?php $p = $presence->getParlementaire(); ?>
<?php $nbpreuves = $presence->getNbPreuves(); 
    if ($titre == 0) {
      if ($nbpreuves == 1)
        $titre = -1;
      else {
        $titre++;
        echo '<li>Participants&nbsp;:<ul>';
      }
    } else if ($titre == 1 && $nbpreuves == 1) {
      $titre++;
      echo '</ul><li>Non-participants&nbsp;:<ul>';
    } ?>
<li><?php echo link_to($p->nom, '@parlementaire?slug='.$p->getSlug()).', '.$p->getLongStatut(1); ?> <em><a href="<?php echo url_for('@preuve_presence_seance?seance='.$seance->id.'&slug='.$p->slug); ?>">(<?php echo ($nbpreuves>1) ? "$nbpreuves preuves" : "1 preuve"; ?>)</a></em></li>
<?php endforeach;
  if ($titre == 2) echo '</ul>'; ?>
</ul>
