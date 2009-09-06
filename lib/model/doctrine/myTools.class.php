<?php
class myTools {

   static $num_mois = array(
     "01" => "janvier",
     "02" => "février",
     "03" => "mars",
     "04" => "avril",
     "05" => "mai",
     "06" => "juin",
     "07" => "juillet",
     "08" => "août",
     "09" => "septembre",
     "10" => "octobre",
     "11" => "novembre",
     "12" => "decembre");
  public static function displayDate($date) {
    if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $date, $match)) {
      if ($match[3] == '1') $match[3] .= 'er';
      return $match[3].' '.self::$num_mois[$match[2]].' '.$match[1];
    } else return $date;
  }

  public static function clearHtml($s, $authorized_tags = '<strong><i><b><a><em>') {
	sfApplicationConfiguration::loadHelpers(array('Url'));
    if ($authorized_tags)
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
