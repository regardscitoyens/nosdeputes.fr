<?php

class searchActions extends sfActions
{
  public function executeIndex(sfWebRequest $request) {
    $type = $request->getParameter('type');
    $search = $request->getParameter('search');
    if ($type == 'depute')
      return $this->redirect('parlementaire/list?search='.$search);
    elseif ($type == 'departement')
      return $this->redirect('parlementaire/listCirco?search='.$search);
    elseif ($type == 'profession')
      return $this->redirect('parlementaire/listProfession?search='.$search);
    elseif ($type == 'intervention')
      return $this->redirect('intervention/search?search='.$search);
    elseif ($type == 'amendement')
      return $this->redirect('amendement/search?search='.$search);
    elseif ($type == 'question')
      return $this->redirect('questions/search?search='.$search);
    else $this->forward404();
  }

}
