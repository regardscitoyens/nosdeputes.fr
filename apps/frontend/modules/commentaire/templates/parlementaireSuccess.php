<div class="temp">
<?php
$titre = 'Commentaires sur le travail parlementaire';
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
echo include_component('commentaire', 'pager', array('query_commentaires' => $q_commentaires)); ?>
</div>