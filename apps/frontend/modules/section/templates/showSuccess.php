   <h1><?php 
   $titre = '';
if ($section->getSection()) {
  echo link_to($section->getSection()->getTitre(), '@section?id='.$section->section_id).'</h1><h2>';
  $titre = ', '.$section->getSection()->getTitre();
 }
echo $section->titre;
$titre = $section->titre.$titre;
$sf_response->setTitle($titre);
if ($section->getSection()) echo '</h2>';
else echo '</h1>';
?>

<div class="numeros_textes">
<?php if ($lois && ! preg_match('/(questions?\s|ordre\sdu\sjour|nomination|suspension\sde\séance|rappels?\sau\srèglement)/i', $section->titre)) { ?>
<span>Texte<?php if (count($lois) > 1) echo 's'; ?> de loi<?php if (count($lois) > 1) echo 's'; ?> N°
<?php foreach ($lois as $loi) echo myTools::getLinkLoi($loi).' '; ?>
<br/>
<?php echo link_to('Tous les amendements à ce dossier',  '@find_amendements_by_loi_and_numero?loi='.urlencode(implode(',',$lois_amendees)).'&numero=all').'&nbsp;: ';
foreach ($lois_amendees as $loi) echo link_to('loi N°'.$loi, '@find_amendements_by_loi_and_numero?loi='.$loi.'&numero=all').' ('.myTools::getLiasseLoiImpr($loi).', '.myTools::getLiasseLoiAN($loi).') '; ?>
</span>
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
<li><?php echo link_to($subsection->titre, '@interventions_seance?seance='.$subsection->getFirstSeance().'#table_'.$subsection->id); ?></li>
<?php endif; endforeach;?>
</ul>
</div>
<div class="left">
<?php } else echo '<div>'; ?>
<div class="seances_dossier">
<h2>Toutes les séances consacrées à ce dossier</h2>
<ul>
<?php foreach($section->getSeances() as $seance) : ?>
<li><?php echo link_to($seance->getTitre(), '@interventions_seance?seance='.$seance->id.'#table_'.$section->id); ?></li>
<?php endforeach; ?>
</ul>
</div>
<div class="orateurs_dossier">
<h2>Tous les orateurs sur ce dossier :</h2>
<?php echo include_component('parlementaire', 'list', array('parlementairequery' => $ptag, 'route'=>'@parlementaire_texte?id='.$section->id.'&slug=')); ?>
</div>
</div>
