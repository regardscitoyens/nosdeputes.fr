<?php 
$sf_response->setTitle("Recherche de $query");  $style = "xneth";

function link_search($text, $query, $args, $options) 
{
  $extra = '';
  $url = "solr/search?query=".$query;
  foreach($args as $k => $v) {
    if (is_array($v)) {
      if (count($v))
	$extra .= '&'.$k.'='.implode(',', array_keys($v));
    }    else
      $extra .= '&'.$k.'='.$v;
  }
  if($options) { return link_to($text, $url.$extra, $options); }
  else { return link_to($text, $url.$extra); }
}

$recherche = preg_replace('/"/', '&quot;', $query);

if($interval == '+1MONTH') { $par = 'mois'; } 
else { $par = 'jour'; }
?>
<script type="text/javascript">
function urlParams(params) {
	param = new Object();
	if(jQuery.isArray(params)) {
    jQuery.each(params, function() {
      val = this.split('=');
      param[val[0]] = val[1];
    });
  }
	return param;
}

periode = new Array();
date_href = new Array();
nb_li = 0;
bh = 0;
nh = 0;
if(location.search.substring(1)) { parametre = urlParams(location.search.substring(1).split('&')); }
else { parametre = new Object(); }

$(document).ready(function() {
  $(".date li").each(function() {
    if($(this).height() > bh) { bh = $(this).height(); }
    date_li = $(this).find('a').attr("title").split(':');
    date_href[nb_li] = $(this).find('a').attr("href"); /* ajouter onclick haut colonnes */
    $(this).find("#hover_graph").attr("onclick", "document.location.replace('http://"+location.host+date_href[nb_li]+"')");
    periode[nb_li] = date_li[0];
    nb_li++;
  });
  if(bh <= 30) {
    $(".date li").each(function() {
      nh = $(this).height() * 2; $(this).height(nh);
    });
    bh = bh * 2;
  }
  if(bh <= 170) { bh = bh + 30; $(".date").height(bh); }
  $(".date").fadeIn(300);
	$(function() {
		$("#slider_date_graph").slider({
			range: true,
			min: 0,
			max: nb_li-1,
			values: [0, nb_li],
			slide: function(event, ui) {
			  from = date_href[ui.values[0]].split('?');
			  from = urlParams(from[1].split('&'));
			  from = from["date"].split('%2C');
			  parametre["from"] = from[1];
			  to = date_href[ui.values[1]].split('?');
			  to = urlParams(to[1].split('&'));
			  to = to["date"].split('%2C');
			  parametre["to"] = to[1];
			  
			  if(parametre.date != undefined) { delete(parametre.date); }
			  lien = "?";
			  if(ui.values[0] == ui.values[1]) { 
			    lien = lien+"date="+parametre.from; 
			  }
			  else {
			    if(parametre.from != undefined) { lien = lien+"from="+parametre.from; }
			    if(parametre.to != undefined) { lien = lien+"&to="+parametre.to; }
			  }
			  if(parametre.parlementaire != undefined) { lien = lien+"&parlementaire="+parametre.parlementaire; }
			  if(parametre.object_name != undefined) { lien = lien+"&object_name="+parametre.object_name; }
			  if(parametre.tag != undefined) { lien = lien+"&tag="+parametre.tag; }
			  /* parlementaire object_name tag date to from */
			  <?php if($par == 'mois') { echo "en = 'en';"; } else { echo "en = 'le';"; } ?>
			  if(ui.values[0] == ui.values[1]) { 
			    texte_periode = '<a href="'+lien+'">'+en+' '+periode[ui.values[0]]+'</a>';
			  }
			  else { 
			    texte_periode = '<a href="'+lien+'">entre '+periode[ui.values[0]]+' et '+ periode[ui.values[1]]+'</a>';
			  }
			  
			  $("#periode").text("");
				$("#periode").append(texte_periode);
			}
		});
		$("#periode").text('entre ' + periode[$("#slider_date_graph").slider("values", 0)] + ' et ' + periode[$("#slider_date_graph").slider("values", 1)]);
	});
});
</script>

<div class="solr">
<div class="searchbox">
<form>
    <input name="search" value="<?php echo $recherche; ?>" />
    <input type="submit" value="Rechercher"/>
