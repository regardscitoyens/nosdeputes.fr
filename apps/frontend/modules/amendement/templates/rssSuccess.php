<?php

$feed->setTitle("Les dernières amendements portant sur \"".$mots."\"");
$feed->setLink('http://'.$_SERVER['HTTP_HOST'].url_for('@search_amendements_mots?search='.$mots));

$query->limit(20);

foreach($query->execute() as $a)
{
  $item = new sfFeedItem();
  $item->setTitle(strip_tags($a->getTitre()));
  $item->setLink('http://'.$_SERVER['HTTP_HOST'].$a->getLink());
  $item->setPubdate(strtotime($a->date));
  $item->setUniqueId(get_class($a).$a->id);
  $item->setDescription(utf8_encode(utf8_decode(strip_tags($a))));
  $feed->addItem($item);
}
decorate_with(false);
echo $feed->asXml();
