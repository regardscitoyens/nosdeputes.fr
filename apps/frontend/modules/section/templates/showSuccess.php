<?php if ($section->id_dossier_an) echo '<span class="source">'.myTools::getLinkDossier($section->id_dossier_an)."</span>"; ?>
<h1><?php 
   $titre = '';
if ($section->getSection()) {
  echo link_to($section->getSection()->getTitre(), '@section?id='.$section->section_id).'</h1><h2 class="aligncenter">';
  $titre = ', '.$section->getSection()->getTitre();
 }
echo $section->titre;
$titre = $section->titre.$titre;
$sf_response->setTitle($titre.' - NosDéputés.fr');
if ($section->getSection()) echo '</h2>';
else echo '</h1>';
?>
<?php if ($section->nb_commentaires) { ?>
<div class="source"><span class="list_com"><a href="#commentaires">Voir le<?php if ($section->nb_commentaires > 1) echo 's '.$section->nb_commentaires; ?>&nbsp;commentaire<?php if ($section->nb_commentaires > 1) echo 's'; ?></a></span></div><div class="clear"></div>
<?php } ?>
<div class="resume">
<div class="right">
<?php echo include_component('tag', 'tagcloud', array('hide'=>1, 'tagquery' => $qtag, 'model' => 'Intervention', 'limit'=>40, 'route' => '@tag_section_interventions?section='.$section->id.'&', 'nozerodisplay' => true)); ?>
</div>
<div class="left">
<div class="plot_section">
<?php echo include_component('plot', 'groupes', array('plot' => 'section_'.$section->id)); ?>
</div>
</div>
</div>
<div class="clear"></div>
<?php $sommaire = $section->getSubSections();
if (count($sommaire)) { ?>
<div class="orga_dossier right">
<h2>Organisation du dossier</h2>
<ul>
<?php foreach($section->getSubSections() as $subsection) :
if ($subsection->id != $section->id) : ?>
<li><?php $subtitre = $subsection->titre;
  if ($subsection->nb_commentaires > 0) {
    $subtitre .= ' (<span class="list_com">'.$subsection->nb_commentaires.'&nbsp;commentaire';
    if ($subsection->nb_commentaires > 1) $subtitre .= 's';
    $subtitre .= '</span>)';
  }
  echo link_to($subtitre, '@interventions_seance?seance='.$subsection->getFirstSeance().'#table_'.$subsection->id); ?></li>
<?php endif; endforeach;?>
</ul>
</div>
<?php } ?>
<div class="left">
<?php if ($docs) { 
  echo '<div class="documents"><h2>Documents législatifs</h2><ul>';
  $curid = 0;
  foreach ($docs as $id => $doc) {
    $shortid = preg_replace('/-[atv].*$/', '', preg_replace('/[A-Z]/', '', $id));
    if ($curid != $shortid) {
      echo "<li>";
      $curid = $shortid;
      if (isset($doc['texteloi_id'])) {
        $doctitre = "N°$curid en débat sur NosDéputés.fr&nbsp;: ".strip_tags($doc['titre']);
        if ($doc['nb_commentaires'])
          $doctitre .= " (".$doc['nb_commentaires']."&nbsp;commentaire";
        if ($doc['nb_commentaires'] > 1)
          $doctitre .= "s";
        if ($doc['nb_commentaires'])
          $doctitre .= ")";
        echo link_to($doctitre, '@loi?loi='.$doc['texteloi_id']);
      } else if (isset($doc['id'])) {
        $amendements = Texteloi::getAmdmts($doc['type'], $curid, 1);
        $doctitre = $doc['type']." N° $curid";
        if (!preg_match('/^,/', $doc['type_details']))
          $doctitre .= " ";
        $doctitre .= $doc['type_details'];
        if (preg_match('/mixte paritaire/', $doc['signataires']))
          $doctitre .= " de la Commission mixte paritaire";
        if ($doc['nb_commentaires'])
          $doctitre .= ' (<span class="list_com">'.$doc['nb_commentaires'].'&nbsp;commentaire';
        if ($doc['nb_commentaires'] > 1)
          $doctitre .= "s";
        if ($doc['nb_commentaires']) {
          $doctitre .= "</span>";
          if ($amendements)
            $doctitre .= ", ";
          else $doctitre .= ")";
        } else if ($amendements)
          $doctitre .= " (";
        if ($amendements) 
          $doctitre .= $amendements.'&nbsp;amendement';
        if ($amendements > 1)
          $doctitre .= "s";
        if ($amendements) 
          $doctitre .= ")";
        echo link_to($doctitre, '@document?id='.$curid);
      } else
        echo 'Texte N°&nbsp;'.myTools::getLinkLoi($doc);
      echo '</li>';
    }
  }
  echo '</ul></div>';
} ?>
<div class="seances_dossier">
<h2>Toutes les séances consacrées à ce dossier</h2>
<ul>
<?php foreach($seances as $seance) : ?>
<li><?php $subtitre = $seance->getTitre();
  if ($seance->nb_commentaires > 0) {
    $subtitre .= ' (<span class="list_com">'.$seance->nb_commentaires.' commentaire';
    if ($seance->nb_commentaires > 1) $subtitre .= 's';
    $subtitre .= '</span>)';
  }
  echo link_to($subtitre, '@interventions_seance?seance='.$seance->id.'#table_'.$section->id); ?></li>
<?php endforeach; ?>
</ul>
</div>
<div class="orateurs_dossier">
<h2>Les principaux orateurs sur ce dossier :</h2>
<?php echo include_component('parlementaire', 'list', array('interventions' => $interventions, 'route'=>'@parlementaire_texte?id='.$section->id.'&slug=')); ?>
</div>
</div>
<?php if ($section->nb_commentaires != 0) { ?>
  <div class="stopfloat"></div>
  <div class="commentaires" id="commentaires">
    <h2 class="list_com">Derniers commentaires sur <?php echo $section->titre; ?> <span class="rss"><a href="<?php echo url_for('@section_rss_commentaires?id='.$section->id); ?>"><?php echo image_tag('xneth/rss.png', 'alt="Flux rss"'); ?></a></span></h2>
<?php echo include_component('commentaire', 'lastObject', array('object' => $section, 'presentation' => 'nodossier'));
    if ($section->nb_commentaires > 4)
      echo '<p class="suivant">'.link_to('Voir les '.$section->nb_commentaires.' commentaires', '@section_commentaires?id='.$section->id).'</p><div class="stopfloat"></div>'; ?>
</div>
<?php } ?>
