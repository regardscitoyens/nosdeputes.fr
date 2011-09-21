<?php if (!$tag) {
   $titre = 'Trouver un député par mot-clé'; ?>
<h1><?php echo $titre; ?></h1>
<div class="liste_deputes_tags">
<?php echo include_component('tag', 'tagcloud', array('querytag'=>$tquery,'route'=>'@tag_result_parlementaires?', 'limit'=>500)); 
 echo "</div>";
 return;
 } else  $titre = 'Les députés spécialistes de "'.$tag.'"';?>
<h1><?php echo $titre; ?></h1>
<?php $sf_response->getTitle($titre); ?>
<div><ul>
<?php $sf_response->setTitle('Les députés spécialistes de "'.$tag.'"');
foreach($parlementaires as $inter) {
  echo '<li>'.link_to($inter['Parlementaire']['nom'], '@tag_parlementaire_interventions?parlementaire='.$inter['Parlementaire']['slug'].'&tags='.$tag).' (<span class="list_inter">'.$inter['nb'].' intervention'.($inter['nb'] > 1 ? 's' : '').'</span>)</li>';
}
?></ul>
</div>
