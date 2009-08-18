<?php
/**
 * xsPChartPluginConfiguration.class
 * @filesource
 */

/**
 * xsPChartPluginConfiguration
 *
 * @author Jacek Olearczyk / XSolve
 * @version 2009-02-06
 * @package symfony
 * @subpackage xsPChart
 */
class xsPChartPluginConfiguration extends sfPluginConfiguration
{
  /**
   * Initialize
   */
  public function initialize()
  {
    // Plugin dir
    sfConfig::set('sf_xspchart_root_dir', dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');

    // Core pChart library dir
    sfConfig::set('sf_xspchart_lib_dir', sfConfig::get('sf_xspchart_root_dir') . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'pChart' );

    // Dir for temporary files/pchar - system path
    sfConfig::set('sf_xspchart_sys_web_tmp_dir', sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR .  sfConfig::get('sf_xspchart_web_tmp_dir'));

  }
}