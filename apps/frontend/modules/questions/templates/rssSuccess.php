<?php

$feed->setTitle("Les dernières questions écrites portant sur \"".$mots."\"");
$feed->setLink('http://'.$_SERVER['HTTP_HOST'].url_for('@search_questions_ecrites_mots?search='.$mots));

$query->limit(20);

foreach($query->execute() as $q)
{
  $item = new sfFeedItem();
  $item->setTitle($q->getTitre());
  $item->setLink('http://'.$_SERVER['HTTP_HOST'].$q->getLink());
  $item->setAuthorName($q->Parlementaire->nom);
  $item->setPubdate(strtotime($q->getLastDate()));
  $item->setUniqueId(get_class($q).$q->id);
  $item->setDescription(utf8_encode(utf8_decode(strip_tags($q))));
  $feed->addItem($item);
}
decorate_with(false);
echo $feed->asXml();
