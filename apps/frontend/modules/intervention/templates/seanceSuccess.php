<? foreach($seance->getInterventions() as $intervention) : ?>
<div>
<div class='intervenant'><? 
$persos = $intervention->getAllPersonnalites(); 
if (count($persos)) 
  echo $persos[0]->nom;
else
  echo 'Didascalie';
?>&nbsp;:</div>
<div class='intervention<? 
if (!$persos) 
  echo ' comment';?>'><ul><? echo $intervention->getIntervention(); ?></ul></div>
</div>
<div class="source"><a href="<? echo $intervention->getSource(); ?>">source</a></div>
<? endforeach; ?>