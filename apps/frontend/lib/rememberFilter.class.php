<?php
class rememberFilter extends sfFilter
{
  public function execute ($filterChain)
  {
    // execute le filtre une seule fois
    if ($this->isFirstCall())
    {
      if ($this->getContext()->getRequest()->getCookie('remember'))
      {
        $context = $this->getContext();
        $remember_cookie = $context->getRequest()->getCookie('remember');
        $secret_key = sfConfig::get('app_secret_key');
        $part = explode("_", $remember_cookie);
        if (sha1($secret_key.$part[0]) == $part[1])
        {
          $user = Doctrine::getTable('Citoyen')->findOneBySlug($part[0]);
          // signin
          $context->getUser()->setAttribute('user_id', $user->getId());
          $context->getUser()->setAttribute('is_active', $user->getIsActive());
          $context->getUser()->setAttribute('login', $user->getLogin());
          $context->getUser()->setAttribute('slug', $user->getSlug());
          $context->getUser()->setAuthenticated(true);
          $context->getUser()->clearCredentials();
          $context->getUser()->addCredentials($user->getRole());
          
          // save last login
          $user->setLastLogin(date('Y-m-d H:i:s'));
          $user->save();
        }
      }
    }
    // execute le prochain
    $filterChain->execute();
  }
}
?>