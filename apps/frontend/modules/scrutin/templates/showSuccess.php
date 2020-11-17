<h1>Scrutin public <?= $scrutin->type ?> n°<?= $scrutin->numero ?></h1>
<h3>Sur <?= $scrutin->titre ?></h3>
<div class="source"><a href="<?= $scrutin->getURLInstitution() ?>">source</a></div>

<p>
    Voté dans l'hémicycle en <a href="<?= url_for('@interventions_seance?seance='.$scrutin->seance_id) ?>">séance publique le <?= myTools::displayDate($scrutin->date) ?></a>
<p>
    Résultat : <strong><?= $scrutin->sort ?></strong>
    <ul>
        <li>Pour : <?= $scrutin->nombre_pours ?></li>
        <li>Contre : <?= $scrutin->nombre_contres ?></li>
        <li>Abstention : <?= $scrutin->nombre_abstentions ?></li>
    </ul>
</p>
<?php if (!empty($scrutin->demandeurs)) { ?>
<p>
    À la demande de : <?php
    if (count($scrutin->demandeurs) > 1) {
        echo join(', ', array_slice($scrutin->demandeurs, 0, count($scrutin->demandeurs)-1)).' et '.end($scrutin->demandeurs);
    } else {
        echo $scrutin->demandeurs[0];
    }
    ?>.
</p>
<?php } ?>

<p>Votes :</p>
<ul>
<?php
  foreach ($grouped_votes as $g) {
?>
<li>
    <?= $g[0]->parlementaire_groupe_acronyme ?>
    <ul>
    <?php
      foreach ($g as $vote) {
    ?>
        <li>
            <?php $parl = $vote->getParlementaire(); ?>
            <a href="<?= url_for('@parlementaire?slug='.$parl->slug) ?>"><?= $parl->nom ?></a> :

            <strong class="vote-<?= $vote->position ?>">
            <?= $vote->getHumanPosition() ?></strong><strong><?php if ($vote->par_delegation) { ?>, par délégation<?php } ?>
            </strong>

            <?php if ($vote->mise_au_point_position) { ?>
                (mise au point: <strong class="vote-<?= $vote->mise_au_point_position ?>"><?= $vote->getHumanPositionMiseAuPoint() ?></strong>)
            <?php } ?>
        </li>
    <?php } ?>
    </ul>
</li>
<?php } ?>
</ul>
</p>
