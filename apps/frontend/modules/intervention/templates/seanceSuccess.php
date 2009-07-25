<? foreach($interventions as $intervention) : ?>
<div>
<div class='intervenant'><? 
$persos = $intervention->getAllPersonnalitesAndFonctions(); 
if (count($persos)) {
  echo "<a id='".$intervention->getId()."'";
  if ($persos[0][0]->getPageLink())
    echo " href=\"".url_for($persos[0][0]->getPageLink())."\"";
  echo ">";
  echo $persos[0][0]->nom;
  if (isset($persos[0][1])) {
    echo ", ".$persos[0][1];
  }
  echo "&nbsp;:";
  if ($persos[0][0]->getPhoto()) {
    echo "<br/><img width='50' height='64' src=\"".$persos[0][0]->getPhoto()."\">";
  }
  echo "</a>";
 } else {
  echo "<a id='".$intervention->getId()."'/>";
  echo 'Didascalie&nbsp;:';
 }
?></div>
<div class='intervention<? 
if (!$persos) 
  echo ' comment';?>'><ul><? echo $intervention->getIntervention(); ?></ul></div>
</div>
<div class="source">
<a href="<? echo $intervention->getSource(); ?>">source</a> - 
<a href="<? echo url_for('@interventions_seance?seance='.$seance->id); ?>#<? echo $intervention->getId(); ?>">permalink</a>
</div>
<? endforeach; ?>