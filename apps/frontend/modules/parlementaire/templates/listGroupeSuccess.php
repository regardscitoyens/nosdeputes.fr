<div class="organisme_header">
<h3><a href="<?php echo url_for('@list_organismes_type?type=groupe'); ?>">Groupe politique :</a></h3>
<h1><?php echo '<span class="c_'.strtolower($orga->getSmallNomGroupe()).'">'.$title.'</span>'; ?></h1>
<h2><?php echo $total; ?> député<?php if ($total > 1) echo 's'; ?></h2>
</div>
<div class="liste">
<?php $listimp = array_keys($parlementaires);
  foreach($listimp as $i) {
    echo '<div class="list_table">';
    include_partial('parlementaire/table', array('deputes' => $parlementaires[$i], 'list' => 1, 'imp' => $i, 'nogroupe' => 1));
    echo '</div>';
} ?>
</div>
