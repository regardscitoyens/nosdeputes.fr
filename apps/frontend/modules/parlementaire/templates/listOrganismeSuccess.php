<?php use_helper('Text') ?>
<h1><?php echo $orga->getNom(); $sf_response->setTitle($orga->getNom()); ?></h1>
<?php if (isset($pagerRapports)) $nrap = $pagerRapports->getNbResults();
else $nrap = 0;
if (isset($pagerSeances)) $nse = $pagerSeances->getNbResults();
else $nse = 0;
if ($page === "home") {
  include_component('article', 'show', array('categorie'=>'Organisme', 'object_id'=>$orga->id));
  $divclass = "";
  $colonnes = 3;
  if ($nse || $nrap) {
    $divclass = '<div class="listeleft">';
    $colonnes = 2;
  }
  if ($total && $pagerSeances->getPage() == 1 && ($pagerRapports->getPage() == 1)) {
    if ($orga->type == 'extra')
      echo '<h2>Organisme extra-parlementaire composé de '.$total.' député'.($total > 1 ? 's' : '').'&nbsp;:</h2>';
    else echo '<h2>'.(preg_match('/commission/i', $orga->getNom()) ? 'Comm' : 'M').'ission parlementaire composée de '.$total.' député'.($total > 1 ? 's' : '').'&nbsp;:</h2>';
  }
  echo $divclass.'<div class="liste">';
  $listimp = array_keys($parlementaires);
  foreach ($listimp as $i) {
    echo '<div class="list_table">';
    include_partial('parlementaire/table', array('deputes' => $parlementaires[$i], 'list' => 1, 'colonnes' => $colonnes, 'imp' => $i));
    echo '</div>';
  }
  echo '</div>';
}
if ($page === "home" && ($nse || $nrap))
  echo '</div><div class="listeright">';
else echo '<div>';
if (count($parlementaires)) {
  echo '<div class="plot_seance aligncenter">';
  echo include_component('plot', 'groupes', array('plot' => 'organisme_'.$orga->id, 'membres' => $parlementaires, 'nolegend' => true));
  echo '</div>';
}
if ($page != "seances" && $nrap) {
  echo '<h3>';
  if ($page === "home")
    echo 'Ses derniers rapports';
  else echo 'Rapports de la '.(preg_match('/commission/i', $orga->getNom()) ? 'comm' : 'm').'ission';
  echo '&nbsp;:</h3><ul>';
  $curid = 0;
  foreach($pagerRapports->getResults() as $rap) {
    $shortid = preg_replace('/-[atv].*$/', '', preg_replace('/[A-Z]/', '', $rap->id));
    if ($curid != $shortid) {
      echo '<li>';
      $curid = $shortid;
      $doctitre = preg_replace('/ (de|pour|par) l[ea\'\s]+ '.$orga->nom.'/i', '', $rap->getTitreCommission());
      $doctitre = preg_replace('/ (de|pour|par) l[ea\'\s]+ '.preg_replace('/\'\s*/', '’', $orga->nom).'/i', '', $rap->getTitreCommission());
      $doctitre = preg_replace('/ (de|pour|par) l[ea\'\s]+ '.preg_replace('/’\s*/', '\'', $orga->nom).'/i', '', $rap->getTitreCommission());
      if ($pagerRapports->getPage() == 1) $doctitre = truncate_text($doctitre, 120);
      echo link_to($doctitre, '@document?id='.$curid).'</li>';
    }
  }
  echo '</ul>';
  include_partial('parlementaire/paginate', array('pager'=>$pagerRapports, 'link'=>'@list_parlementaires_organisme?slug='.$orga->getSlug().'&'));
}
if ($page != "rapports" && $nse) {
  echo '<h3>';
  if ($page === "home")
    echo 'Ses dernières réunions';
  else echo 'Réunions de la '.(preg_match('/commission/i', $orga->getNom()) ? 'comm' : 'm').'ission';
  echo '&nbsp;:</h3><ul>';
  $curdate = "";
  foreach($pagerSeances->getResults() as $seance) {
    $newdate = myTools::displayDate($seance->date);
    if ($curdate != $newdate) {
      if ($curdate != "")
        echo '</li>';
      $curdate = $newdate;
      echo '<li>'.$newdate.'&nbsp;: ';
    } else echo '&nbsp;&mdash; ';
    $subtitre = $seance->getShortMoment();
    if ($seance->nb_commentaires > 0) {
      $subtitre .= ' (<span class="list_com">'.$seance->nb_commentaires.' commentaire';
      if ($seance->nb_commentaires > 1) $subtitre .= 's';
      $subtitre .= '</span>)';
    }
    echo link_to($subtitre, '@interventions_seance?seance='.$seance->id);
  }
  echo '</ul>';
  include_partial('intervention/paginate', array('pager'=>$pagerSeances, 'link'=>'@list_parlementaires_organisme?slug='.$orga->getSlug().'&')); 
} ?>
</div>
<?php if ($page != "home") echo '<h3 class="aligncenter">'.link_to('Voir la composition de la commission', '@list_parlementaires_organisme?slug='.$orga->slug).'</h3>'; ?>
