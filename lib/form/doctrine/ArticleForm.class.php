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
	  $this['link'],
	  $this['citoyen_id'],
	  $this['nb_commentaires'],
	  $this['rate']
	  ) ;
    $this->widgetSchema['categorie'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['corps'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['user_corps']->setOption('label', 'Corps');
    $this->widgetSchema['user_corps']->setAttribute('style', 'width:65em');
    if (!isset($_GET['moderateur']) || !$_GET['moderateur']) {
      unset($this['status']);
    }
  }

  public function setTitre($b)
  {
    if (!$b) {
      $this->widgetSchema['titre'] = new sfWidgetFormInputHidden();
      return ;
    }
    $this->validatorSchema['titre'] = new sfValidatorRegex(array('pattern' => '/^[^\<]+$/', 'required'=>true), array('invalid' => 'tags html non authorisés', 'required' => 'titre obligatoire'));
  }

  public function setObject($b, $categorie, $display = true) {
    if (!$b) {
      unset($this['object_id']);
      return;
    }

    if ($display) {
      $this->widgetSchema['object_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getValue('categorie')));
      $this->widgetSchema['object_id']->setOption('label', $categorie);
      $query = Doctrine::getTable($categorie)->createQuery('c')->where('c.nom IS NOT NULL')->orderBy('nom');
      $this->widgetSchema['object_id']->setOption('query', $query);
    }else{
      $this->widgetSchema['object_id'] = new sfWidgetFormInputHidden();
    }
    
  }

  public function setUnique($b) {
    if (!$b)
      return;
    $this->validatorSchema->setPostValidator(new sfValidatorDoctrineUnique(array('model' => 'Article', 'column' => array('object_id' ,'categorie')), array('invalid' => 'Il existe déjà un article pour cet objet')));
  }

  public function setParent($b, $categorie) {
    if (!$b) {
      unset($this['article_id']);
      return ;
    }

    $query = Doctrine::getTable('Article')->createQuery('a')->where('a.article_id IS NULL')->andWhere('a.categorie = ?', $categorie);
    if ($this->object && $this->object->id) {
      $query->andWhere('a.id != ?', $this->object->id);
    }
    $this->widgetSchema['article_id']->setOption('query', $query);
    $this->widgetSchema['article_id']->setOption('label', 'Article père');
  }
}
