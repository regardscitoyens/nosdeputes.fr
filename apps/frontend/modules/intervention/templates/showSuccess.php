<h1>Intervention de <?php echo $intervention->getParlementaire()->nom; ?></h1>
<?php 
$section = $intervention->getSection();
$titre2 = $intervention->getSeance()->getTitre(0,0,$intervention->getMd5());
$titre2 .= ' <br/> ';
if ($intervention->getType() == 'commission') {
  $orga = $intervention->getSeance()->getOrganisme();
  $titre2 .= link_to($orga->getNom(), '@list_parlementaires_organisme?slug='.$orga->getSlug());
 }
$titre2 .= link_to(ucfirst($section->getSection()->getTitre()), '@section?id='.$section->section_id);
$titre2 .= ' &nbsp; ';
if ($section->getTitre())
  $titre2 .= link_to(ucfirst($section->getTitre()), '@section?id='.$section->id);
if(count($amdmts) >= 1)
  $titre2 .= ', amendement';
if(count($amdmts) > 1) $titre2 .= 's';
$titre2 .= ' ';
foreach($amdmts as $amdmt)
$titre2 .= link_to($amdmt, '/amendements/'.(implode(',',$lois).'/'.$amdmt)).' ';

?>
<h2><?php echo $titre2 ; ?></h2>
<div class="interventions">
  <?php echo include_component('intervention', 'parlementaireIntervention', array('intervention' => $intervention, 'complete' => true, 'lois' => $lois, 'amdmts' => $amdmts, 'section'=>$section)); ?>
</div>
