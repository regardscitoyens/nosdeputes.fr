{
  "start": <?php echo $results['start'] + 1; ?>,
    "end" : <?php echo $results['end'] - 1; ?>,
      "last_result" : <?php echo $results['numFound'];  ?>,
	"results" : {
<?php
	    $nb = 0;
foreach ($results['docs'] as $record)
{
  if ($nb)
    echo ",";
  else
    $nb = 1;
  echo "[document_type:\"".get_class($record['object'])."\",";
  echo "document_id:".$record['object']->id.",";
  echo "document_url:\"\"]";
}
	  ?> }}

