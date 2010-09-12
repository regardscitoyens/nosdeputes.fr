<?php

$feed->setTitle("Les dernières interventions portant sur \"".$mots."\"");
$feed->setLink('http://'.$_SERVER['HTTP_HOST'].url_for('@search_interventions_mots?search='.$mots));

$query->limit(20);

foreach($query->execute() as $i)
{
  $item = new sfFeedItem();
  $item->setTitle($i->getTitre());
  $item->setLink('http://'.$_SERVER['HTTP_HOST'].$i->getLink());
  $item->setAuthorName($i->Parlementaire->nom);
  $item->setPubdate(strtotime($i->date));
  $item->setUniqueId(get_class($i).$i->id);
  $item->setDescription(utf8_encode(utf8_decode(strip_tags($i))));
  $feed->addItem($item);
}
decorate_with(false);
echo $feed->asXml();
