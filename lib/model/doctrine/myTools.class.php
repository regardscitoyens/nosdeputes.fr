<?php
class myTools {

  public static function betterUCFirst($str) {
    $str = ucfirst($str);
    $str = preg_replace('/^é/', 'É', $str);
    $str = preg_replace('/^ê/', 'Ê', $str);
    $str = preg_replace('/^â/', 'Â', $str);
    $str = preg_replace('/^à/', 'À', $str);
    $str = preg_replace('/^œ/', 'Œ', $str);
    return $str;
  }

  public static function convertYamlToArray($string) {
    $string = preg_replace('/^\s*\[\s*"\s*/', '', $string);
    $string = preg_replace('/\s*"\s*]\s*$/', '', $string);
    $string = preg_replace('/",\s+"/', '","', $string);
    return explode('","', $string);
  }

  public static function getDebutData() {
    $date = sfConfig::get('app_debut_data');
    if (!$date)
      $date = "2004-10-01";
    return $date;
  }

  public static function getDebutMandature() {
    $date = sfConfig::get('app_debut_mandature');
    if (!$date)
      $date = "2011-10-01";
    return $date;
  }

  public static function isDebutMandature() {
    $gap = time() - strtotime(self::getDebutMandature());
    if ($gap > 864000 && $gap < 25920000)
      return true;
    return false;
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
      if($match[2] != '00') {
        return self::$num_mois[$match[2]].' '.$match[1];
      } else return $date;
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
    if ($urlan)
      return link_to('Dossier sur le site du Sénat', "http://www.senat.fr/dossier-legislatif/".$urlan.".html");
    return;
  }

  public static function getLinkLoi($id) {
    return $id;
// link_to($id, "http://recherche2.assemblee-nationale.fr/resultats-avancee.jsp?11AUTPropositions=&11AUTRap-enq=&11AUTRap-info=&11AUTRapports=&12AUTPropositions=&12AUTRap-enq=&12AUTRap-info=&12AUTRap-infoLoi=&12AUTRapports=&13AUTComptesRendusReunions=&13AUTComptesRendusReunionsDeleg=&13AUTPropositions=&13AUTRap-info=&13AUTRap-infoLoi=&13AUTRapports=&legislature=13&legisnum=&num_init_11=&num_init_12=&num_init_13=".$id."&searchadvanced=Rechercher&searchtype=&texterecherche=&type=13ProjetsLoi");
  }

  public static function getLiasseLoiSenat($id, $comm = 0) {
    $link = "http://www.senat.fr/amendements/";
    if (Doctrine::getTable('Amendement')->createQuery('a')->where('texteloi_id = ?', $id)->andWhere('numero LIKE ?', 'COM-%')->fetchOne() != null)
      $link .= "commissions/";
    $id = preg_replace('/^(\d{4})(\d{4})-0*(\d+)$/', '\\1-\\2/\\3', $id);
    $link.= $id."/jeu_classe.html";
    return link_to('liasse du Sénat', $link);
  }

  public static function getLiasseLoiImpr($id) {
    return link_to('liasse imprimable', "/liasses/liasse_".$id.".pdf");
  }

  public static function escape_blanks($txt) {
    $txt = preg_replace('/([«\-])\s+/', '\\1&nbsp;', $txt);
    $txt = preg_replace('/\s+([0»:;\?!\-%])/', '&nbsp;\\1', $txt);
    return $txt;
  }

