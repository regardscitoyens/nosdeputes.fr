<?php if (!$tags) : ?>
<search>
<start><?php echo $results['start'] + 1; ?></start>
<end><?php if ($results['numFound'] == $results['end'] - 1) echo $results['end'] - 1; else echo $results['end']; ?></end>
<last_result><?php echo $results['numFound']; ?></last_result>
<results>
<?php
foreach ($results['docs'] as $record)
{
  echo "<result>";
  echo "<document_type>".get_class($record['object'])."</document_type>";
  echo "<document_id>".$record['object']->id."</document_id>";
  echo "<document_url>".preg_replace('/([^:])\/\//', '\1/', sfConfig::get('app_base_url').url_for('@api_document?format='.$format.'&class='.get_class($record['object']).'&id='.$record['object']->id))."</document_url>";
  echo "<document_intervenant>".$record['personne']."</document_intervenant>";
  echo "<document_content>". preg_replace('/^'.$record['personne'].' /', '', preg_replace('/<\/?em>/', '', $record['highlighting']))."</document_content>";
  echo "<document_date>".$record['date'].'</document_date>';
  echo "</result>\n";
}
?></results></search>
<?php return ;
else: ?>
<search>
<tags>
<?php
foreach(array_keys($facet) as $k)
  if (isset($facet[$k]['values']) && count($facet[$k]['values']))
    foreach($facet[$k]['values'] as $value => $nb)
     if ($nb)
       echo "<tag><type>$k</type><nom>$value</nom><nb>$nb</nb></tag>\n";

?></tags>
</search>
<?php endif; ?>
