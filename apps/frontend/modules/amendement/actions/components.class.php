<?php

class AmendementComponents extends sfComponents
{
  public function executeParlementaireAmendement()
  {
  }
  public function executePagerAmendements()
  {
    if (!$this->amendement_query)
          throw new Exception('amendement_query parameter missing');

    $pager = new sfDoctrinePager('Amendement',20);
    $pager->setQuery($this->amendement_query);
    $pager->setPage($this->request->getParameter('page', 1));
    $pager->init();

    $this->pager = $pager;
  }
}