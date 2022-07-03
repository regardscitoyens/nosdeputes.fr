<p class="legende">
<?php
if (!isset($width)) $width = 400;
$max_l = $width / 6.5;
$txt_l = 0;
foreach (myTools::getGroupesInfos() as $gpe) {
  if (!isset($gpe[3]))
    continue;
  if (isset($groupes) && !in_array($gpe[1], $groupes))
    continue;
  $txt_l += strlen($gpe[3]) + 2;
  if ($txt_l > $max_l) {
    echo '<br/>';
    $txt_l = 0;
  }
  echo '<span title="<b>Groupe '.$gpe[1].'</b><br/>'.$gpe[0].'" class="jstitle"><span class="square c_b_'.strtolower($gpe[1]).'"></span>'.link_to($gpe[3], '@list_parlementaires_groupe?acro='.$gpe[1]).'</span>&nbsp;&nbsp;';
} ?>
</p>
