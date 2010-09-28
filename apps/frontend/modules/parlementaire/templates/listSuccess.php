<div class="liste"><?php 
$listlettres = array_keys($parlementaires);
foreach($listlettres as $i) {
  echo '<div class="list_choix" id="'.$i.'">';
  foreach($listlettres as $l) {
    if ($l != $i) echo link_to($l , '@list_parlementaires#'.$l);
    else echo '<big><strong>'.$l.'</strong></big>';
    echo '&nbsp;&nbsp;';
  }
  echo '</div><div class="list_table">';
  include_partial('parlementaire/table', array('deputes' => $parlementaires[$i], 'list' => 1));
  echo '</div><div class="suivant"><a href="#">Haut de page</a></div>';
}

 ?>
</div>

