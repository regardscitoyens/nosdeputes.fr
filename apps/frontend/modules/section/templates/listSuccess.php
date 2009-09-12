<div class="temp">
<ul><?php foreach($sections as $s) if ($s->titre) {
if (preg_match('/(questions?\s|ordre\sdu\sjour|nomination|suspension\sde\séance|rappels?\sau\srèglement)/i', $s->titre)) continue;
  echo '<li>';
  echo link_to(ucfirst($s->titre), '@section?id='.$s->id);
  echo ' ('.$s->nb_interventions.' intervention';
  if ($s->nb_interventions > 1) echo 's';
  echo ')</li>';
 } ?></ul>
</div>
