<div class="temp">
<ul><?php foreach($sections as $s) if ($s->titre)
{ 
  echo '<li>';
  echo link_to($s->titre, '@section?id='.$s->id);
  echo ' ('.$s->nb_interventions.' intervention';
  if ($s->nb_interventions > 1) echo 's';
  echo ')</li>';
 } ?></ul>
</div>