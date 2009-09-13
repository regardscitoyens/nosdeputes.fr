<?php foreach($pager->getResults() as $c) 
{
$nomPartial = (isset($partial)) ? 'show'.ucfirst($partial).'Commentaire' : 'showCommentaire';
include_partial($nomPartial, array('c' => $c)); ?>
<?php } ?>
<?php if ($pager->haveToPaginate()) :

$uri = $sf_request->getUri();
$uri = preg_replace('/page=\d+\&?/', '', $uri);

if (!preg_match('/[\&\?]$/', $uri)) {
  if (preg_match('/\?/', $uri)) {
    $uri .= '&';
  }else{
    $uri .= '?';
  }
}
?>
<div class="pagination">
    <?php echo link_to('<< ', $uri.'page=1'); ?>
    <?php echo link_to('< ', $uri.'page='.$pager->getPreviousPage()); ?>
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <?php echo link_to($page, $uri.'page='.$page); ?>
      <?php endif; ?>
    <?php endforeach; ?>
    <?php echo link_to('> ', $uri.'page='.$pager->getNextPage()); ?>
    <?php echo link_to('>> ', $uri.'page='.$pager->getLastPage()); ?>
</div>
<?php endif; ?>
