<?php

if ($type == 'Parlementaire')
  $feed->setTitle($titre." de ".$object->nom);
else $feed->setTitle($titre);
$feed->setLink('http://'.$_SERVER['HTTP_HOST'].url_for(preg_replace('/_rss/', '', $linkrss)));
foreach($comments as $c)
{
  $item = new sfFeedItem();
  if (isset($presentation))
    $item->setTitle($c->getPresentation($presentation));
  else $item->setTitle($c->getPresentation());
  $item->setLink('http://'.$_SERVER['HTTP_HOST'].url_for($c->getLien()));
  $item->setAuthorName($c->getCitoyen()->login);
  $item->setPubdate(strtotime($c->created_at));
  $item->setUniqueId('Commentaire'.$c->id);
  $item->setDescription(utf8_encode(utf8_decode(strip_tags($c))));
  $feed->addItem($item);
}

decorate_with(false);
echo $feed->asXml();
