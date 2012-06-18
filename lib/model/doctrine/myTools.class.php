<?php
class myTools {


  public static function displayVCards($adresses, $mails) {
    foreach (unserialize($adresses) as $adresse) {
      if(trim($adresse) != '') {
        preg_match('/^([^0-9]+)/', $adresse, $titre);
        preg_match('/([0-9]{5})/', $adresse, $code_postal);
        preg_match('/(([-. ]?[0-9]{2}){5})/', $adresse, $tel);
        if(!isset($tel[0])) { $tel[0] = '###'; }
        $replace = array(
          $titre[0] => '<div class="fn org">'.$titre[0].'</div>',
          $code_postal[0] => '<br /><span class="postal-code">'.$code_postal[0].'</span>',
          'Téléphone :'.$tel[0] => '<br />Téléphone : <span class="tel"><a href="callto:'.preg_replace('/0/', '0033', preg_replace('/[^0-9]/', '', $tel[0]), 1).'">'.trim($tel[0]).'</a></span>',
          'Télécopie' => '<br /><span class="tel"><span class="type">Fax</span>'
        );
        echo '<div class="vcard">';
        echo strtr($adresse, $replace);
        echo '</div>';
      }
    }
    echo '<div class="stopfloat"></div>';
  }


  public static function betterUCFirst($str) {
    $str = ucfirst($str);
    $str = preg_replace('/^é/', 'É', $str);
    $str = preg_replace('/^ê/', 'Ê', $str);
    $str = preg_replace('/^â/', 'Â', $str);
    $str = preg_replace('/^à/', 'À', $str);
    return $str;
  }

  public static function convertYamlToArray($string) {
    $string = preg_replace('/^\s*\[\s*"\s*/', '', $string);
    $string = preg_replace('/\s*"\s*]\s*$/', '', $string);
    $string = preg_replace('/",\s+"/', '","', $string);
    return explode('","', $string);
  }

  public static function getDebutLegislature() {
    $date = sfConfig::get('app_debut_legislature');
    if (!$date)
      $date = "2007-06-20";
    return $date;
  }

  public static function isFinLegislature() {
    return (sfConfig::get('app_fin_legislature'));
  }

  public static function isLegislatureCloturee() {
    return preg_match('/clotur/', sfConfig::get('app_fin_legislature'));
  }

  public static function getGroupesInfos() {
    $conf = sfConfig::get('app_groupes_infos', '');
    if (!$conf) {
      $config = sfYaml::load(dirname(__FILE__).'/../../../config/app.yml');
      $conf = $config['all']['groupes_infos'];
    }
    $gpes = self::convertYamlToArray($conf);
    $res = array();
    foreach ($gpes as $gpe)
      $res[] = explode(' / ', $gpe);
    return $res;
  }

  public static function getGroupesInfosOrder() {
    $gpes = self::getGroupesInfos();
    $map = array();
    foreach ($gpes as $gpe)
      $map[$gpe[1]] = $gpe;
    $gpes = array();
    foreach (self::convertYamlToArray(sfConfig::get('app_groupes_actuels', '')) as $gpe)
      $gpes[] = $map[$gpe];
    return $gpes;
  }

