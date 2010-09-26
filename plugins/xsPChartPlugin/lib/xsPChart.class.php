<?php
/**
 * pChart.class.php
 * @filesource
 */
// Require core pCHart class
require_once(sfConfig::get('sf_xspchart_lib_dir') . '/pChart/pChart.class' );
require_once(sfConfig::get('sf_xspchart_lib_dir') . '/pChart/pData.class' );
require_once(sfConfig::get('sf_xspchart_lib_dir') . '/pChart/pCache.class' );

// Load helper
sfApplicationConfiguration::getActive()->loadHelpers('xsPChart');

/**
 * xsPChart
 *
 * Additional methods
 * - xsSetFontProperties('font_name', ...) -> wrapper for setFontProperties() with automatic path to 'Fonts' dir
 * - xsRender() -> wrapper for Render()  with automatic path to 'xspchart_web_tmp_dir'
 *
 * @author Michał Organek / XSolve
 * @version 2009-02-06
 * @package symfony
 * @subpackage xsPChart
 * @link http://pchart.sourceforge.net/documentation.php
 * @example 
 */
class xsPChart extends pChart
{


/**
 *  Clear content of temporary directory or just sinle file
 *
 * @author orj
 * @version  2009-02-09
 */
  public function xsClearTmpDir($fileName = null)
  {
    if(!is_null($fileName))
    {
      @unlink(sfConfig::get('sf_web_dir') . sfConfig::get('xspchart_web_tmp_dir') . DIRECTORY_SEPARATOR . $fileName );
    }
    else
    {
      sfToolkit::clearDirectory(sfConfig::get('sf_web_dir') . sfConfig::get('xspchart_web_tmp_dir'));
    }

  } // xsClearTmpDir()

  /**
   *  xsSetFontProperties
   *~
   * @author orj
   * @version  2009-02-09
   */
  public function xsSetFontProperties()
  {
    $params = func_get_args();
    $params[0] = sfConfig::get('sf_xspchart_lib_dir') . DIRECTORY_SEPARATOR . 'Fonts' . DIRECTORY_SEPARATOR . $params[0];

    call_user_func_array(array($this, 'parent::setFontProperties'), $params);
  } // xsSetFontProperties()

  /**
   *  xsRender
   *
   * @author orj
   * @version  2009-02-09
   */
  public function xsRender($fileName)
  {
    parent::Render(sfConfig::get('sf_web_dir') . sfConfig::get('sf_xspchart_web_tmp_dir') . DIRECTORY_SEPARATOR . $fileName);
  } // xsRender()

  /**
   *  xsStroke
   *
   * @author roux
   * @version  2010-09-26
   */
  public function xsStroke()
  {
   if ( $this->ErrorReporting )
    $this->printErrors("GD");

   /* Save image map if requested */
   if ( $this->BuildMap )
    $this->SaveImageMap();
   imagepng($this->Picture);
  } // xsStroke()

}
 // pChart

/**
 * xsPData
 *
 * @author Michał Organek / XSolve
 * @version 2009-02-06
 * @package symfony
 * @subpackage xsPData
 */
class xsPData extends pData
{

} // pChart

/**
 * xsPData
 *
 * @author Michał Organek / XSolve
 * @version 2009-02-06
 * @package symfony
 * @subpackage xsPData
 */
class xsPCache extends pData
{

} // pChart


?>
