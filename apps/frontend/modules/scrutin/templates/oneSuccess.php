<h1>Scrutin public n°<?= $scrutin->numero ?> <?= $scrutin->type ?></h1>
<h3>Sur <?= $scrutin->titre ?></h3>
<div class="source"><a href="<?= $scrutin->getURLInstitution() ?>">source</a></div>

<p>
    Date: <?= myTools::displayVeryShortDate($scrutin->date) ?>
<p>
    Résultat: <strong><?= $scrutin->sort ?></strong>
    <ul>
        <li>Pour: <?= $scrutin->nombre_pours ?></li>
        <li>Contre: <?= $scrutin->nombre_contres ?></li>
        <li>Abstention: <?= $scrutin->nombre_abstentions ?></li>
    </ul>
</p>
<p>
    À la demande de: 
    <ul>
        <?php foreach($scrutin->demandeurs as $dem) { ?>
        <li><?= $dem ?></li>
        <?php } ?>
    </ul>
</p>

<p>Votes:</p>
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
            <?= $vote->getParlementaire()->nom ?> :

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