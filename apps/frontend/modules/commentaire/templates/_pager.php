<?php use_helper('Text') ?>
<?php foreach($pager->getResults() as $c) 
{ ?>
<div class="commentaire">
<p><a href="<?php echo url_for($c->lien); ?>#commentaire_<?php echo $c->id; ?>"><?php echo $c->getPresentation() ?>, <?php 
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id));
?> a dit le <?php echo myTools::displayDate($c->created_at); ?>:</a></p>
<p><?php echo truncate_text($c->commentaire, 500); ?></p>
<p><a href="<?php echo url_for($c->lien); ?>#commentaire_<?php echo $c->id; ?>">Lire dans le contexte</a></p>
</div>
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
