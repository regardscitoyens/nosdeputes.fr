<?php use_helper('Text');
if (count(array_keys($parlementaires))) {
  echo '<div class="plot_organisme">';
  echo include_component('plot', 'groupes', array('plot' => 'organisme_'.$orga->id, 'membres' => $parlementaires, 'nolegend' => true));
  echo '</div>';
} ?>
<div class="organisme_header">
  <h4><a href="<?php echo url_for('@list_organismes_type?type='.$orga->type); ?>"><?php echo $human_type; ?></a></h4>
  <h1><?php echo $orga->getNom(); ?></h1>
<?php
if ($total && $pagerSeances->getPage() == 1 && ($pagerRapports->getPage() == 1)) {
  echo '<h2>'.$detailed_type.'</h2>';
}
 ?>
</div>
<?php
if (isset($pagerRapports)) $nrap = $pagerRapports->getNbResults();
else $nrap = 0;
if (isset($pagerSeances)) $nse = $pagerSeances->getNbResults();
else $nse = 0;
$ndep = 0;
if ($page === "home") {
  echo '<div class="article_organisme">';
  include_component('article', 'show', array('categorie'=>'Organisme', 'object_id'=>$orga->id));
  echo '</div>';
  $divclass = "";
  $colonnes = 3;
  if ($nse || $nrap) {
    $divclass = '<div class="listeleft">';
    $colonnes = 2;
  }
  echo $divclass.'<div class="liste">';
  $listimp = array_keys($parlementaires);

  foreach ($listimp as $i) {
    $ndep += count($parlementaires[$i]);
    echo '<div class="list_table">';
    include_partial('parlementaire/table', array('deputes' => $parlementaires[$i], 'list' => 1, 'colonnes' => $colonnes, 'imp' => $i));
    echo '</div>';
  }
  echo '</div>';
}
if ($page === "home" && ($nse || $nrap))
  echo '</div><div class="listeright">';
else echo '<div>';
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
  include_partial('parlementaire/paginate', array('pager'=>$pagerRapports, 'link'=>url_for('@list_parlementaires_organisme?slug='.$orga->getSlug())."?"));
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
