<div class="precedent"><?php echo myTools::displayDate($doc->date); ?></div>
<div class="source"><?php if ($section) echo link_to('Dossier relatif', '@section?id='.$section->id); else echo myTools::getLinkDossier($doc->id_dossier_an); ?></div>
<h1><?php echo $doc->getShortTitre(); ?></h1>
<h2><?php echo preg_replace('/ - /', '<br/>- ', $doc->getDetailsTitre()); ?></h2>
<div class="document">
<?php $feminin = "";
if (preg_match('/(propos|lettre)/i', $doc->type))
  $feminin = "e";

if (count($auteurs)) {
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
if (count($cosign)) {
  echo '<div class="photos"><p class="aligncenter">cosigné'.$feminin.' par <span id="liste_deputes">';
  include_partial('parlementaire/auteurs', array("deputes" => $cosign));
  echo '</span>';
  if (count($cosign) < 16) { echo '<span id="photos">'; include_partial('parlementaire/photos', array("deputes" => $cosign)); echo '</span>'; }
  echo '</p></div>';
}

?>
</div>
<?php $tags = get_component('tag', 'tagcloud', array('tagquery' => $qtag, 'model' => 'Texteloi', 'limit' => 40, 'fixlevel' => 1, 'nozerodisplay' => true)); ?>
<div class="document">
<div<?php if ($tags != "" || (isset($texte) && $texte > 0) || count($annexes) || $amendements || count($relatifs) || $section) echo ' class="left"'; ?>>
<?php if ($txt = $doc->getExtract()) { ?>
<h3>Extrait</h3>
<p class="justify tabulation"><?php echo myTools::escape_blanks(preg_replace('/([a-z])\. ([^"»])/', '\\1.</p><p class="justify tabulation">\\2', $doc->getExtract())); ?></p>
<?php } ?>
<h3><a href="<?php echo $doc->source; ?>">Consulter le document complet sur le site de l'Assemblée</a></h3>
<p class="aligncenter">(<?php echo link_to('version pdf', preg_replace('/asp$/', 'pdf', preg_replace('/'.sfConfig::get('app_legislature', 13).'\//', sfConfig::get('app_legislature', 13).'/pdf/', $doc->source))); ?>)</p>
</div>
<div class="right">
<?php print $tags;
if ((isset($texte) && $texte > 0) || count($annexes) || $amendements) { ?>
  <div class="annexes">
  <h3>Documents associés</h3><ul>
  <?php if ($amendements) echo '<li>'.link_to("Voir les ".$amendements." amendement".($amendements > 1 ? "s" : "")." déposé".($amendements > 1 ? "s" : "")." sur ce texte", '@find_amendements_by_loi_and_numero?loi='.$doc->numero.'&numero=all').'</li>';
  if (isset($texte) && $texte > 0)
    echo '<li>'.link_to('Voir le rapport de la commission', '@document?id='.$doc->numero).'</li>';
  if (count($annexes)) {
    foreach ($annexes as $annexe) if ($annexe['id'] != $doc->id && preg_match('/-a0/', $annexe['id']))
      echo '<li>'.link_to('Voir le texte adopté par la commission', '@document?id='.$doc->numero.'-a0').'</li>';
    foreach ($annexes as $annexe) if ($annexe['id'] != $doc->id && preg_match('/t([\dIVX]+)/', $annexe['id'], $tom)) {
      $titreannexe = "Tome&nbsp;".$tom[1];
      if (preg_match('/v(\d+)/', $annexe['id'], $vol))
        $titreannexe .= " - volume ".$vol[1];
      echo '<li>'.link_to($titreannexe, '@document?id='.$annexe['id']).'</li>';
    }
    foreach ($annexes as $annexe) if ($annexe['id'] != $doc->id && preg_match('/-a([1-9]\d*)/', $annexe['id'], $ann))
      echo '<li>'.link_to("Annexe N°&nbsp;".$ann[1], '@document?id='.$annexe['id']).'</li>';
  }
  echo '</ul></div>';
}
if (count($relatifs) || $section) { ?>
  <div class="annexes">
  <h3>Documents relatifs</h3><ul>
  <?php if ($section) echo '<li>'.link_to('Dossier : '.$section->titre, '@section?id='.$section->id).'</li>';
  $curid = 0;
  foreach ($relatifs as $rel) {
    $shortid = preg_replace('/-[atv].*$/', '', preg_replace('/[A-Z]/', '', $rel['id']));
    if ($curid != $shortid) {
      echo '<li>';
      $curid = $shortid;
      $doctitre = $rel['type']." N° $curid";
      if (!preg_match('/^,/', $rel['type_details']))
        $doctitre .= " ";
      $doctitre .= $rel['type_details'];
      if (preg_match('/mixte paritaire/', $rel['signataires']))
        $doctitre .= " de la Commission mixte paritaire";
      echo link_to($doctitre, '@document?id='.$curid).'</li>';
    }
  }
  echo '</ul></div>';
} ?>
</div>
</div>
<div class="commentaires document">
<?php echo include_component('commentaire', 'showAll', array('object' => $doc, 'type' => $doc->getTypeString()));
echo include_component('commentaire', 'form', array('object' => $doc)); ?>
</div>
<?php
if (count($cosign)) {
  if (count($cosign) < 16) { ?>
<script type="text/javascript">
<!--
$('#liste_deputes a').live('mouseover', function() {
 nom = $(this).attr('href').split('/'); nom = nom.reverse(); $('.photo_fiche[src*="'+nom[0]+'"]').css('opacity', '1');
});
$('#liste_deputes').bind('mouseover mouseout', function(event) {
 if (event.type == "mouseover") { $('#photos .photo_fiche').css('opacity', '0.3'); $("#liste_deputes").die("mouseover"); }
 else { $('.photo_fiche').css('opacity', '1'); $("#liste_deputes").die("mouseout"); }
});
// -->
</script>
<?php  }
}
?>
