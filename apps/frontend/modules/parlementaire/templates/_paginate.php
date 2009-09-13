<?php if ($pager->haveToPaginate()) : ?>
<div class="pagination">
    <?php echo link_to('<< ', $link.'page=1'); ?>
    <?php echo link_to('< ', $link.'page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <?php echo link_to($page, $link.'page='.$page); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <?php echo link_to('> ', $link.'page='.$pager->getNextPage()); ?>
    <?php echo link_to('>> ', $link.'page='.$pager->getLastPage()); ?>
</div>
<?php endif; ?>
