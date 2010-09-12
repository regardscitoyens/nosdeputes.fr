<?php

$feed->setTitle("L'activité de ".$parlementaire->nom);
$feed->setLink('http://'.$_SERVER['HTTP_HOST'].url_for('@parlementaire?slug='.$parlementaire->slug));
$i = 0;
for ($i = 0 ; $i < $limit ; $i++)
{
  if (!isset($news[$i]))
    break;
  $new = $news[$i];
  $item = new sfFeedItem();
  $item->setTitle(strip_tags($new->getTitre()));
  $item->setLink('http://'.$_SERVER['HTTP_HOST'].$new->getLink());
  $item->setAuthorName($parlementaire->nom);
  $item->setPubdate(strtotime($new->date));
  $item->setUniqueId(get_class($new).$new->id);
  $item->setDescription(utf8_encode(utf8_decode(strip_tags($new))));
  $feed->addItem($item);
}
decorate_with(false);
echo $feed->asXml();
