<h1><?php echo $title; ?></h1>
<table class="list_orgas">
<tbody>
<?php $permas = myTools::getCommissionsPermanentes();
if ($type == "groupe") {
  $groupes = array();
  $tmporgas = array();
  foreach($organismes as $o)
    $groupes[strtolower($o["nom"])] = $o;
  foreach (myTools::getGroupesInfos() as $gpe) {
    $g = $groupes[strtolower($gpe[0])];
    $g["nom"] = $gpe[0].' (<b class="c_'.strtolower($gpe[1]).'">'.$gpe[1].'</b>)';
    $tmporgas[] = $g;
  }
  $organismes = $tmporgas;
}

foreach($organismes as $o) :
  $name = ucfirst(in_array($o["slug"], $permas) ? str_replace("commission", "commission <small>(permanente)</small>", $o["nom"]) : $o["nom"]); ?>
<tr>
  <td class="orga"><a href="<?php echo url_for('@list_parlementaires_organisme?slug='. $o["slug"]); ?>"><?php echo $name; ?></a></td>
  <td><?php if ($o["membres"]) echo $o["membres"]." membre".($o["membres"] > 1 ? "s" : ""); ?></td>
  <?php if ($type == "parlementaire") { ?>
  <td><?php if ($o["reunions"]) echo $o["reunions"]." réunion".($o["reunions"] > 1 ? "s" : ""); ?></td>
  <?php } ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<p><a href="<?php echo url_for('@list_organismes'); ?>">Retour à la liste des types d'organismes</a></p>
