<ul>
<?php 
  foreach ($votes as $v) {
?>
<li>
    <a href="<?= $v->getScrutin()->getURL() ?>">
        <?= myTools::displayVeryShortDate($v->getScrutin()->date) ?> : <?= $v->getScrutin()->titre ?>
        (<strong class="vote-<?= $v->position ?>"><?= $v->position ?></strong>)
        <?php if ($v->mise_au_point_position) { ?>
            (mise au point: <strong class="vote-<?= $v->mise_au_point_position ?>"><?= $v->getHumanPositionMiseAuPoint() ?></strong>)
        <?php } ?>
    </a>
</li>
<?php
  }
?>
</ul>