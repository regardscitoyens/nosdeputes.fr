<?php if ($pager->haveToPaginate()) : ?>
<div class="pagination">
   <?php if ($pager->getPage() != 1) echo link_to('<img src="'.$sf_request->getRelativeUrlRoot().'/images/xneth/min2.png"/>&nbsp; ', $link.'pages=1'); ?>
    <?php if ($pager->getPage() != 1)  echo link_to('<img src="'.$sf_request->getRelativeUrlRoot().'/images/xneth/left.png"/>&nbsp; ', $link.'pages='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <span><b><?php echo $page ?></b></span>
      <?php else: ?>
        <span><?php echo link_to($page, $link.'pages='.$page); ?></span>
      <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($pager->getPage() != $pager->getLastPage()) echo link_to('<img src="'.$sf_request->getRelativeUrlRoot().'/images/xneth/right.png"/>', $link.'pages='.$pager->getNextPage()); ?>
    <?php if ($pager->getPage() != $pager->getLastPage()) echo link_to('<img src="'.$sf_request->getRelativeUrlRoot().'/images/xneth/max2.png"/> ', $link.'pages='.$pager->getLastPage()); ?>
</div>
<?php endif; ?>
