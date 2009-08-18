<?php
/**
 * xsPChartHelper.php
 * @filesource
 */


/**
 *  xspchart_image_tag
 *
 * @author Jacek Olearczyk / XSolve
 * @version  2009-02-09
 */
function xspchart_image_tag()
{
  $params = func_get_args();
  $params[0] = sfConfig::get('sf_xspchart_web_tmp_dir') . DIRECTORY_SEPARATOR . $params[0];

  return call_user_func_array('image_tag', $params);
} // xspchart_image_tag()
?>
