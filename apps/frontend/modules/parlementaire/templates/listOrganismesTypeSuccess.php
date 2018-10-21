<h1><?php echo $title; ?></h1>
<table class="list_orgas">
<thead><tr>
  <th class="orga">Intitulé</th>
  <th>Membres</th>
  <?php if ($type == "parlementaire") echo "<th>Réunions publiques</th>"; ?>
</tr></thead>
<tbody>
<?php $permas = myTools::getCommissionsPermanentes();
if ($type == "groupe") {
  $groupes = array();
  $tmporgas = array();
  $tmpoldorgas = array();
  foreach($organismes as $o)
    $groupes[strtolower($o["nom"])] = $o;
  foreach (myTools::getGroupesInfos() as $gpe) {
    $g = $groupes[strtolower($gpe[0])];
    $g["nom"] = $gpe[0].' (<b class="c_'.strtolower($gpe[1]).'">'.$gpe[1].'</b>)';
    $g["acronyme"] = $gpe[1];
    if ($g["membres"])
      $tmporgas[] = $g;
    else $tmpoldorgas[] = $g;
  }
  $organismes = $tmporgas;
  $old_organismes = $tmpoldorgas;
}

foreach($organismes as $o) :
  $name = ucfirst(in_array($o["slug"], $permas) ? str_replace("commission", "commission <small>(permanente)</small>", $o["nom"]) : $o["nom"]); ?>
<tr>
  <td class="orga"><a class="<?php echo $o['slug']; ?>" href="<?php echo url_for($type == "groupe" ? '@list_parlementaires_groupe?acro='.$o['acronyme'] : '@list_parlementaires_organisme?slug='. $o["slug"]); ?>"><?php echo $name; ?></a></td>
  <td><?php if ($o["membres"]) echo $o["membres"]; ?></td>
  <?php if ($type == "parlementaire") { ?>
  <td><?php if ($o["reunions"]) echo $o["reunions"]; ?></td>
  <?php } ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php if ($old_organismes) : ?>
<h2><?php echo ($type == "groupe" ? "Anciens groupes" : "Missions achevées"); ?> :</h2>
<table class="list_orgas">
<thead><tr>
  <th class="orga">Intitulé</th>
  <?php if ($type == "parlementaire") echo "<th>Réunions publiques</th>"; ?>
</tr></thead>
<tbody>
<?php foreach($old_organismes as $o) :
  $name = ucfirst(in_array($o["slug"], $permas) ? str_replace("commission", "commission <small>(permanente)</small>", $o["nom"]) : $o["nom"]); ?>
<tr>
  <td class="orga"><a class="<?php echo $o['slug']; ?>" href="<?php echo url_for($type == "groupe" ? '@list_parlementaires_groupe?acro='.$o['acronyme'] : '@list_parlementaires_organisme?slug='. $o["slug"]); ?>"><?php echo $name; ?></a></td>
  <?php if ($type == "parlementaire") { ?>
  <td><?php if ($o["reunions"]) echo $o["reunions"]; ?></td>
  <?php } ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>

<?php if ($type == "groupe") {
  echo '<div class="plot_groupes">';
  echo include_component('plot', 'groupes', array('plot' => 'groupes', 'groupes' => $organismes, 'nolegend' => true));
  echo '</div>';
} ?>
<p><a href="<?php echo url_for('@list_organismes'); ?>">Retour à la liste des types d'organismes</a></p>
<script type="text/javascript">
$('.list_orgas').bind('dynatable:init', function(e, dynatable) {
  dynatable.sorts.clear();
});
$(".list_orgas").dynatable({
  features: {
    paginate: false,
    pushState: false,
    search: false,
    recordCount: false
  },
  readers: {
    _attributeReader: function(cell, record) {
      var $cell = $(cell).html();
      return Number($cell) || $cell;
    }
  }
});
</script>
