<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGuardBasicSecurityFilter.class.php 7634 2008-02-27 18:01:40Z fabien $
 */
class sfGuardBasicSecurityFilter extends sfBasicSecurityFilter
{
  public function execute ($filterChain)
  {
    if ($this->isFirstCall() and !$this->getContext()->getUser()->isAuthenticated())
    {
      if ($cookie = $this->getContext()->getRequest()->getCookie(sfConfig::get('app_sf_guard_plugin_remember_cookie_name', 'sfRemember')))
      {
        $q = Doctrine_Query::create()
              ->from('sfGuardRememberKey r')
              ->innerJoin('r.sfGuardUser u')
              ->where('r.remember_key = ?', $cookie);

        if ($q->count())
        {
          $this->getContext()->getUser()->signIn($q->fetchOne()->sfGuardUser);
        }
      }
    }

    parent::execute($filterChain);
  }
}