  public static function getGroupesColorMap() {
    $colormap = array();
    foreach (myTools::getGroupesInfos() as $gpe)
      $colormap[$gpe[1]] = $gpe[2];
    return $colormap;
  }

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
     "12" => "décembre");

  public static function displayDate($date) {
    if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $date, $match)) {
      $match[3] = preg_replace('/^0(\d)/', '\\1', $match[3]);
      if ($match[3] == '1') $match[3] .= 'er';
      return $match[3].' '.self::$num_mois[$match[2]].' '.$match[1];
    } else return $date;
  }

  public static function displayDateMoisAnnee($date) {
    if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $date, $match)) {
      return self::$num_mois[$match[2]].' '.$match[1];
    } else return $date;
  }

  static $day_week = array(
     "0" => "Dimanche",
     "1" => "Lundi",
     "2" => "Mardi",
     "3" => "Mercredi",
     "4" => "Jeudi",
     "5" => "Vendredi",
     "6" => "Samedi");

  public static function displayDateSemaine($date) {
    $day = self::$day_week[date('w', strtotime($date))];
    return $day.' '.self::displayDate($date);
  }

  public static function displayShortDate($d) {
    $d = preg_replace ('/\-/', '', $d);
    $date = substr($d,6,2)."/";        // jour
    $date = $date.substr($d,4,2)."/";  // mois
    $date = $date.substr($d,0,4);      // année
    return $date;
  }

  public static function displayVeryShortDate($d) {
    $d = preg_replace ('/\-/', '', $d);
    $date = substr($d,6,2)."/";        // jour
    $date = $date.substr($d,4,2)."/";  // mois
    $date = $date.substr($d,2,2);      // année
    return $date;
  }

  public static function displayMoisAnnee($d) {
    $d = preg_replace ('/\-/', '', $d);
    $date = self::$num_mois[substr($d,4,2)].' ';  // mois txt
    $date = $date.substr($d,0,4);      // année num
    return $date;
  }

  public static function displayDateTime($d) {
    $date = self::displayShortDate($d)." à ";
    $date = $date.substr($d,11,5);     // heures et minutes
    return $date;
  }

  public static function getAge($dob) {
    list($year,$month,$day) = explode("-",$dob);
    $year_diff  = date("Y") - $year;
    $month_diff = date("m") - $month;
    $day_diff   = date("d") - $day;
    if (($month_diff == 0 && $day_diff < 0) || $month_diff < 0)
      $year_diff--;
    return $year_diff;
  }

  public static function getLinkDossier($urlan) {
    return link_to('Dossier sur le site de l\'Assemblée', "http://www.assemblee-nationale.fr/".sfConfig::get('app_legislature', 13)."/dossiers/".$urlan.".asp");
  }

  public static function getLinkLoi($id) {
    return link_to($id, "http://recherche2.assemblee-nationale.fr/resultats-avancee.jsp?11AUTPropositions=&11AUTRap-enq=&11AUTRap-info=&11AUTRapports=&12AUTPropositions=&12AUTRap-enq=&12AUTRap-info=&12AUTRap-infoLoi=&12AUTRapports=&13AUTComptesRendusReunions=&13AUTComptesRendusReunionsDeleg=&13AUTPropositions=&13AUTRap-info=&13AUTRap-infoLoi=&13AUTRapports=&legislature=13&legisnum=&num_init_11=&num_init_12=&num_init_13=".$id."&searchadvanced=Rechercher&searchtype=&texterecherche=&type=13ProjetsLoi");
  }

  public static function getLiasseLoiAN($id) {
    return link_to('liasse de l\'Assemblée', "http://recherche2.assemblee-nationale.fr/amendements/resultats.jsp?typeEcran=avance&chercherDateParNumero=non&NUM_INIT=".$id."&NUM_AMEND=&AUTEUR=&DESIGNATION_ARTICLE=&DESIGNATION_ALINEA=&SORT_EN_SEANCE=&DELIBERATION=&NUM_PARTIE=&DateDebut=&DateFin=&periode=&LEGISLATURE=13Amendements&QueryText=&Scope=TEXTEINTEGRAL&SortField=ORDRE_TEXTE&SortOrder=Asc&format=PDF&searchadvanced=Rechercher");
  }

  public static function getLiasseLoiImpr($id) {
    return link_to('liasse imprimable', "/liasses/liasse_".$id.".pdf");
  }

  public static function escape_blanks($txt) {
    $txt = preg_replace('/« /', '«&nbsp;', $txt);
    $txt = preg_replace('/ +([0»:;\?!\-%])/', '&nbsp;\\1', $txt);
    return $txt;
  }

  public static function clearHtml($s, $authorized_tags = '<strong><i><b><a><em>') {
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));

    if ($authorized_tags)
      $s = strip_tags($s, $authorized_tags.'<depute>');

    //Protection des liens
    $s = preg_replace('/on[^=\s]+=[^\s>]+/i', '', $s);
    $s = preg_replace('/=[\'"]?javascript:[^\s\>]+/i', '=""', $s);
    $s = preg_replace('/<a /i', '<a rel="nofollow" ', $s);
    //Conversion des urls en liens
    $s = preg_replace('/(^|\s)(http\S+)/', ' <a rel="nofollow" href="\\2">\\2</a>', $s);
    if (preg_match_all('/(({+|\<depute\>)([^}<]+)(}+|\<\/?depute\>))/i', $s, $matches)) {
      for($i = 0 ; $i < count($matches[0]) ; $i++) {
  $parlementaire = Doctrine::getTable('Parlementaire')->similarTo($matches[3][$i]);
  $matches[1][$i] = preg_replace('/\//', '\/', $matches[1][$i]);
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

  public static function echo_synthese_groupe($list, $bulles, $class, $ktop, $cpt) {
    foreach ($list as $gpe => $t) {
      $cpt++;
      echo '<tr'.($cpt %2 ? ' class="tr_odd"' : '').'>';
      echo '<td id="'.$gpe.'" class="jstitle c_'.strtolower($gpe).' '.$class['parl'].'" title="'.$t[0]['nom'];
      if (isset($t[0]['desc'])) {
        echo ' -- '.$t[0]['desc'].'"><a href="'.url_for('@list_parlementaires_groupe?acro='.$gpe).'">'.$gpe.' : '.$t[0]['nb'].' députés</a>';
      } else {
        echo '">'.$t[0]['nom']." : ".$t[0]['nb'];
      }
      echo '</td>';
      for($i = 1 ; $i < count($t) ; $i++) {
        $t[$i] = round($t[$i]/$t[0]['nb']);
        echo '<td title="'.$t[$i].' '.($t[$i] < 2 ? preg_replace('/s (.*-- )/', ' \\1', preg_replace('/s (.*-- )/', ' \\1', $bulles[$i])) : $bulles[$i]).'" class="jstitle '.$class[$ktop[$i]].'">';
        if (preg_match('/\./', $t[$i]))
          printf('%02d', $t[$i]);
        else echo $t[$i];
        echo '</td>';
      }
      echo '</tr>';
    }
    return $cpt;
  }

}
