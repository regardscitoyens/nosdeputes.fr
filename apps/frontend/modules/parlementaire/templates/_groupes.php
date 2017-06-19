<center><h4><?php foreach (myTools::getCurrentGroupesInfos() as $gpe)
  echo '&nbsp; '.link_to(str_replace(' ', '&nbsp;', $gpe[0].' (<b').' class="c_'.strtolower($gpe[1]).'">'.$gpe[1].'</b>)', '@list_parlementaires_groupe?acro='.$gpe[1]).'&nbsp; '; ?>
</h4></center>
