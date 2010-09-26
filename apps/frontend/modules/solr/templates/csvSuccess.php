type document;document id;url vers document;resultats <?php echo $results['start'] + 1; ?> à <?php echo $results['end'] - 1; ?> sur <?php echo $results['numFound']; ?>

<?php

foreach ($results['docs'] as $record)
{
  echo get_class($record['object']);
  echo ";";
  echo $record['object']->id;
  echo ";\n";
}

