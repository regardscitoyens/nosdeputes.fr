   <h1><?php 
   $titre = '';
if ($section->getSection()) {
  echo link_to($section->getSection()->getTitre(), '@section?id='.$section->section_id).'</h1><h2>';
  $titre = ', '.$section->getSection()->getTitre();
 }
echo $section->titre;
$titre = $section->titre.$titre;
$sf_response->setTitle($titre.' - NosDéputés.fr');
if ($section->getSection()) echo '</h2>';
else echo '</h1>';
?>
<div class="numeros_textes">
<?php if ($section->nb_commentaires) { ?>
<div class="source"><span class="list_com"><a href="#commentaires">Voir le<?php if ($section->nb_commentaires > 1) echo 's '.$section->nb_commentaires; ?> commentaire<?php if ($section->nb_commentaires > 1) echo 's'; ?></a></span></div>
<?php }
  if ($lois && ! preg_match('/(questions?\s|ordre\sdu\sjour|nomination|suspension\sde\séance|rappels?\sau\srèglement)/i', $section->titre)) { ?>
<span><?php if (count($textes_loi)) foreach ($textes_loi as $texte) echo link_to(strip_tags($texte['titre']), '@loi?loi='.$texte['texteloi_id']); else { echo 'Texte'; if (count($lois) > 1) echo 's'; echo ' N°'; foreach ($lois as $loi) echo myTools::getLinkLoi($loi).' '; echo '('.link_to('tous les amendements à ce dossier',  '@find_amendements_by_loi_and_numero?loi='.urlencode(implode(',',$lois_amendees)).'&numero=all').')'; } ?></span>
<?php } ?>
</div>
<div class="resume">
<div class="right">
<div class="nuage_de_tags">
<h3>Mots-clés de cette section</h3>
  <?php echo include_component('tag', 'tagcloud', array('hide'=>1, 'tagquery' => $qtag, 'model' => 'Intervention', 'limit'=>40, 'route' => '@tag_section_interventions?section='.$section->id.'&')); ?>
</div>
</div>
<div class="left">
<div class="plot_section">
<?php echo include_component('plot', 'groupes', array('plot' => 'section_'.$section->id)); ?>
</div>
</div>
</div>

<?php $sommaire = $section->getSubSections();
if (count($sommaire)) { ?>
<div class="orga_dossier right">
<h2>Organisation du dossier</h2>
<ul>
<?php foreach($section->getSubSections() as $subsection) :
if ($subsection->id != $section->id) : ?>
<li><?php $subtitre = $subsection->titre;
  if ($subsection->nb_commentaires > 0) {
    $subtitre .= ' (<span class="list_com">'.$subsection->nb_commentaires.' commentaire';
    if ($subsection->nb_commentaires > 1) $subtitre .= 's';
    $subtitre .= '</span>)';
  }
  echo link_to($subtitre, '@interventions_seance?seance='.$subsection->getFirstSeance().'#table_'.$subsection->id); ?></li>
<?php endif; endforeach;?>
</ul>
</div>
<div class="left">
<?php } else echo '<div>'; ?>
<div class="seances_dossier">
<h2>Toutes les séances consacrées à ce dossier</h2>
<ul>
<?php foreach($section->getSeances() as $seance) : ?>
<li><?php $subtitre = $seance->getTitre();
  if ($seance->nb_commentaires > 0) {
    $subtitre .= ' (<span class="list_com">'.$seance->nb_commentaires.' commentaire';
    if ($seance->nb_commentaires > 1) $subtitre .= 's';
    $subtitre .= '</span>)';
  }
  echo link_to($subtitre, '@interventions_seance?seance='.$seance->id.'#table_'.$section->id); ?></li>
<?php endforeach; ?>
</ul>
</div>
<div class="orateurs_dossier">
<h2>Tous les orateurs sur ce dossier :</h2>
<?php echo include_component('parlementaire', 'list', array('parlementairequery' => $ptag, 'route'=>'@parlementaire_texte?id='.$section->id.'&slug=')); ?>
</div>
</div>
<?php if ($section->nb_commentaires != 0) { ?>
  <div class="stopfloat"></div>
  <div class="commentaires" id="commentaires">
    <h2>Derniers commentaires sur <?php echo $section->titre; ?> <span class="rss"><a href="<?php echo url_for('@section_rss_commentaires?id='.$section->id); ?>"><?php echo image_tag('xneth/rss.png', 'alt="Flux rss"'); ?></a></span></h2>
<?php echo include_component('commentaire', 'lastObject', array('object' => $section, 'presentation' => 'nodossier'));
    if ($section->nb_commentaires > 4)
      echo '<p class="suivant">'.link_to('Voir les '.$section->nb_commentaires.' commentaires', '@section_commentaires?id='.$section->id).'</p><div class="stopfloat"></div>'; ?>
</div>
<?php } ?>
