<?php

class InterventionComponents extends sfComponents
{
  public function executeParlementaireIntervention() {
  }

  public function executeParlementaireQuestion() {
    $query = Doctrine::getTable('Intervention')->createQuery('i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.type = ?', 'question')
      ->andWhere('i.fonction NOT LIKE ?', 'président%')
      ->andWhere('i.nb_mots > ?', 40)
      ->groupBy('i.seance_id')
      ->orderBy('i.date DESC, i.timestamp ASC');
    if (isset($this->limit))
      $query->limit($this->limit);
    $this->questions = $query->execute();
  }

  public function executePagerInterventions()  {
    if (!$this->intervention_query)
          throw new Exception('intervention_query parameter missing');

    $pager = new sfDoctrinePager('Intervention',20);
    $pager->setQuery($this->intervention_query);
    $pager->setPage($this->request->getParameter('page', 1));
    $pager->init();

    $this->pager = $pager;
  }
}
