<div class="temp">
<h1><?php if ($section->getSection()) 
  echo link_to($section->getSection()->getTitre(), '@section?id='.$section->section_id).'</h1><h2>';
echo $section->titre;
if ($section->getSection()) echo '</h2>';
else echo '</h1>';
?>
<div>
<?php if ($lois && ! preg_match('/(questions?\s|ordre\sdu\sjour|nomination|suspension\sde\séance|rappels?\sau\srèglement)/i', $section->titre)) { ?>
<span>Projet<?php if (count($lois) > 1) echo 's'; ?> de loi<?php if (count($lois) > 1) echo 's'; ?> N°
<?php foreach ($lois as $loi) echo myTools::getLinkLoi($loi).' ('.link_to('amdmts', '@find_amendements_by_loi_and_numero?loi='.$loi.'&numero=all').') '; ?>
</span>
<?php } ?>
</div>
<?php echo include_component('plot', 'groupes', array('plot' => 'section_'.$section->id)); ?>
<div>
<p>Voici la liste des mots clés pour cette section :</p>
<?php echo include_component('tag', 'tagcloud', array('tagquery' => $qtag, 'model' => 'Intervention', 'route' => '@tag_section_interventions?section='.$section->id.'&')); ?>
</div>

<div>
Voici l'organisation du projet :
<ul>
<?php foreach($section->getSubSections() as $subsection) :
if ($subsection->id != $section->id) : ?>
<li><?php echo link_to($subsection->titre, '@section?id='.$subsection->id); ?></li>
<?php endif; endforeach;?>
</ul>
</div>


<div>
Voici la liste des séances pour cette section : 
<ul>
<?php foreach($section->getSeances() as $seance) : ?>
<li><?php echo link_to($seance->getTitre(), '@interventions_seance?seance='.$seance->id.'#table_'.$section->id); ?></li>
<?php endforeach; ?>
</ul>
</div>
<div>
Voici la liste des principaux orateurs pour cette section :
<?php echo include_component('parlementaire', 'list', array('parlementairequery' => $ptag, 'route'=>'@parlementaire_texte?id='.$section->id.'&slug=')); ?>
</div>
</div>
