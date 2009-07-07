<p><? echo $pager->getNbResults(); ?> résultats</p>
<ul>
<? foreach($pager->getResults() as $parlementaire) : ?>
<li><? echo link_to($parlementaire->nom, 'parlementaire/show?permalink='.$parlementaire->permalink); ?></li>
<? endforeach ; ?>
</ul>
<div class="pagination">
    <a href="<?php echo url_for('parlementaire/list') ?>?page=1">
   << 
    </a>
 
    <a href="<?php echo url_for('parlementaire/list') ?>?page=<?php echo $pager->getPreviousPage() ?>">
   <
    </a>
 
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <a href="<?php echo url_for('parlementaire/list') ?>?page=<?php echo $page ?>"><?php echo $page ?></a>
      <?php endif; ?>
    <?php endforeach; ?>
 
    <a href="<?php echo url_for('parlementaire/list') ?>?page=<?php echo $pager->getNextPage() ?>">
   >
    </a>
 
    <a href="<?php echo url_for('parlementaire/list') ?>?page=<?php echo $pager->getLastPage() ?>">
   >>
    </a>
  </div>
