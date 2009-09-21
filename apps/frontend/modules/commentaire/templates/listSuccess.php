<h1>Les derniers commentaires</h1>
<div class="last_commentaires">
<?php echo include_component('commentaire', 'pager', array('query_commentaires' => $comments, 'partial' => 'Trunc'));  ?>
</div>