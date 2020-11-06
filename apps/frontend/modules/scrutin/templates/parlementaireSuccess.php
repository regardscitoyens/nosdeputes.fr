<?php
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => "Votes") );
?>


<table class="scrutins">
    <tr>
        <td>
            <h2>Votes sur l'ensemble</h2>

            <ul>
            <?php 
              foreach ($scrutins as $s) {
                if ($s->isOnWholeText()) {
                    $vote = null;
                    foreach ($votes as $v) {
                        if ($v->scrutin_id == $s->id) {
                            $vote = $v; 
                            break;
                        }
                    }
                    echo include_component('scrutin', 'vote', array(
                        'vote' => $vote,
                        'scrutin' => $s,
                        'titre' => $s->titre)
                    );
                }
              }
            ?>
            </ul>
        </td>

        <td classs='votes-autres'>
            <h2>Autres votes</h2>

            <ul>
            <?php 
              foreach ($grouped_scrutins as $g) {
            ?>
                <li>
                    <?= $g[0]->getLaw() ?>

                    <ul>
                    <?php 
                      foreach ($g as $s) {
                        $vote = null;
                        foreach ($votes as $v) {
                            if ($v->scrutin_id == $s->id) {
                                $vote = $v; 
                                break;
                            }
                        }
                        echo include_component('scrutin', 'vote', array(
                            'vote' => $vote,
                            'scrutin' => $s,
                            'titre' => str_replace($s->getLaw(), '...', $s->titre))
                        );
                      }
                    ?>
                    </ul>
                </li>
            <?php } ?>
            </ul>
        </td>
    </tr>
</table>