{
<?php

foreach($commentaires as $c) 
{
  if (!$c['nb_commentaires'])
    continue;
  echo '"'.$c['id'].'": "'.$c['nb_commentaires'].'",';
  echo "\n";
}

  ?> "-1":""}
