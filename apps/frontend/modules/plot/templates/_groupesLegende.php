<p class="legende">
<?php $txt_l = 0;
foreach (myTools::getCurrentGroupesInfos() as $gpe) {
  if (isset($groupes) && !in_array($gpe[1], $groupes))
    continue;
  $txt_l += strlen($gpe[3]) + 4;
  if ($txt_l > 65) {
    echo '<br/>';
    $txt_l = 0;
  }
  echo '<span class="c_b_'.strtolower($gpe[1]).'">&nbsp;</span>&nbsp;'.link_to($gpe[3], '@list_parlementaires_groupe?acro='.$gpe[1]).'&nbsp;&nbsp;';
} ?>
</p>
