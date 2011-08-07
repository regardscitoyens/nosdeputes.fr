<?php if ($type == 'Parlementaire')
  echo include_component('parlementaire', 'header', array('parlementaire' => $object, 'titre' => $titre, 'rss' => $linkrss));
else { ?>
<h1><?php if (isset($titre)) echo preg_replace('/^Les derniers commentaires sur (.*)$/', 'Les derniers commentaires sur <a href="'.url_for($url_link).'">\1</a>', $titre); ?><span class="rss"><a href="<?php echo url_for($linkrss); ?>"><img src="<?php echo $sf_request->getRelativeUrlRoot(); ?>/images/xneth/rss.png" alt="Les derniers commentaires en RSS"/></a></span></h1>
<?php } ?>
<div class="last_commentaires">
<p class="list_com"><?php if (isset($object)) {
  if ($object->nb_commentaires > 0) {
    echo $object->nb_commentaires.' commentaire';
    if ($object->nb_commentaires > 1) echo 's';
  } else echo 'Aucun commentaire n\'a encore été formulé';
} ?></p>
<?php $options = array('query_commentaires' => $commentaires, 'partial' => 'Trunc');
if (isset($presentation)) $options = array_merge($options, array('presentation' => $presentation));
echo include_component('commentaire', 'pager', $options);  ?>
</div>
