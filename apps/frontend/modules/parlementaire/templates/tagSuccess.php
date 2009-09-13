<div class="temp">
<div class="liste_deputes_tags">
<p>Tous les députés par mots-clés prononcés</p>
<?php
if (!$tag) {
 echo include_component('tag', 'tagcloud', array('querytag'=>$tquery,'route'=>'@tag_result_parlementaires?', 'limit'=>500)); 
 echo "</div>";
 return;
 }
foreach($parlementaires as $inter) {
  echo '<li>'.link_to($inter['Parlementaire']['nom'], '@tag_parlementaire_interventions?parlementaire='.$inter['Parlementaire']['slug'].'&tags='.$tag).'('.$inter['nb'].')</li>';
}
?>
</div>
</div>
