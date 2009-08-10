<div class="temp">
<h1><?php if ($section->getSection()) 
  echo link_to($section->getSection()->titre, '@section?id='.$section->section_id).' > '; 
echo $section->titre;
?></h1>

<div>
<p>Voici la liste des mots clés pour cette section :</p>
<?php echo include_component('tag', 'tagcloud', array('tagquery' => $qtag, 'model' => 'Intervention', 'route' => '@tag_section_interventions?section='.$section->id.'&')); ?>
</div>

<div>
Voici l'organisation du projet :
<ul>
<?php foreach($section->getSubSections() as $subsection) :
if ($subsection->nb_interventions && $subsection->id != $section->id) : ?>
<li><?php echo link_to($subsection->titre, '@section?id='.$subsection->id); ?></li>
<?php endif; endforeach;?>
</ul>
</div>


<div>
Voici la liste des séances pour cette section : 
<ul>
<?php foreach($section->getSeances() as $seance) : ?>
<li><?php echo link_to($seance->getDate().', '.$seance->getMoment(), '@interventions_seance?seance='.$seance->id.'#table_'.$section->id); ?></li>
<?php endforeach; ?>
</ul>
</div>
<div>
Voici la liste des principaux orateurs pour cette section :
<? echo include_component('parlementaire', 'list', array('parlementairequery' => $ptag, 'route'=>'@parlementaire_texte?id='.$section->id.'&slug=')); ?>
</div>
</div>