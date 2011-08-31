<?php

class QuestionsComponents extends sfComponents
{
  public function executeParlementaire() {
    if (!$this->type)
      throw new Exception('type parameter missing');
    $query = Doctrine::getTable('Question')->createQuery('q')
      ->where('q.parlementaire_id = ?', $this->parlementaire->id);
    if ($this->type === "écrites")
      $query->andWhere('q.type = ?', "Question écrite");
    else $query->andWhere('q.type != ?', "Question écrite");
    $query->orderBy('q.date DESC');
    if (isset($this->limit))
      $query->limit($this->limit);
    $this->questions = $query->execute();
  }

  public function executeSearch() {
  }

  public function executePagerQuestions() {
    if (!$this->question_query)
          throw new Exception('question_query parameter missing');

    $pager = new sfDoctrinePager('Questions',20);
    $pager->setQuery($this->question_query);
    $pager->setPage($this->request->getParameter('page', 1));
    $pager->init();

    $this->pager = $pager;
  }
}
