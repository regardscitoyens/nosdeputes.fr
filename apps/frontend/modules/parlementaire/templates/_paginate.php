<?php if ($pager->haveToPaginate()) : ?>
<div class="pagination">
   <?php if ($pager->getPage() != 1) echo link_to('<< ', $link.'page=1'); ?>
    <?php if ($pager->getPage() != 1)  echo link_to('< ', $link.'page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <?php echo link_to($page, $link.'page='.$page); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($pager->getPage() != $pager->getLastPage()) echo link_to('> ', $link.'page='.$pager->getNextPage()); ?>
    <?php if ($pager->getPage() != $pager->getLastPage()) echo link_to('>> ', $link.'page='.$pager->getLastPage()); ?>
</div>
<?php endif; ?>
