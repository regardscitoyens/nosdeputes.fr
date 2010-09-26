<?php if (!$tags) : ?>
{ "start": <?php echo $results['start'] + 1; ?>, "end" : <?php echo $results['end'] - 1; ?>, "last_result" : <?php echo $results['numFound'];  ?>, "results" : {<?php
	    $nb = 0;
foreach ($results['docs'] as $record)
{
  if ($nb)
    echo ",";
  else
    $nb = 1;
  echo "[document_type:\"".get_class($record['object'])."\",";
  echo "document_id:".$record['object']->id.",";
  echo "document_url:\"".sfConfig('app_baseurl').url_for('@api_document?type='.get_class($record['object']).'&id='.$record['object']->id)."\"]";
}
?> }}<?php return;
endif;
?>
{
<?php
    $cpt = 0;
foreach(array_keys($facet) as $k)
  if (isset($facet[$k]['values']) && count($facet[$k]['values']))
    foreach($facet[$k]['values'] as $value => $nb) 
      if ($nb) {
	if ($cpt)
	  echo ",";
	else
	  $cpt = 1;
	echo "[\"tag_type\":\"$k\";\"tag_nom\":\"$value\",\"nb\":$nb]\n";
      }
  ?>}
