<?php
foreach ($tops as $t)
  if (!isset($date)) {
    $date = $t[0]['updated_at'];
    break;
  }
?>
<h1>Synthèse générale de l'activité parlementaire<br/><small><?php echo $subtitle; ?></small></h1>
<h2 class="aligncenter"><small>(<a href="<?php echo url_for('@faq'); ?>#post_2">mise-à-jour quotidienne</a>, dernière en date le <?php echo preg_replace('/20(\d+)-(\d+)-(\d+) (\d+):(\d+):\d+/', '$3/$2/$1 à $4H$5', $date); ?>)</small></h2>
<h2>Activité <?php if ($fin) echo "mensuelle moyenne "; ?>de tous les députés<?php echo ($fin ? " ayant exercé au moins 6 mois" : ($fresh ? "" : " en cours de mandat depuis au moins 10 mois")); ?> :</h2>
<?php
$indicateurs = myTools::$indicateurs;
$title = array(
  'semaines_presence'               => "d'activité",
  'commission_presences'            => 'réunion',
  'commission_interventions'        => 'interv.',
  'hemicycle_interventions'         => 'interv.<br/>longues',
  'hemicycle_interventions_courtes' => 'interv.<br/>courtes',
  'amendements_proposes'            => 'proposés',
  'amendements_signes'              => 'signés',
  'amendements_adoptes'             => 'adoptés',
  'rapports'                        => 'écrits',
  'propositions_ecrites'            => 'écrites',
  'propositions_signees'            => 'signées',
  'questions_ecrites'               => 'écrites',
  'questions_orales'                => 'orales'
);
$class = array(
  'parl'                            => 'p',
  'semaines_presence'               => 'we',
  'commission_presences'            => 'cp',
  'commission_interventions'        => 'ci',
  'hemicycle_interventions'         => 'hl',
  'hemicycle_interventions_courtes' => 'hc',
  'amendements_proposes'            => 'ap',
  'amendements_signes'              => 'as',
  'amendements_adoptes'             => 'aa',
  'rapports'                        => 'ra',
  'propositions_ecrites'            => 'pe',
  'propositions_signees'            => 'ps',
  'questions_ecrites'               => 'qe',
  'questions_orales'                => 'qo'
);
$bulles = array("");
foreach (array_keys($title) as $k)
  $bulles[] = $indicateurs[$k]['titre'].' --  -- '.$indicateurs[$k]['desc'];
?>
<div class="liste_deputes_top">
<div class="synthese">
<table id="synthese_deputes">
<thead>
  <tr>
    <th id="search"></th>
    <th title="Trier par : <?php echo $bulles[1]; ?>" class="jstitle <?php if ($sort == 1) echo 'tr_odd';?>"><?php echo link_to('Semaines', '@top_global_sorted?sort=1'); ?></th>
    <th colspan="2" class="<?php if ($sort == 2 || $sort == 3) echo 'tr_odd';?>">Commission</th>
    <th colspan="2" class="<?php if ($sort == 4 || $sort == 5) echo 'tr_odd';?>">Hémicycle</th>
    <th colspan="3" class="<?php if ($sort == 6 || $sort == 7 || $sort == 8) echo 'tr_odd';?>">Amendements</th>
    <th title="Trier par : <?php echo $bulles[9]; ?>" class="jstitle <?php if ($sort == 9) echo 'tr_odd';?>"><?php echo link_to('Rapports', '@top_global_sorted?sort=9'); ?></th>
    <th colspan="2" class="<?php if ($sort == 10 || $sort == 11) echo 'tr_odd';?>">Propositions</th>
    <th colspan="2" class="<?php if ($sort == 12 || $sort == 13) echo 'tr_odd';?>">Questions</th>
  </tr>
  <tr>
    <th data-dynatable-column="nom_de_famille" style="display: none;"></th>
    <th
      data-dynatable-sorts="nom_de_famille"
      id="nom"
      title="Trier par : Nom de famille"
      class="jstitle <?php echo $class['parl']; ?>"><?php
      echo link_to('Nom', '@top_global');
    ?></th>
    <?php $last = end($tops); $i = 0;
    foreach($ktop as $key) :
      $i++; ?>
    <th
      data-dynatable-column="<?php echo $key; ?>"
      data-dynatable-sorts="<?php echo $key; ?>-sort"
      title="<?php echo "Trier par : ".$bulles[$i]; ?>"
      class="jstitle <?php echo $class[$key]; if ($sort == $i) echo ' tr_odd'; ?>"><?php
      echo link_to($title[$key], '@top_global_sorted?sort='.$i);
    ?></th>
    <?php endforeach; ?>
  </tr>
