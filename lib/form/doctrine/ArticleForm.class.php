<?php

/**
 * Article form.
 *
 * @package    form
 * @subpackage Article
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class ArticleForm extends BaseArticleForm
{
  public function configure()
  {
    unset($this['created_at'],
	  $this['updated_at'],
	  $this['version'],
	  $this['slug'],
	  $this['status']
	  ) ;
    $this->widgetSchema['categorie'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['citoyen_id'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['corps'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['user_corps']->setOption('label', 'Corps');
  }

  public function setTitre($b)
  {
    if (!$b) {
      unset($this['titre']);
      return ;
    }
    $this->validatorSchema['titre'] = new sfValidatorRegex(array('pattern' => '/^[^<]+$/', 'required'=>true), array('invalid' => 'tags html non authorisés', 'required' => 'titre obligatoire'));
  }

  public function setObject($b) {
    if (!$b) {
      unset($this['object_id']);
      return;
    }
    $categorie = $this->getValue('categorie');
    if (!$categorie) {
      $categorie = $this->object->categorie;
    }
    $this->widgetSchema['object_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getValue('categorie')));
    $this->widgetSchema['object_id']->setOption('label', $categorie);
    $query = doctrine::getTable($this->getValue('categorie'))->createQuery('c')->where('c.nom IS NOT NULL')->orderBy('nom');
    $this->widgetSchema['object_id']->setOption('query', $query);
    
  }

  public function setParent($b) {
    if (!$b) {
      unset($this['article_id']);
      return ;
    }
    $categorie = $this->getValue('categorie');
    if (!$categorie) {
      $categorie = $this->object->categorie;
    }
    $query = doctrine::getTable('Article')->createQuery('a')->where('a.article_id IS NULL')->andWhere('a.categorie = ?', $categorie);
    if ($this->object) {
      $query->andWhere('a.id != ?', $this->object->id);
    }
    $this->widgetSchema['article_id']->setOption('query', $query);
    $this->widgetSchema['article_id']->setOption('label', 'Article père');
  }
}