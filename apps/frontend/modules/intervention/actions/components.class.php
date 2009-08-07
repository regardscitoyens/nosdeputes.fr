<?php

class InterventionComponents extends sfComponents
{
  public function executeParlementaireIntervention()
  {
  }
  public function executePagerInterventions()
  {
    if (!$this->intervention_query)
          throw new Exception('intervention_query parameter missing');

    $pager = new sfDoctrinePager('Intervention',20);
    $pager->setQuery($this->intervention_query);
    $pager->setPage($this->request->getParameter('page', 1));
    $pager->init();

    $this->pager = $pager;
  }
}