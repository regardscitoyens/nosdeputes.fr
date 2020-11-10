<li>
    <a href="<?= url_for('@scrutin?numero='.$scrutin->numero) ?>">
        <?= myTools::displayVeryShortDate($scrutin->date) ?> :
        <?= $titre ?>
    </a>
</li>