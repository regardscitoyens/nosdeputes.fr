<?php

class AmendementComponents extends sfComponents
{
  public function executeParlementaireAmendement() {}

  public function executeParlementaireStats() {
     $this->amendements = array(
      "sorts" => array(
        "Adopté" => "adoptés",
        "Indéfini" => "en attente",
        "Irrecevable" => "irrecevables",
        "Non soutenu" => "non soutenus",
        "Rejeté" => "rejetés",
        "Retiré" => "retirés",
        "Retiré avant séance" => "retirés",
        "Tombe" => "tombés"
      ),
      "proposes" => array(
        "Total" => 0,
        "adoptés" => 0,
        "rejetés" => 0,
        "tombés" => 0,
        "retirés" => 0,
        "non soutenus" => 0,
        "irrecevables" => 0,
        "en attente" => 0
      ),
      "signes" => array(
        "Total" => 0,
        "adoptés" => 0,
        "rejetés" => 0,
        "tombés" => 0,
        "retirés" => 0,
        "non soutenus" => 0,
        "irrecevables" => 0,
        "en attente" => 0
      )
    );
    foreach (Doctrine_Query::create()
      ->select('a.sort, count(a.id)')
      ->from('Amendement a')
      ->where('a.auteur_id = ?', $this->parlementaire->id)
      ->andWhere('a.sort <> ?', "Rectifié")
      ->groupBy('a.sort')
      ->fetchArray() as $sort) {
        $this->amendements["proposes"]["Total"] += $sort["count"];
        $this->amendements["proposes"][$this->amendements["sorts"][$sort["sort"]]] += $sort["count"];
    };
    foreach (Doctrine_Query::create()
      ->select('a.sort, count(a.id)')
      ->from('Amendement a')
      ->leftJoin('a.ParlementaireAmendements pa')
      ->where('pa.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('a.sort <> ?', "Rectifié")
      ->groupBy('a.sort')
      ->fetchArray() as $sort) {
        $this->amendements["signes"]["Total"] += $sort["count"];
        $this->amendements["signes"][$this->amendements["sorts"][$sort["sort"]]] += $sort["count"];
    };
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
