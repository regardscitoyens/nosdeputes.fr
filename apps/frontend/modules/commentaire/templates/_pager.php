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
include_partial('parlementaire/paginate', array('pager'=>$pager, 'link'=>$uri);
endif;