<?php array_unshift($ktop, ''); ?>
</thead>
<tbody class="tableau_synthese">
  <?php
  foreach($tops as $t) {
    $urldep = url_for('@parlementaire?slug='.$t[0]['slug']); ?>
  <tr>
    <td style="display: none;"><?php echo $t[0]['nom_de_famille']; ?></td>
    <td><span
      id="<?php echo $t[0]['slug']; ?>"
      class="jstitle phototitle c_<?php echo strtolower($t[0]['groupe_acronyme']); ?> <?php echo $class['parl']; ?>"
      title="<?php echo $t[0]['nom']; ?> -- Député<?php if ($t[0]['sexe'] === "F") echo 'e'; ?> <?php echo $t[0]['groupe_acronyme'].' '.preg_replace('/([^\'])$/', '\\1 ', Parlementaire::$dptmt_pref[trim($t[0]['nom_circo'])]).$t[0]['nom_circo']; ?>">
      <span class="urlphoto" title="<?php echo $urldep; ?>"></span>
      <a href="<?php echo $urldep; ?>"><?php echo $t[0]['nom']; ?></a>
    </span></td>
<?php $field = "value";
    if ($fin)
      $field = "moyenne";
    for($i = 1 ; $i < count($t); $i++) {
      echo '<td><span title="'.$t[$i]['value'].' ';
      $leg = $bulles[$i];
      if ($t[$i]['value'] < 2)
        $leg = preg_replace('/s (.*-- )/', ' \\1', preg_replace('/s (.*-- )/', ' \\1', $leg));
      if ($fin)
        $leg = str_replace(" -- Nombre", " sur ".$t[0]["nb_mois"]." mois d'exercice -- Nombre", $leg);
      echo $leg;
      echo '" '.$t[$i]['style'].' class="jstitle '.$class[$ktop[$i]].'">';
      if (!$fin && preg_match('/\./', $t[$i]['value']))
        printf('%02d', $t[$i]['value']);
      else echo str_replace(".", ",", ($fin ? sprintf('%.02f', $t[$i][$field]) : $t[$i][$field]));
      echo '</span></td>';
    } ?>
  </tr>
<?php } ?>
</tbody>
</table>
</div>
<p class="aligncenter">Les chiffres en couleur indiquent que le député se trouve pour le critère indiqué parmi <span style="color:green;font-weight: bold;">les 150 plus actifs</span> ou <span style="color:red;font-style : italic;">les 150 moins actifs</span>.</p>
<p class="aligncenter">Télécharger les données : <b><?php echo link_to('CSV', '@api_synthese_current?format=csv'); ?></b> &mdash; <b><?php echo link_to('JSON', '@api_synthese_current?format=json'); ?></b> &mdash; <b><?php echo link_to('XML', '@api_synthese_current?format=xml'); ?></b> &mdash; <b class="jstitle" title="Format CSV corrigeant les problèmes d'encodage liés à certains logiciels de tableurs"><?php echo link_to('Tableur', '@api_synthese_current?format=csv&withBOM=true'); ?></b>&nbsp;&nbsp;<a href="http://www.regardscitoyens.org/open-data-en-france/"><img src="/images/opendata.png" alt="OpenData" title="OpenData" style="border: none; margin-bottom: -4px;"/></a></p>
<p class="aligncenter"><?php if (!$fin) echo "Ces données portent uniquement sur les douze derniers mois. "; ?>Retrouvez les données historisées mois par mois au travers de <u><a href="https://github.com/regardscitoyens/nosdeputes.fr/blob/master/doc/api.md">l'API de NosDéputés.fr</a></u></p>
</div>
<h2 id="groupes">Activité moyenne d'un député de chaque groupe politique <?php if ($fresh) echo "depuis le début de la législature"; elseif ($fin) echo "sur toute la législature"; else echo "au cours des 12 derniers mois"; ?> :</h2>
<div class="liste_deputes_top">
<div class="synthese">
<?php for($i=0; $i<count($bulles); $i++)
  $bulles[$i] = str_replace('Nombre', 'Nombre moyen', str_replace('le député', 'un député de ce groupe', $bulles[$i])); ?>
<table id="synthese_groupes">
<thead>
  <tr>
    <th class="gpes <?php echo $class['parl']; ?>">&nbsp;</th>
    <th title="<?php echo $bulles[1]; ?>" class="jstitle <?php if ($sort == 1) echo 'tr_odd';?>">Semaines</th>
    <th colspan="2" class="<?php if ($sort == 2 || $sort == 3) echo 'tr_odd';?>">Commission</th>
    <th colspan="2" class="<?php if ($sort == 4 || $sort == 5) echo 'tr_odd';?>">Hémicycle</th>
    <th colspan="3" class="<?php if ($sort == 6 || $sort == 7|| $sort == 8) echo 'tr_odd';?>">Amendements</th>
    <th title="<?php echo $bulles[9]; ?>" class="jstitle <?php if ($sort == 9) echo 'tr_odd';?>">Rapports</th>
    <th colspan="2" class="<?php if ($sort == 10 || $sort == 11) echo 'tr_odd';?>">Propositions</th>
    <th colspan="2" class="<?php if ($sort == 12 || $sort == 13) echo 'tr_odd';?>">Questions</th>
  </tr>
  <tr>
    <th class="jstitle gpes <?php echo $class['parl']; ?>">Groupe</th>
    <?php $i = 0;
    foreach($ktop as $key) :
      if ($key === "") continue;
      $i++;
    ?>
    <th
      data-dynatable-column="<?php echo $key; ?>"
      data-dynatable-sorts="<?php echo $key; ?>-sort"
      title="<?php echo $bulles[$i]; ?>"
      class="jstitle <?php echo $class[$key].($sort == $i ? ' tr_odd' : ''); ?>"><?php
      echo $title[$key];
    ?></th>
    <?php endforeach; ?>
  </tr>
