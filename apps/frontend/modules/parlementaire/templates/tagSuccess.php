<?php
if (!$tag) {
 echo include_component('tag', 'tagcloud', array('querytag'=>$tquery,'route'=>'@tag_result_parlementaires?', 'limit'=>500)); 
 return;
 }

foreach($parlementaires as $p) {
  echo '<li>'.link_to($p['nom'], '@tag_parlementaire_interventions?parlementaire='.$p['slug'].'&tags='.$tag).'('.$p['nb'].')</li>';
}