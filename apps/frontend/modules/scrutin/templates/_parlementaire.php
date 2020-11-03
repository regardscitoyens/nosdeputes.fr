<ul>
<?php 
  foreach ($votes as $vote) {
    echo include_partial('scrutin/vote', array(
        'vote' => $vote,
        'scrutin' => $vote->getScrutin(),
        'titre' => $vote->getScrutin()->titre)
    );
  }
?>
</ul>