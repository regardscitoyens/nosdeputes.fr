<?php if (!$tags) :?>
type document;document id;url vers document;resultats <?php echo $results['start'] + 1; ?> à <?php if ($results['numFound'] == $results['end'] - 1) echo $results['end'] - 1; else echo $results['end']; ?> sur <?php echo $results['numFound']; ?>

<?php

foreach ($results['docs'] as $record)
{
  echo get_class($record['object']);
  echo ";";
  echo $record['object']->id;
  echo url_for('@api_document?format='.$format.'&class='.get_class($record['object']).'&id='.$record['object']->id)."\n";
  echo ";\n";
}
return;
endif;
?>
tag type;tag nom;nb
<?php
foreach(array_keys($facet) as $k)
  if (isset($facet[$k]['values']) && count($facet[$k]['values']))
    foreach($facet[$k]['values'] as $value => $nb)
     if ($nb)
       echo "$k:$value:$nb\n";
