<?php

class searchActions extends sfActions
{
  public function executeIndex(sfWebRequest $request) {
    $type = $request->getParameter('type');
    $search = strip_tags($request->getParameter('search'));
    if ($type == 'depute')
      return $this->redirect('@list_parlementaires_search?search='.$search);
    elseif ($type == 'departement')
      return $this->redirect('@list_parlementaires_circo_search?search='.$search);
    elseif ($type == 'profession')
      return $this->redirect('@list_parlementaires_profession?search='.$search);
    elseif ($type == 'intervention')
      return $this->redirect('@search_interventions_mots?search='.$search);
    elseif ($type == 'amendement')
      return $this->redirect('@search_amendements_mots?search='.$search);
    elseif ($type == 'question')
      return $this->redirect('@search_questions_ecrites_mots?search='.$search);
    else $this->forward404();
  }

}
