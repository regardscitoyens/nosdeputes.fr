<h1>Les scrutins publics</h1>

<ul>
<?php 
  foreach ($scrutins as $s) {
?>
<li>
    <a href="<?= url_for('@scrutin?numero='.$s->numero) ?>">
        <?= myTools::displayVeryShortDate($s->date) ?> : <?= $s->titre ?>
    </a>
</li>
<?php
  }
?>
</ul>