</thead>
<tbody class="tableau_synthese_groupes">
<?php foreach ($gpes as $gpe => $t) {
  if (!$t[0]['nb']) continue;
  $nb = ' ('.$t[0]['nb'].' député'.($t[0]['nb'] > 1 ? 's' : '').')';
  echo '<tr>';
  echo '<td id="'.$gpe.'"><span class="jstitle c_'.strtolower($gpe).' '.$class['parl'].'" title="'.$t[0]['nom'];
  if (isset($t[0]['desc']))
    echo ' -- '.$t[0]['desc'].'"><a href="'.url_for('@list_parlementaires_groupe?acro='.$gpe).'">'.$gpe.$nb.'</a>';
  else echo '">'.$t[0]['nom'].$nb;
  echo '</span></td>';
  for($i = 1 ; $i < count($t) ; $i++) {
    $t[$i] = round($t[$i]/$t[0]['nb']);
    echo '<td><span title="'.$t[$i].' '.($t[$i] < 2 ? preg_replace('/s (.*-- )/', ' \\1', preg_replace('/s (.*-- )/', ' \\1', $bulles[$i])) : $bulles[$i]).'" class="jstitle '.$class[$ktop[$i]].'">';
    if (preg_match('/\./', $t[$i]))
      printf('%02d', $t[$i]);
    else echo $t[$i];
    echo '</span></td>';
  }
  echo '</tr>';
} ?>
</tbody>
</table>
</div>
</div>
<div class="synthese_div">
<h2>Répartition de l'activité des députés sur <?php if ($fresh) echo "depuis le début de la législature"; elseif ($fin) echo "toute la législature"; else echo "les 12 derniers mois"; ?> par groupe politique :</h2>
<div class="aligncenter"><?php echo include_component('plot', 'syntheseGroupes', array('type' => 'all')); ?></div>
</div>
<div id="legende" class="synthese_div">
<h2>Explications :</h2>
<ul>
<?php foreach(array_keys($title) as $k)
  echo '<li><strong>'.$indicateurs[$k]['titre'].'</strong> : '.str_replace(' -- ', ' ', $indicateurs[$k]['desc']).'</li>';
?>
</ul>
<p><a href="<?php echo url_for('@faq#post_1'); ?>">Lire notre FAQ pour plus d'explications</a></p>
</div>
<script type="text/javascript">
$(document).ready(function(){
  var initTable = function(e, dynatable) {
    dynatable.sorts.clear();
    e.currentTarget.init = true;
  }, hideTable = function(e, dynatable) {
    if (!e.currentTarget.init)
      $(e.currentTarget).find('tbody').css('opacity', 0.6);
  }, showTable = function(e, dynatable) {
    if (!e.currentTarget.init)
      $(e.currentTarget).find('tbody').css('opacity', 1);
    e.currentTarget.init = false;
    window.jsTitle();
  }, dynamiseTable = function(tableId, search, delay) {
    setTimeout(function(){
      $('#'+tableId).bind('dynatable:init', initTable);
      if (search) {
        $('#'+tableId).bind('dynatable:beforeProcess', hideTable);
        $('#'+tableId).bind('dynatable:afterProcess', showTable);
      }
      $('#'+tableId).dynatable({
        features: {
          paginate: false,
          pushState: false,
          search: search,
          recordCount: false
        },
        table: {
          headRowSelector: "thead tr:last-child"
        },
        inputs: {
          processingText: "",
          searchText: "",
          searchTarget: $("#search"),
          searchPlaceholder: "chercher un député...",
          searchPlacement: "html",
        },
        readers: {
          _attributeReader: function(cell, record, column) {
            var $cell = $(cell),
              text = Number($cell.text()),
              html = $cell.html().replace(/<a href=".*synthesetri\/\d+">(.*?)<\/a>/, "$1");
            record[column+"-sort"] = (isNaN(text) ? html : text);
            return Number(html) || html;
          }
        }
      });
    }, delay);
  };
  dynamiseTable('synthese_groupes', false, 100);
  dynamiseTable('synthese_deputes', true, 1500);
});
</script>
