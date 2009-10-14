<h1>Les derniers commentaires<span class="rss"><a href="<?php echo url_for('@commentaires_rss'); ?>"><img src="/images/xneth/rss.png" alt="Flux rss"/></a></span></h1>
<div class="last_commentaires">
<?php echo include_component('commentaire', 'pager', array('query_commentaires' => $comments, 'partial' => 'Trunc'));  ?>
</div>