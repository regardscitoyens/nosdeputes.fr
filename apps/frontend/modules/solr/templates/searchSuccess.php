<?php 
$sf_response->setTitle("Recherche de $query");  $style = "xneth";

function url_search($query, $args) 
{
  $extra = '';
  $url = "solr/search?query=".$query;
  foreach($args as $key => $value) {
    if (is_array($value)) {
      if (count($value)) { $extra .= '&'.$key.'='.implode(',', array_keys($value)); }
    }    
    else { $extra .= '&'.$key.'='.$value; }
  }
  return $url.$extra;
}

function link_search($text, $query, $args, $options) 
{
  if($options) { return link_to($text, url_search($query, $args), $options); }
  else { return link_to($text, url_search($query, $args)); }
}

function addToDate($date, $interval) {
  $date = explode("T", $date); 
  $date = explode("-", $date[0]);
  if($interval == 'mois') {
    $date = mktime(0, 0, 0, $date[1] + 1, $date[2], $date[0]); 
    #int mktime  ("H","m","s","M","j","Y", -1)
  }
  else { $date = mktime(0, 0, 0, $date[1], $date[2] - 1, $date[0]); }
  $date = date("Y-m-d", $date).'T00%3A00%3A00Z';
  return $date;
}

$recherche = preg_replace('/"/', '&quot;', $query);
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
  else {
    val = params.split('=');
    param[val[0]] = val[1];
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
    date_href[nb_li] = $(this).find('a').attr("href");
    $(this).find("#hover_graph").attr("onclick", "document.location.replace('http://"+location.host+date_href[nb_li]+"')");
    $(this).find("#hover_graph").css("cursor", "pointer");
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
  $(".date").fadeIn(300); /* à revoir */
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
			  to = date_href[ui.values[1]].split('?');
			  to = urlParams(to[1].split('&'));
			  to = to["date"].split('%2C');
			  parametre["date"] = from[0]+'%2C'+to[0];
			  
			  lien = "?date="+parametre.date;
			  if(parametre.parlementaire != undefined) { lien = lien+"&parlementaire="+parametre.parlementaire; }
			  if(parametre.object_name != undefined) { lien = lien+"&object_name="+parametre.object_name; }
			  if(parametre.tag != undefined) { lien = lien+"&tag="+parametre.tag; }
			  /* date parlementaire object_name tag */
			  if(ui.values[0] == ui.values[1]) { 
			    texte_periode = '<a href="'+lien+'">'+periode[ui.values[0]]+'</a>';
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
  <p>
    <input name="search" value="<?php echo $recherche; ?>" />
    <input type="submit" value="Rechercher"/>
  </p>
  </form>
</div>
<?php 
$start = explode("T", $start);
if($end == 'NOW') { $end = date("Y-m-d").'T00%3A00%3A00Z'; }
$end =  explode("T", $end);
?>
<h1><?php
switch ($vue) {
  case "jour":
    echo 'Résultats pour "'.$recherche.'" le '.myTools::displayShortDate($start[0]);
    $graph = 0;
    break;
  case "mois":
    echo 'Résultats pour "'.$recherche.'" en '.myTools::displayMoisAnnee($start[0]);
    $graph = 1;
    break;
  case "par_jour":
    echo 'Résultats pour "'.$recherche.'" entre le '.myTools::displayShortDate($start[0]).' et le '.myTools::displayShortDate($end[0]);
    $graph = 1;
    break;
  case "par_mois":
    echo 'Résultats pour "'.$recherche.'" entre '.myTools::displayMoisAnnee($start[0]).' et '.myTools::displayMoisAnnee($end[0]);
    $graph = 1;
    break;
  default:
    echo 'Recherche de "'.$recherche;
    $graph = 1;
}
?></h1>
<?php if($graph) { 
  $width_date = 900;
  $left = 2;
  $espacement = 4;
  $width = (($width_date - $left) / count($fdates['values'])) - $espacement;
?>
<div class="cont_date_graph">
  <span>Affiner par date :</span> <span id="periode"></span>
  <div class="date" style="width: <?php echo $width_date ?>px;">
  <ul>
    <?php $i = 0; foreach($fdates['values'] as $date => $nb) :
    $i++;
    $height = round($nb['pc']*100/($fdates['max']) * 2);
    $padding = 200-$height; ?>
    <li<?php echo ' style="list-style-image: none; width: '.$width.'px; height: '.$height.'px; left: '.$left.'px;">'; 
    $left = $left + $width; if($i < (count($fdates['values']))) { $left = $left + $espacement; }
    $newargs = $selected;
    
    $title_date = explode("T", $date);
    
    $newargs['date'] = $date.'%2C'.$date;
    
    if(($vue == 'jour') or ($vue == 'par_jour') or ($vue == 'mois')){ 
      $title_date = myTools::displayShortDate($title_date[0]).' : '.$nb['nb'].' résultats';
    }
    if($vue == 'par_mois') { 
      $title_date = ucfirst(myTools::displayMoisAnnee($title_date[0])).' : '.$nb['nb'].' résultats';
    }
    if($vue == 'par_mois') { 
      $newargs['date'] = $date.'%2C'.addToDate($date, 'mois');
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
<?php } ?>
<div class="nb_results">
  <h2>Résultats <?php echo $results['start']+1; ?> à <?php echo min($results['end'],$results['numFound']); ?> sur <?php echo $results['numFound']; ?> <strong>triés par <?php echo $sort_type; ?></strong> - 
  <span class="tri">
  <?php 
  $newargs = $selected;
  if ($sort) {
    if (isset($newargs['sort'])) { $newargs['sort'] = 0; }
    echo link_search('trier par pertinence', $query, $newargs, 0); 
  } 
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
  <?php foreach(array_keys($facet) as $k) { if (isset($facet[$k]['values']) && count($facet[$k]['values'])) : ?>
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
      echo link_search($value, $query, $newargs, 0); ?>(<?php echo $nb; ?>)
      </li>
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
  <?php if ($record['photo']) { ?>
  <p class="photo"><a href="<?php echo $record['link']; ?>" rel="nofollow"><?php echo $record['photo']; ?></a></p>
  <?php } ?>
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
    echo link_search('page suivante', $query, $newargs, 0); 
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
    echo link_search('page précédente', $query, $newargs, 0); 
  }
  ?>
  </div>
</div>
</div>
