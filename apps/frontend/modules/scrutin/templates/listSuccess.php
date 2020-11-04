<table class="scrutins">
    <tr>
        <td>
            <h2>Scrutins sur l'ensemble</h2>
            <ul>
            <?php 
              foreach ($scrutins_ensemble as $s) {
                echo include_partial('scrutin/scrutin', array(
                    'scrutin' => $s,
                    'titre' => $s->titre)
                );
              }
            ?>
            </ul>
        </td>

        <td classs='votes-autres'>
            <h2>Autres scrutins</h2>
            <ul>
            <?php 
              foreach ($grouped_scrutins as $g) {
            ?>
                <li>
                    <?= $g[0]->getLaw() ?>
                    <ul>
                    <?php 
                      foreach ($g as $s) {
                        echo include_partial('scrutin/scrutin', array(
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