<?php
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => "Votes") );
?>


<table>
    <tr>
        <td>
            <h2>Votes sur l'ensemble</h2>

            <ul>
            <?php 
              foreach ($scrutins as $s) {
                if ($s->isOnWholeText()) {
            ?>
            <li>
                <a href="<?= $s->getURL() ?>">
                    <?= myTools::displayVeryShortDate($s->date) ?> :

                    <?php
                    $vote = null;
                    foreach ($votes as $v) {
                        if ($v->scrutin_id == $s->id) {
                            $vote = $v; 
                            break;
                        }
                    }
                    ?>

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

                    <?= $s->titre ?>
                </a>
            </li>
            <?php
                }
              }
            ?>
            </ul>
        </td>

        <td classs='votes-autres'>
            <h2>Autres votes</h2>

            <?php
            // group scrutins by law
            $grouped_scrutins = array();
            $current_group = false;
            foreach($scrutins as $s) {
                if (!$s->isOnWholeText()) {
                    if ($current_group && $current_group[0]->getLaw() != $s->getLaw()) {
                        $grouped_scrutins[] = $current_group;
                        $current_group = array();
                    }
                    $current_group[] = $s;
                }
            }
            if ($current_group) {
                $grouped_scrutins[] = $current_group;
            }
            ?>

            <ul>
            <?php 
              foreach ($grouped_scrutins as $g) {
            ?>
                <li>
                    <?= $g[0]->getLaw() ?>

                    <ul>
                    <?php 
                      foreach ($g as $s) {
                    ?>
                        <li>
                            <a href="<?= $s->getURL() ?>">
                                <?= myTools::displayVeryShortDate($s->date) ?> :

                                <?php
                                $vote = null;
                                foreach ($votes as $v) {
                                    if ($v->scrutin_id == $s->id) {
                                        $vote = $v; 
                                        break;
                                    }
                                }
                                ?>

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

                                <?= str_replace($s->getLaw(), '...', $s->titre) ?>
                            </a>
                        </li>
                    <?php
                      }
                    ?>
                    </ul>
                </li>
            <?php } ?>
            </ul>
        </td>
    </tr>
</table>

<style>
td {
    width: 50%;
    padding: 10px;
    vertical-align: top;
}
td.votes-autres {
    border-left: 1px solid black;
}
</style>