<?php

$feed->setTitle("Les commentaires portant sur l'activité de ".$parlementaire->nom);
$feed->setLink('http://'.$_SERVER['HTTP_HOST'].url_for('@parlementaire_commentaires?slug='.$parlementaire->slug));
foreach($commentaires as $c)
{
  $item = new sfFeedItem();
  $item->setTitle($c->getPresentation());
  $item->setLink('http://'.$_SERVER['HTTP_HOST'].url_for($c->getLien()));
  $item->setAuthorName($c->getCitoyen()->login);
  $item->setPubdate(strtotime($c->created_at));
  $item->setUniqueId('Commentaire'.$c->id);
  $item->setDescription($c);
  $feed->addItem($item);
}

decorate_with(false);
echo $feed->asXml();
