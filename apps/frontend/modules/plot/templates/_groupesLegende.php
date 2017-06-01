<ul class="no-bullet legend">
<?php $txt_l = 0;
foreach (array_reverse(myTools::getGroupesInfosOrder()) as $gpe) {
  echo '<li class="c_b_'.strtolower($gpe[1]).'">'.link_to($gpe[3], '@list_parlementaires_groupe?acro='.$gpe[1]).'</li>';
  $txt_l += strlen($gpe[3]) + 4;
} ?>
</ul>
