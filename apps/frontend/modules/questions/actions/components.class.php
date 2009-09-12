<?php

class QuestionsComponents extends sfComponents
{
  public function executeParlementaire()
  {
  }
  public function executeSearch(){}
  public function executePagerQuestions()
  {
    if (!$this->question_query)
          throw new Exception('question_query parameter missing');

    $pager = new sfDoctrinePager('Questions',20);
    $pager->setQuery($this->question_query);
    $pager->setPage($this->request->getParameter('page', 1));
    $pager->init();

    $this->pager = $pager;
  }
}
