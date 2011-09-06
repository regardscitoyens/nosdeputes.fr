<p class="legende">
<?php $txt_l = 0;
foreach (array_reverse(myTools::getGroupesInfosOrder()) as $gpe) {
  if ($txt_l > 75) {
    echo '<br/>';
    $txt_l = 0;
  }
  echo '<span class="c_b_'.strtolower($gpe[1]).'">&nbsp;</span>&nbsp;'.link_to($gpe[3], '@list_parlementaires_groupe?acro='.$gpe[1]).'&nbsp;&nbsp;';
  $txt_l += strlen($gpe[3]) + 4;
} ?>
</p>
