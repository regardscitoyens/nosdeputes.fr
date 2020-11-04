<li>
    <a href="<?= url_for('@scrutin?numero='.$scrutin->numero) ?>">
        <?= myTools::displayVeryShortDate($scrutin->date) ?> :

        <?php if ($vote) { ?>
            <strong class="vote-<?= $vote->position ?>">
            <?= $vote->getHumanPosition() ?></strong><strong><?php if ($vote->par_delegation) { ?>, par délégation,<?php } ?>
            </strong>

            <?php if ($vote->mise_au_point_position) { ?>
                (mise au point: <strong class="vote-<?= $vote->mise_au_point_position ?>"><?= $vote->getHumanPositionMiseAuPoint() ?></strong>)
            <?php } ?>

            <?php if ($vote->position == 'abstention' || $vote->position == 'nonVotant') { ?>
                sur
            <?php } ?>

        <?php }  else { ?>
            <strong class="vote-absent">n'as pas voté</strong> sur
        <?php } ?>

        <?= $titre ?>
    </a>
</li>