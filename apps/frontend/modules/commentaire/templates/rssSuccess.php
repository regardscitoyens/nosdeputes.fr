<?php

if ($type == 'Parlementaire')
  $feed->setTitle($titre." de ".$object->nom);
else $feed->setTitle($titre);
$feed->setLink('http://'.$_SERVER['HTTP_HOST'].url_for(preg_replace('/_rss/', '', $linkrss)));
foreach($comments as $c)
{
  $auteur = $c->getCitoyen()->login;
  $item = new sfFeedItem();
  if (isset($presentation))
    $item->setTitle($c->getPresentation($presentation).', '.$auteur.' a dit');
  else $item->setTitle($c->getPresentation());
  $item->setLink($c->getLien().'#commentaire_'.$c->id);
  $item->setAuthorName($auteur);
  $item->setPubdate(strtotime($c->created_at));
  $item->setUniqueId('Commentaire'.$c->id);
  $item->setDescription(utf8_encode(utf8_decode(strip_tags($c))));
  $feed->addItem($item);
}

decorate_with(false);
echo $feed->asXml();