  public static function clearHtml($s, $authorized_tags = '<strong><i><b><a><em>') {
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));

    if ($authorized_tags)
      $s = strip_tags($s, $authorized_tags.'<senateur>');

    //Protection des liens
    $s = preg_replace('/on[^=\s]+=[^\s>]+/i', '', $s);
    $s = preg_replace('/=[\'"]?javascript:[^\s\>]+/i', '=""', $s);
    $s = preg_replace('/<a /i', '<a rel="nofollow" ', $s);
    //Conversion des urls en liens
    $s = preg_replace('/(^|\s)(http\S+)/', ' <a rel="nofollow" href="\\2">\\2</a>', $s);
    if (preg_match_all('/(({+|\<senateur\>)([^}<]+)(}+|\<\/?senateur\>))/i', $s, $matches)) {
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

  public static function array2hash($array, $hashname) {
    if (!$array)
      return array();
    $hash = array();
    if (!isset($array[0])) {
      if (isset($array->fonction))
        return array("organisme" => $array->getNom(), "fonction" => $array->fonction);
      else return $array;
    }
    foreach($array as $e) if ($e) {
      if (isset($e->fonction))
        $hash[] = array($hashname => array("organisme" => $e->getNom(), "fonction" => $e->fonction));
      else $hash[] = array($hashname => preg_replace('/\n/', ', ', $e));
    }
    return $hash;
  }

  public static function depile_assoc_xml($asso, $breakline, $alreadyline) {
    foreach (array_keys($asso) as $k) {
      if (!$alreadyline && $k == $breakline) {
        $alreadyline = 1;
      }
      echo "<$k>";
      echo self::depile_xml($asso[$k], $breakline, $alreadyline);
      echo "</$k>";
      if ($k == $breakline) {
        echo "\n";
      }
    }
  }

  public static function depile_xml($res, $breakline, $alreadyline = 0) {
    if (is_array($res)) {
      if (!isset($res[0])) {
        self::depile_assoc_xml($res, $breakline, $alreadyline);
      }else{
        foreach($res as $r) {
  	  self::depile_xml($r, $breakline, $alreadyline);
        }
      }
    }else{
      $res = str_replace('<', '&lt;', $res);
      $res = str_replace('>', '&gt;', $res);
      $res = str_replace('&', '&amp;', $res);
      echo $res;
    }
  }

  public static function depile_assoc_csv($asso, $breakline, $multi, $alreadyline) {
    $semi = 0;
    foreach (array_keys($asso) as $k) {
      if (isset($multi[$k]) && $multi[$k]) {
        $semi = 1;
      }
      self::depile_csv($asso[$k], $breakline, $multi, $semi, $alreadyline);
      if ($k == $breakline) {
        echo "\n";
      }
    }
    return $semi;
  }

  public static function depile_csv($res, $breakline, $multi, $comma = 0, $alreadyline = 0) {
    if (is_array($res)) {
      if (isset($res['organisme']) && isset($res['fonction']))
        return self::depile_csv($res['organisme']." - ".$res['fonction'], $breakline, $multi, $comma, $alreadyline);
      if (!isset($res[0])) {
        if (array_keys($res))
  	return self::depile_assoc_csv($res, $breakline, $multi, $alreadyline);
        echo ";";
        return;
      }
      foreach($res as $r)
        $semi = self::depile_csv($r, $breakline, $multi, 0, $alreadyline);
      if ($semi)
        echo ';';
    }else{
      if ($comma)
        $res = preg_replace('/[,;]/', '', $res);
      $string = preg_match('/[,;"]/', $res);
      if ($string) {
        $res = preg_replace('/"/', '\"', $res);
        echo '"';
      }
      echo $res;
      if ($string)
        echo '"';
      if ($comma)
        echo '|';
      else echo ';';
    }
  }

  public static function templatize($action, $request, $filename) {
    self::headerize($action, $request, $filename);
    $action->setTemplate($request->getParameter('format'), 'api');
  }

  public static function headerize($action, $request, $filename) {
    $action->setLayout(false);
    switch($request->getParameter('format')) {
      case 'json':
        if (!$request->getParameter('textplain')) {
          $action->getResponse()->setContentType('text/plain; charset=utf-8');
          $action->getResponse()->setHttpHeader('content-disposition', 'attachment; filename="'.$filename.'.json"');
        }
        break;
      case 'xml':
        if (!$request->getParameter('textplain')) {
          $action->getResponse()->setContentType('text/xml; charset=utf-8');
          //    $action->getResponse()->setHttpHeader('content-disposition', 'attachment; filename="'.$filename.'.xml"');
        }
        break;
      case 'csv':
        if (!$request->getParameter('textplain')) {
          $action->getResponse()->setContentType('application/csv; charset=utf-8');
          $action->getResponse()->setHttpHeader('content-disposition', 'attachment; filename="'.$filename.'.csv"');
        }
        break;
    default:
      $action->forward404();
    }
    if ($request->getParameter('textplain')) {
      $action->getResponse()->setContentType('text/plain; charset=utf-8');
    }
  }
}
