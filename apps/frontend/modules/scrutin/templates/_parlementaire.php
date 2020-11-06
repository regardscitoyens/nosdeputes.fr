<ul>
<?php 
  foreach ($voteotes as $vote) {
?>
<li>
    <a href="<?= $vote->getScrutin()->getURL() ?>">
        <?= myTools::displayVeryShortDate($vote->getScrutin()->date) ?> :

        <strong class="vote-<?= $vote->position ?>">
        <?= $vote->getHumanPosition() ?></strong><strong><?php if ($vote->par_delegation) { ?>, par délégation,<?php } ?>
        </strong>

        <?php if ($vote->mise_au_point_position) { ?>
            (mise au point: <strong class="vote-<?= $vote->mise_au_point_position ?>"><?= $vote->getHumanPositionMiseAuPoint() ?></strong>)
        <?php } ?>

        <?php if ($vote->position == 'abstention' || $vote->position == 'nonVotant') { ?>
            sur
        <?php } ?>

        <?= $vote->getScrutin()->titre ?>
    </a>
</li>
<?php
  }
?>
</ul>