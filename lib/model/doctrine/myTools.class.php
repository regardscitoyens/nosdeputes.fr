<?php
sfLoader::loadHelpers(array('Url'));

class myTools {
  public static function clearHtml($s, $authorized_tags = '<strong><i><b><a><em>') {
    if ($authorized)
      $s = strip_tags($s, $authorized_tags.'<depute>');

    //Protection des liens
    $s = preg_replace('/on[^=\s]+=[^\s>]+/i', '', $s);
    $s = preg_replace('/=[\'"]?javascript:[^\s\>]+/i', '=""', $s);
    $s = preg_replace('/<a /i', '<a rel="nofollow" ', $s);
    //Convertion des urls en liens
    $s = preg_replace('/(^|\s)(http\S+)/', ' <a rel="nofollow" href="\\2">\\2</a>', $s);
    if (preg_match_all('/(({+|<depute>)([^}<]+)(}+|<\/?depute>))/i', $s, $matches)) {
      for($i = 0 ; $i < count($matches[0]) ; $i++) {
	$parlementaire = Doctrine::getTable('Parlementaire')->similarTo($matches[3][$i]);
	$matches[1][$i] = preg_replace('/\//', '', $matches[1][$i]);
	if ($parlementaire) {
	  $s = preg_replace('/'.$matches[1][$i].'/', '<a href="'.url_for('@parlementaire?slug='.$parlementaire->slug).'"><img src="'.url_for('@photo_parlementaire?slug='.$parlementaire->slug).'/20" height=20/>'.$parlementaire->nom.'</a>', $s);
	}else{
	  $s = preg_replace('/'.$matches[1][$i].'/', '<b class="erreur" style="color:red">'.$matches[2][$i].'</b>', $s);
	}
      }
    }
    $s = '<p>'.$s.'</p>'; 
    $s = preg_replace('/\n/', '</p><p>', $s);
    return $s;
  }
}