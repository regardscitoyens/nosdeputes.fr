<?php

if ($title)
  $feed->setTitle("NosDeputes.fr : $title");
 else
   $feed->setTitle("NosDeputes.fr : Recherche sur $query");
$feed->setLink('http://'.$_SERVER['HTTP_HOST'].url_for('@recherche_solr?query='.$query));

foreach ($results['docs'] as $record)
{
  $item = new sfFeedItem();
  $item->setTitle($record['titre']);
  $item->setLink('http://'.$_SERVER['HTTP_HOST'].$record['link']);
  $item->setAuthorName($record['personne']);
  $item->setPubdate(strtotime($record['date']));
  $item->setUniqueId($record['id']);
  $item->setDescription($record['highlighting']);
  $feed->addItem($item);
}
decorate_with(false);
echo $feed->asXml();
