<?php

class ArticleComponents extends sfComponents
{
  public function executeShow() {
    $articles = Doctrine::getTable('Article')->createQuery('a')
      ->where('categorie = ?', $this->categorie)
      ->andWhere('object_id = ?', $this->object_id)
      ->limit(1)
      ->fetchArray();
    $this->article = '';
    if ($articles)
      $this->article = $articles[0];
  }
}