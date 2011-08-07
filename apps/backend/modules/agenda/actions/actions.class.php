<?php

class agendaActions extends sfActions
{

  public function executeSemaine(sfWebRequest $request)
  {
    $this->annee = $request->getParameter('annee');
    if (!$this->annee) $this->annee = date('Y', time());
    $this->semaine = $request->getParameter('semaine');
    if (!$this->semaine) $this->semaine = date('W', time());

    $query = Doctrine::getTable('Seance')->createQuery('s')
      ->where('s.annee = ?', $this->annee)
      ->andWhere('s.numero_semaine = ?', $this->semaine)
      ->leftJoin('s.Organisme o')
      ->orderBy('s.date, s.type, o.nom, s.moment');
    $this->seances = $query->execute();
  }
}
