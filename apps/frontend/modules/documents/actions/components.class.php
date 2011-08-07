<?php

class DocumentsComponents extends sfComponents
{
  public function executeParlementaire() {
    $query = Doctrine::getTable('Texteloi')->createQuery('t')
      ->leftJoin('t.ParlementaireTexteloi pt')
      ->where('pt.parlementaire_id = ?', $this->parlementaire->id);
    $lois = array('Proposition de loi', 'Proposition de résolution');
    if ($this->type === "loi") {
      $query->andWhere('t.type = ? OR t.type = ?', $lois)
        ->andWhere('pt.importance = ?', 1);
      $this->type = "proposition de loi";
      $this->feminin = "e";
    }
    else if ($this->type === "rap") {
      $query->andWhere('t.type != ? AND t.type != ?', $lois);
      $this->type = "rapport";
      $this->feminin = "";
    }
    $query->orderBy('date DESC');
    if (isset($this->limit))
      $query->limit($this->limit);
    $this->docs = $query->execute();
  }

  public function executePagerDocuments() {
    if (!$this->document_query)
      throw new Exception('document_query parameter missing');

    $pager = new sfDoctrinePager('Texteloi', 20);
    $pager->setQuery($this->document_query);
    $pager->setPage($this->request->getParameter('page', 1));
    $pager->init();

    $this->pager = $pager;
  }
}
