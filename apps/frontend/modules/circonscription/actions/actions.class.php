<?php

/**
 * circonscription actions.
 *
 * @package    cpc
 * @subpackage circonscription
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class circonscriptionActions extends sfActions
{

  /* Parse a transform attribute in a node and returns the associated
   * translation.
   * Only "translate" is supported.
   * http://www.w3.org/TR/SVG/coords.html#TransformAttribute
   */
  private static function get_transform($n)
  {
    $r = array(0., 0.);

    if($t = $n->getAttribute('transform')) {
      if(preg_match_all(
            "/(\w+)\s*\(\s*(-?\d+.?\d*)\s*(?:,\s*(-?\d+.?\d*))?\s*\)/U",
            $t, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
          switch($m[1]) {
            case "translate":
              $r[0] += $m[2];
            $r[1] += $m[3];
            break;
            case "matrix":
              case "scale":
              case "rotate":
              case "skewX":
              case "skewY":
            default:
              trigger_error("Unsupported transform attribute: ".(string)$m[0],
                  E_USER_ERROR);
          }
        }
      } else {
        trigger_error("Unsupported transform attribute: ".(string)$t,
            E_USER_ERROR);
      }
    }
    return $r;
  }

  /* Compute the coordinate system of a node.
   * Only translations are supported.  viewBox attributes are ignored.
   * http://www.w3.org/TR/SVG/coords.html#EstablishingANewUserSpace
   */
  private static function compose_transform($n)
  {
    $r = array(0., 0.);
    do
    {
      $t = self::get_transform($n);
      $r[0] += $t[0];
      $r[1] += $t[1];
      $n = $n->parentNode;
    }
    while($n->parentNode);

    return $r;
  }

  /* Convert an svg path 'd' attribute into an HTML coords attribute
   * $cs is the current coordinate system
   * http://www.w3.org/TR/SVG/paths.html#PathData
   * http://www.w3.org/TR/html40/struct/objects.html#adef-shape
   */
  private static function convert_path($data, $cs, $ratio_w, $ratio_h)
  {
    /* This is an ad-hoc hack which works quite well if the path has
     * following form:
     * M x,y L x,y L x,y ... L x,y z
     * It does not handle the SVG spec, because PHP lacks proper parsing
     * tools. It does NOT handle relative coordinates either.
     * Bezier curves are converted to polygons following the control
     * points --- yes, this is VERY ugly.
     */
    if (preg_match("/[^\sMCLz\d.,-]/",$data)) {
      trigger_error("Unsupported path data attribute: ". $data,
          E_USER_ERROR);
      return NULL;
    }
    $points =  preg_split("/[\sCMLz]+/",$data, -1, PREG_SPLIT_NO_EMPTY);
    foreach($points as $k => $p) {
      $xy = preg_split("/,/", $p);
      if (count($xy) != 2) {
        trigger_error("Unsupported path data attribute: ". $data,
            E_USER_ERROR);
        return NULL;
      }
      $points[$k] = implode(",", array(($xy[0] + $cs[0]) * $ratio_w,
            ($xy[1] + $cs[1]) * $ratio_h));
    }
    return (implode(",",$points));
  }

  /* Get the min and max x and y coordinates of an svg path 'd' attribute
   * $cs is the current coordinate system
   * http://www.w3.org/TR/SVG/paths.html#PathData
   * http://www.w3.org/TR/html40/struct/objects.html#adef-shape
   */
  private static function path_minmax($data, $cs)
  {
    /* Same limitations as convert_path */
    if (preg_match("/[^\sMCLz\d.,-]/",$data)) {
      trigger_error("Unsupported path data attribute: ". $data,
          E_USER_ERROR);
      return NULL;
    }
    $points =  preg_split("/[\sCMLz]+/",$data, -1, PREG_SPLIT_NO_EMPTY);
    foreach($points as $k => $p) {
      $xy = preg_split("/,/", $p);
      if (count($xy) != 2) {
        trigger_error("Unsupported path data attribute: ". $data,
            E_USER_ERROR);
        return NULL;
      }
      $x[$k] = $xy[0] + $cs[0];
      $y[$k] = $xy[1] + $cs[1];
    }
    return (array(
          "minx" => min($x),
          "miny" => min($y),
          "maxx" => max($x),
          "maxy" => max($y)));
  }

  /* Get the title of a given path node. */
  private static function get_title($n)
  {
    $title = (string)
      $n->getElementsByTagName('title')->item(0)->textContent;
    $title .= " &mdash; ". (string)
      $n->getElementsByTagName('desc')->item(0)->textContent;
    return $title;
  }

  /* Compute the areas of an image map.
   * $dom is the svg DOM, $w and $h the width and height of the resulting
   * image - 0 if you want to extract this from the svg - and $regexp is a
   * selects the nodes to include in the image map (based on their id).
   * If only one of $w and $h is given, preserve the svg ratio.
   */
  private static function compute_areas($dom, $w, $h, $regexp, $deptitle = 0)
  {
    $areas = "";

    $svg = $dom->getElementsByTagName('svg')->item(0);

    $svg_w = (string) $svg->getAttribute('width');
    $svg_h = (string) $svg->getAttribute('height');

    if ($w == 0 && $h == 0)
    {
      $w = (int) $svg_w;
      $h = (int) $svg_h;
    }
    elseif ($w == 0)
      $w = (int) ($svg_w * $h / $svg_h);
    elseif ($h == 0)
      $h = (int) ($svg_h * $w / $svg_w);

    $ratio_w = $w / $svg_w;
    $ratio_h = $h / $svg_h;

    $paths = $dom->getElementsByTagName('path');
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));

    foreach ($paths as $path)
      if (preg_match($regexp, $path->getAttribute('id'))) {
        $cs = self::compose_transform($path);
        $points = self::convert_path($path->getAttribute('d'), $cs, $ratio_w, $ratio_h);
        if ($deptitle) {
          $title = $path->getAttribute('title');
          $id = preg_replace('/d/', '', $path->getAttribute('id'));
          $href = url_for("@list_parlementaires_circo_search?search=$id");
        } else {
          $id = $path->getAttribute('id');
          $title = self::get_title($path);
          $href = url_for("@redirect_parlementaires_circo?code=".$path->getAttribute('id'));
        }
        $areas .= "<area id=\"map$id\" href=\"".$href."\" class=\"jstitle\" title=\"".str_replace('&mdash;', '--', $title)."\" alt=\"".$title."\" ".
          "shape=\"poly\" coords=\"".$points."\" />\n";
      }
    return array('areas' => $areas, 'w' => $w, 'h' => $h);
  }

  /* Crop an svg dom to keep only the $tags which fullfill the
   * $regexp condition. Any other path is removed, and the
   * image is cropped so as to focus on the remaining paths, with some
   * $margin.
   */
  private static function crop_svg($dom, $regexp, $margin, $tags = array('path', 'text'))
  {
    $svg = $dom->getElementsByTagName('svg')->item(0);

    $toRemove = array();
    $minx = array();
    $maxx = array();
    $miny = array();
    $maxy = array();

    foreach($tags as $tag) {
      $paths = $dom->getElementsByTagName($tag);
      foreach ($paths as $path) {
        if (preg_match($regexp, $path->getAttribute('id'))) {
          $cs = self::compose_transform($path);
          $t = self::path_minmax($path->getAttribute('d'), $cs);
          $minx[] = $t["minx"];
          $maxx[] = $t["maxx"];
          $miny[] = $t["miny"];
          $maxy[] = $t["maxy"];
        }
        else {
          /* WARNING You can't remove DOMNodes from a DOMNodeList as you're
           * iterating over them in a foreach loop. */
          $toRemove[] = $path;
        }
      }
    }

    foreach($toRemove as $node) {
      $node->parentNode->removeChild($node);
    }
 
    if (!count($minx)) return;
    $x_min = min($minx) - $margin;
    $x_max = max($maxx) + $margin;
    $y_min = min($miny) - $margin;
    $y_max = max($maxy) + $margin;

    $svg->setAttribute('width', $x_max - $x_min);
    $svg->setAttribute('height', $y_max - $y_min);
    $svg->setAttribute('transform', "translate(".-$x_min.",".-$y_min.")");
  }

  private static function generateSvgDep($w, $h) {
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = FALSE;
    // FIXME Use loadXML to load from a string instead (database)
    $dom->load("france_deptmts.svg");
    return $dom;
  }

  public static function echoDeptmtsMap($w, $h, $link) {
    $dom = self::generateSvgDep($w, $h);
    $r = self::compute_areas($dom, $w, $h, '/^d\d+/', 1);
    $w = $r['w'];
    $h = $r['h'];

    $src = url_for("@deptmts_image_png?w=$w&h=$h");
    
    if ($link) echo '<a class="jstitle" title="Tous les départements français" href="'.url_for('@list_parlementaires_circo').'">';
    echo "<img alt=\"Carte issue de Wikipedia : Départements et régions de France par Bayo (sous licence GFDL)\" class=\"carte_departement\" src=\"$src\" usemap=\"#deptmts\" ";
    echo 'style="width:'.$w.'px; height:'.$h.'px;" />';
    if ($link) echo '</a>';
    echo "<map name=\"deptmts\" id=\"deptmts\">";
    echo $r['areas'];
    echo "</map>";
  }

  public static function echoDeptmtsImage($w, $h) {
    $dom = self::generateSvgDep($w, $h);

    $im = new Imagick();
    $im->readImageBlob($dom->saveXML());
    $res = $im->getImageResolution();
    $x_ratio = $res['x'] / $im->getImageWidth();
    $y_ratio = $res['y'] / $im->getImageHeight();
    $im->removeImage();
    $im->setResolution($w * $x_ratio, $h * $y_ratio);
    $im->readImageBlob($dom->saveXML());

    $im->setImageFormat("png");
    echo $im;
  }

  private static function generateSvgDom($circo, $w, $h)
  {
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = FALSE;
    // FIXME Use loadXML to load from a string instead (database)
    if (sfConfig::get('app_legislature') <= 13)
      $dom->load("circo.svg");
    else $dom->load("circo2012.svg");

    if(preg_match("/^\d\d[\dab]$/",$circo))
      self::crop_svg($dom, "/^$circo-\d\d$/", 10);

    return $dom;

  }

  private static function prepareMap($circo, $w, $h)
  {
    $dom = self::generateSvgDom($circo, $w, $h);

    if($circo == "full")
      $regexp = "/^\d\d[\dab]-(0[1-9]|[1-9]\d)$/";
    else
      $regexp = "/^$circo-(0[1-9]|[1-9]\d)$/";

    return array($dom, self::compute_areas($dom, $w, $h, $regexp));
  }

  /* $circo is a three digits string, or "full" for the full map */
  public static function echoCircoMap($circo, $w, $h)
  {
    $arr = self::prepareMap($circo, $w, $h);
    $r = $arr[1];
    $w = $r['w'];
    $h = $r['h'];

    $src = url_for("@circo_image_png?circo=$circo&w=$w&h=$h");

    $alt = "";
    if (sfConfig::get('app_legislature') > 13)
      $alt = ' alt="Carte des circonscriptions législatives réalisées par Jérôme Cukier - CC-BY-SA"';
    echo "<img".$alt." class=\"carte_departement\" src=\"$src\" usemap=\"#$circo\" ";
    echo 'style="width:'.$w.'px; height:'.$h.'px;" />';
    echo "<map name=\"$circo\">";
    echo $r['areas'];
    echo "</map>";
  }

  /* $circo is a three digits string, or "full" for the full map */
  public static function echoCircoImage($circo, $w, $h)
  {

    /* If you want to resize a vector-graphics image (such as SVG) to a
     * certain dimension in pixels, without losing quality, you have to do
     * this
     * http://www.php.net/manual/en/function.imagick-setresolution.php
     */
    $arr = self::prepareMap($circo, $w, $h);
    $dom = $arr[0];
    $r = $arr[1];
    $w = $r['w'];
    $h = $r['h'];

    $im = new Imagick();
    $im->readImageBlob($dom->saveXML());
    $res = $im->getImageResolution();
    $x_ratio = $res['x'] / $im->getImageWidth();
    $y_ratio = $res['y'] / $im->getImageHeight();
    $im->removeImage();
    $im->setResolution($w * $x_ratio, $h * $y_ratio);
    $im->readImageBlob($dom->saveXML());

    $im->setImageFormat("png");
    echo $im;
  }

  public function executeGetDeptmtsimagepng(sfWebRequest $request) {
    $this->w = $request->getParameter('w');
    $this->h = $request->getParameter('h');
    $this->getResponse()->setHttpHeader('content-type', 'image/png');
    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('Expires', 'Mon, 06 Jan 2042 00:00:00 GMT');
  }

  public function executeGetCircoimagepng(sfWebRequest $request)
  {
    $this->circo = $request->getParameter('circo');
    $this->w = $request->getParameter('w');
    $this->h = $request->getParameter('h');
    $this->getResponse()->setHttpHeader('content-type', 'image/png');
    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('Expires', 'Mon, 06 Jan 2042 00:00:00 GMT');
  }

  public function executeList(sfWebRequest $request) 
  {
    $this->circos = Parlementaire::$dptmt_nom;
  }

  public function executeShow(sfWebRequest $request) 
  {
    $this->circo = preg_replace('/_/', ' ', $request->getParameter('departement'));
    $this->forward404Unless($this->circo);
    $this->departement_num = Parlementaire::getNumeroDepartement($this->circo);

    $this->parlementaires = Doctrine::getTable('Parlementaire')->createQuery('p')
      ->where('p.nom_circo = ?', $this->circo)
      ->addOrderBy('p.num_circo')
      ->execute();
    $this->total = count($this->parlementaires);
    $this->forward404Unless($this->total);
    if ($this->total == 1) 
        return $this->redirect('@parlementaire?slug='.$this->parlementaires[0]['slug']); 
  }
  public function executeSearch(sfWebRequest $request) 
  {
    $this->search = $request->getParameter('search');
    $departmt = strip_tags(trim(strtolower($this->search)));
    if (preg_match('/(polyn[eé]sie)/i', $departmt)) {
      return $this->redirect('@list_parlementaires_departement?departement=Polyn%C3%A9sie_Fran%C3%A7aise');
    } else {
      $departmt = preg_replace('/\s+/', '-', $departmt);
      if ($this->circo = Parlementaire::getNomDepartement(Parlementaire::getNumeroDepartement($departmt)))
        return $this->redirect('@list_parlementaires_departement?departement='.$this->circo);
      if (preg_match('/^(\d+\w?)$/', $departmt, $match)) {
	$num = preg_replace('/^0+/', '', $match[1]);
        $this->circo = Parlementaire::getNomDepartement($num); 
        if ($this->circo)
	  return $this->redirect('@list_parlementaires_departement?departement='.$this->circo);
      }
      $this->circo = $departmt;
      $ctquery = Doctrine_Query::create()
        ->from('Parlementaire p')
        ->select('count(*) as ct, p.nom_circo')
        ->where('nom_circo LIKE ?', '%'.$this->circo.'%')
        ->groupBy('nom_circo')
        ->fetchOne();
      if ($ctquery['ct'] == 1)
        return $this->redirect('@list_parlementaires_departement?departement='.$ctquery['nom_circo']);
      $this->query_parlementaires = Doctrine::getTable('Parlementaire')
        ->createQuery('p')
        ->where('nom_circo LIKE ?', '%'.$this->circo.'%')
        ->addOrderBy('nom_circo, num_circo');
    }
  }
  public function executeRedirect(sfWebRequest $request) 
  {
    $departement = $request->getParameter('departement');
    $num = $request->getParameter('numero');
    $code = $request->getParameter('code');
    if (preg_match('/0*([^0]\d*[ab]?)\-0*([^0]\d*)/', $code, $match)) {
      $departement = $match[1];
      $num = $match[2];
    }
    $parlementaire = Doctrine::getTable('Parlementaire')->createQuery('p')
      ->where('num_circo = ?', $num)
      ->andWhere('nom_circo = ?', parlementaire::getNomDepartement($departement))
      ->andWhere('fin_mandat IS NULL')
      ->fetchOne();
    if (!$parlementaire) {
      return $this->redirect('circonscription/list?departement='.$departement);
    }
    return $this->redirect('parlementaire/show?slug='.$parlementaire->slug);
  }
}
