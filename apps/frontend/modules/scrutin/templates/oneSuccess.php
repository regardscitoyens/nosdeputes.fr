<h1>Scrutin publique <?= $scrutin->type ?></h1>
<h3><?= $scrutin->titre ?></h3>
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
    Demandeurs: 
    <ul>
        <?php foreach($scrutin->demandeurs as $dem) { ?>
        <li><?= $dem ?></li>
        <?php } ?>
    </ul>
</p>