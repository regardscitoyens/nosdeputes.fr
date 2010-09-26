<search>
<start><?php echo $results['start'] + 1; ?></start>
<end><?php echo $results['end'] - 1; ?></end>
<last_result><?php echo $results['numFound']; ?></last_result>
<results>
<?php
foreach ($results['docs'] as $record)
{
  echo "<result>";
  echo "<document_type>".get_class($record['object'])."</document_type>";
  echo "<document_id>".$record['object']->id."</document_id>";
  echo "<document_url></document_url>";
  echo "</result>\n";
}
?></results></search>