</form>
</div>
<?php 
if(count($fdates['values']) > 1) { 
$width_date = 900;
$left = 2;
$espacement = 4;
$width = (($width_date - $left) / count($fdates['values'])) - $espacement;
$start = explode("T", $start);

if($par == 'mois') {
  if($end == 'NOW') { $end = "aujourd'hui"; }
  else { 
  $end = explode("T", $end); 
  $end = explode("-", $end[0]);
  $end = mktime(0, 0, 0, $end[1], $end[2] - 1, $end[0]);
  $end = date("Y-m-d", $end);
  $end = 'le '.myTools::displayShortDate($end); }
  echo '<h1>Résultats pour "<em>'.$recherche.'</em>" entre le '.myTools::displayShortDate($start[0]).' et '.$end.'</h1>';
}
else {
  $end = explode("T", $end); 
  echo '<h1>Résultats pour "<em>'.$recherche.'</em>" en '.myTools::displayMoisAnnee($start[0]).'</h1>';
}

?>
<div class="cont_date_graph">
<span>Affiner par date :</span> <span id="periode"></span>
<div class="date" style="width: <?php echo $width_date ?>px;">
<ul>
   <?php $i = 0; foreach($fdates['values'] as $date => $nb) : 
    $height = round($nb['pc']*100/($fdates['max']) * 2);
    $padding = 200-$height; ?>
    <li<?php echo ' style="list-style-image: none; width: '.$width.'px; height: '.$height.'px; left: '.$left.'px;">'; 
    $left = $left + $width; if($i < (count($fdates['values']) - 1)) { $left = $left + $espacement; }
    $newargs = $selected;
    $newargs['date'][$date] = 1;
    $title_date = explode("T", $date);
    if($par == "mois") {
      $title_date = ucfirst(myTools::displayMoisAnnee($title_date[0])).' : '.$nb['nb'].' résultats';
    }
    else {
      $title_date = myTools::displayShortDate($title_date[0]).' : '.$nb['nb'].' résultats';
    }
    echo '<div id="hover_graph" title="'.$title_date.'" style="width: '.$width.'px; height: '.$padding.'px;	bottom: '.$height.'px;"></div><span id="text_graph">'.link_search($nb['nb'], $query, $newargs, array('title' => $title_date)).'</span>'; 
    
    # echo ' '.$nb['nb'].' résultats ('; printf('%02d', $nb['pc']*100/($fdates['max'])); echo '%)';
   ?>
   </li>
<?php endforeach; ?>
</ul>
</div>
<div id="slider_date_graph"></div>
</div>
<?php } else { echo '<h1>Résultats pour "<em>'.$recherche.'</em>" le '.strtolower(myTools::displayDateSemaine($date_en_cours)).'</h1>'; } ?>
<div class="nb_results">
    <h2>Résultats <?php echo $results['start']+1; ?> à <?php echo $results['end']-1; ?> sur <?php echo $results['numFound']; ?> <strong>triés par <?php echo $sort_type; ?></strong> - 
<span class="tri">
<?php 
  $newargs = $selected;
  if ($sort)
    echo link_search('trier par pertinence', $query, $newargs, 0); 
  else {
    $newargs['sort'] = 1;
    echo link_search('trier par date', $query, $newargs, 0); 
  }
?>
</span></h2>
</div>
<div class="options">
<div class="mail">
<h3>Alerte email</h3>
<?php 
    $args = '';
foreach(array_keys($selected) as $k) {
  if (!is_array($selected[$k])) 
    continue;
  if ($args)
    $args .= '&';
  $args.= "$k=".implode(',', array_keys($selected[$k]));
}
echo link_to('Recevoir un email lorsque de nouveaux résultats seront publiés pour cette recherche', 'alerte/create?filter='.urlencode($args).'&query='.urlencode($query));
?>
</div>
<div class="facets">
<h3>Filtres supplémentaires</h3>
<?php   foreach(array_keys($facet) as $k) { if (isset($facet[$k]['values']) && count($facet[$k]['values'])) : ?>
<div class="<?php echo $k; ?>">
<p><?php echo $facet[$k]['name']; ?></p>
<ul>
<?php foreach($facet[$k]['values'] as $value => $nb) : if ($nb) :
$is_selected = isset($selected[$facet[$k]['facet_field']][$facet[$k]['prefix'].$value]) && 
		     $selected[$facet[$k]['facet_field']][$facet[$k]['prefix'].$value];
?>
<li<?php if ($is_selected) echo ' class="selected"'; ?>><?php 
$newargs = $selected;
if ($is_selected) 
  unset($newargs[$facet[$k]['facet_field']][$facet[$k]['prefix'].$value]);
else			      
  $newargs[$facet[$k]['facet_field']][$facet[$k]['prefix'].$value] = 1;
echo link_search($value, 
		 $query, 
		 $newargs,
		 0
		 ); ?> (<?php echo $nb; ?>)</li>
<?php endif; endforeach; ?>
</ul>
</div>
<?php endif; } ?>

</div>
</div>
<div class="results">
<?php foreach ($results['docs'] as $record) : ?>
<div class="item">
      <h4><a href="<?php echo $record['link']; ?>"><?php echo $record['titre']; ?></a></h4>
      <?php if ($record['photo']) { ?><p class="photo"><a href="<?php echo $record['link']; ?>" rel="nofollow"><img width="53" src="<?php echo $record['photo']; ?>"/></a></p><?php } ?>
      <p class="intervenant"><a href="<?php echo $record['link']; ?>" rel="nofollow"><?php echo $record['personne']; ?></a></p>
      <p class="content"><?php echo $record['highlighting']; ?></p>
      <p class="more"><a href="<?php echo $record['link']; ?>">Consulter</a></p>
</div>
<div class="record">
</div>
<?php endforeach; ?>
</div>
<div class="pager">
<div class="next">
<?php
if ($results['end']-1 != $results['numFound']) {
  $newargs = $selected;
  $newargs['page'][$results['page'] + 1] = 1;
  echo link_search('page suivante',
		   $query,
		   $newargs,
		   0
		   ); 
}
?>
</div>
<div class="last">
<?php
$newargs = $selected;
$newargs['page'][$results['page'] - 1] = 1;
if ($results['page'] > 1) {
  if (isset($newargs['page'][1]))
    unset($newargs['page'][1]);
  echo link_search('page précédente',
		   $query,
		   $newargs,
		   0
		   ); 
 }
?>
</div>
</div>
</div>
