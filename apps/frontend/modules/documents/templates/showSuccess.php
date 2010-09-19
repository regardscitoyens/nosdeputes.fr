<div class="source"><?php if ($section) echo link_to('Dossier relatif', '@section?id='.$section->id); else echo '<a href="'.$doc->url_an.'">Dossier sur le site de l\'Assemblée</a>'; ?></div>
<h1 class="orange"><?php echo preg_replace('/(N°\s\d+[,\s])/', '\\1<br/>', $doc->getTitre()); ?></h1>
<h3 class="aligncenter"><?php echo myTools::displayDate($doc->date); ?></h3>
<div class="document">
<?php $feminin = "";
if (preg_match('/(propos|lettre)/i', $doc->type))
  $feminin = "e";

if ($auteurs) {
  echo '<div class="photos"><h3 class="aligncenter">écrit'.$feminin." par ";
  include_partial('parlementaire/auteurs', array("deputes" => $auteurs, "orga" => $orga));
  include_partial('parlementaire/photos', array("deputes" => $auteurs));
  echo '</p></div>';
} else if ($orga->id) {
  echo '<h3 class="aligncenter">';
  if ($orga && $orga->type != "groupe") echo link_to($orga->nom, "@list_parlementaires_organisme?slug=".$orga->slug);
  else if ($orga) echo link_to($orga->nom, "@list_parlementaires_groupe?acro=".$orga->getSmallNomGroupe());
  else echo $doc->getAuteursString();
  echo '</h3>';
} else echo '<h3 class="aligncenter">'.$doc->getSignatairesString().'</h3>';
if ($cosign) {
  echo '<div class="photos"><p class="aligncenter">cosigné'.$feminin." par ";
  include_partial('parlementaire/auteurs', array("deputes" => $cosign));
  include_partial('parlementaire/photos', array("deputes" => $cosign));
  echo '</p></div>';
}

?>
</div>
<div class="document">
<div class="right">
<div class="nuage_de_tags">
<h3>Mots-clés</h3>
  <?php echo include_component('tag', 'tagcloud', array('tagquery' => $qtag, 'model' => 'Texteloi', 'limit' => 40, 'fixlevel' => 1)); ?>
</div>
</div>
<div class="left">
<?php if ((isset($texte) && $texte > 0) || count($annexes)) { ?>
<h3>Documents associés</h3><ul>
<?php if (isset($texte) && $texte > 0)
  echo '<li>'.link_to('Voir le rapport de la commission', '@document?id='.$doc->numero).'</li>';
if (count($annexes)) {
  foreach ($annexes as $annexe) if ($annexe['id'] != $doc->id) {
    if (preg_match('/-a0/', $annexe['id']))
      echo '<li>'.link_to('Voir le texte adopté par la commission', '@document?id='.$doc->numero.'-a0').'</li>';
    else {
      if (preg_match('/-a/', $annexe['id']))
        $titreannexe = "Annexe N°&nbsp;";
      else $titreannexe = "Tome ";
      echo '<li>'.link_to($titreannexe.$annexe['annexe'], '@document?id='.$annexe['id']).'</li>';
    }
  }
} 
if ($amendements) echo '<li>'.link_to("Voir les ".$amendements." amendement".($amendements > 1 ? "s" : "")." déposé".($amendements > 1 ? "s" : "")." sur ".$doc->getTypeString(), '@find_amendements_by_loi_and_numero?loi='.$doc->numero.'&numero=all').'</li>';
echo '</ul>';
} ?>
<h3><a href="<?php echo $doc->source; ?>">Consulter le document complet sur le site de l'Assemblée</a></h3>
</div>
</div>
<div class="commentaires document">
<?php if ($doc->nb_commentaires == 0)
  echo '<h3 class="list_com">Aucun commentaire n\'a encore été formulé sur '.$doc->getTypeString().'</h3>';
else echo include_component('commentaire', 'showAll', array('object' => $doc));
echo include_component('commentaire', 'form', array('object' => $doc)); ?>
</div>

