<?php
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => "Votes") );
?>


<table>
    <tr>
        <?php
        $columns = array(
            'votes-ensemble',
            'votes-autres',
        );
        foreach ($columns as $c) {
        ?>

        <td classs="<?= $c ?>">

            <?php if ($c == 'votes-ensemble') { ?>
                <h2>Votes sur l'ensemble</h2>
            <?php } else { ?>
                <h2>Autres votes</h2>
            <?php } ?>

            <ul>
            <?php 
              foreach ($scrutins as $s) {
                if ($c == 'votes-autres' || $s->isOnWholeText()) {
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
                        <strong class="vote-<?= $vote->position ?>"><?= $vote->getHumanPosition() ?></strong>

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
    <?php } ?>
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