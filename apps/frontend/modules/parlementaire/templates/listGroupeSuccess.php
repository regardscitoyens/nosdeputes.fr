<?php $title =  ($orga->getSmallNomGroupe() == "NI" ? '' : 'Groupe ').$orga->getNom()." (".$orga->getSmallNomGroupe().")"; 
$title = preg_replace("/[\-](\w{1,4}(\s|$))/e", 'strtoupper("-$1$2")', $title); ?>
<h1><?php echo '<span class="c_'.strtolower($orga->getSmallNomGroupe()).'">'.$title.'</span>'; $sf_response->setTitle($title); ?></h1>
<h2><?php echo $total; ?> député<?php if ($total > 1) echo 's'; ?></h2>
<div class="liste">
<?php $listimp = array_keys($parlementaires);
  foreach($listimp as $i) {
    echo '<div class="list_table">';
    include_partial('parlementaire/table', array('deputes' => $parlementaires[$i], 'list' => 1, 'imp' => $i, 'nogroupe' => 1));
    echo '</div>';
} ?>
</div>
