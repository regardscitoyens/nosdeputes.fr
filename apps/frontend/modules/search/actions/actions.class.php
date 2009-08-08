<?php

class searchActions extends sfActions
{
  public function executeIndex(sfWebRequest $request) {
    $type = $request->getParameter('type');
    $search = $request->getParameter('search');
    if ($type == 'depute')
      return $this->redirect('parlementaire/list?search='.$search);
    elseif ($type == 'intervention')
      return $this->redirect('intervention/search?search='.$search);
    elseif ($type == 'amendement')
      return $this->redirect('amendement/search?search='.$search);
  }

}
