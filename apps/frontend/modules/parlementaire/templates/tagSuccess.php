<?php if (!$tag) { ?>
<h1>Tous les députés par mots-clés prononcés</h1>
<div class="liste_deputes_tags">
<?php $sf_response->setTitle('Trouver un député par mots-clés prononcés');
 echo include_component('tag', 'tagcloud', array('querytag'=>$tquery,'route'=>'@tag_result_parlementaires?', 'limit'=>500)); 
 echo "</div>";
 return;
 }?>
<h1>Les députés spécialistes de "<?php echo $tag; ?>"</h1>
<div><ul>
<?php $sf_response->setTitle('Les députés spécialistes de "'.$tag.'"');
foreach($parlementaires as $inter) {
  echo '<li>'.link_to($inter['Parlementaire']['nom'], '@tag_parlementaire_interventions?parlementaire='.$inter['Parlementaire']['slug'].'&tags='.$tag).' ('.$inter['nb'].' interventions)</li>';
}
?></ul>
</div>
