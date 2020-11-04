<h1>Les scrutins publics</h1>

<ul>
<?php 
  foreach ($scrutins as $s) {
?>
<li>
    <a href="<?= $s->getURL() ?>">
        <?= myTools::displayVeryShortDate($s->date) ?> : <?= $s->titre ?>
    </a>
</li>
<?php
  }
?>
</ul>