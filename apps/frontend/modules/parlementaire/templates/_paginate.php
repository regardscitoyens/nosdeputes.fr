<?php if ($pager->haveToPaginate()) : ?>
<div class="pagination">
   <?php if ($pager->getPage() != 1) echo link_to('<img alt="first" title="première page" src="'.$sf_request->getRelativeUrlRoot().'/images/xneth/min2.png"/>&nbsp; ', $link.'page=1'); ?>
    <?php if ($pager->getPage() != 1)  echo link_to('<img alt="left" title="page précédente" src="'.$sf_request->getRelativeUrlRoot().'/images/xneth/left.png"/>&nbsp; ', $link.'page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <span><b><?php echo $page ?></b></span>
      <?php else: ?>
        <span><a title="page <?php echo $page; ?>" href="<?php echo $link.'page='.$page; ?>"><?php echo $page; ?></a></span>
      <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($pager->getPage() != $pager->getLastPage()) echo link_to('<img alt="right" title="page suivante" src="'.$sf_request->getRelativeUrlRoot().'/images/xneth/right.png"/>', $link.'page='.$pager->getNextPage()); ?>
    <?php if ($pager->getPage() != $pager->getLastPage()) echo link_to('<img alt="last" title="dernière page" src="'.$sf_request->getRelativeUrlRoot().'/images/xneth/max2.png"/> ', $link.'page='.$pager->getLastPage()); ?>
</div>
<?php endif; ?>
