<?php

class correctCommentairesTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'correct';
    $this->name = 'Commentaires';
    $this->briefDescription = 'Correct Titres et Objects des commentaires';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $about = array('Intervention' => "Suite aux propos d", 'Amendement' => "Au sujet d'un amendement déposé", 'QuestionEcrite' => "A propos d'une question écrite d");
    $comments = Doctrine::getTable('Commentaire')->findAll();
    $loi = '';
    foreach($comments as $comment) {
      unset($loi);
      $object = Doctrine::getTable($comment->object_type)->find($comment->object_id);
      if (isset($object->texteloi_id)) {
        $loi = Doctrine::getTable('TitreLoi')->findLightLoi($object->texteloi_id);
        if ($comment->object_type != 'Amendement') {
          $present = preg_replace('/<br\/>.*$/', '', $loi['titre']).' - A propos de l\'article ';
          if ($comment->object_type == 'Alinea') {
            $article = Doctrine::getTable('ArticleLoi')->createQuery('a')
              ->select('titre')
              ->where('texteloi_id = ?', $object->texteloi_id)
              ->andWhere('id = ?', $object->article_loi_id)
              ->fetchOne();
            $present .= $article['titre'].' alinéa '.$object->numero;
          } else $present .= $object->titre;
        } else {
          if ($loi)
            $present = preg_replace('/<br\/>.*$/', '', $loi['titre']).' - A propos de l\'amendement n°'.$object->numero;
          else $present = $about[$comment->object_type].' le '.date('d/m/Y', strtotime($object->date));
        }
      } else {
        $present = '';
        if ($comment->object_type != 'QuestionEcrite') {
          if ($section = $object->getSection())
            $present = $section->getSection(1)->getTitre();
          if ($present == '' && $comment->object_type == 'Intervention' && $object->type == 'commission')
            $present = $object->getSeance()->getOrganisme()->getNom();
        }
        if ($present != '') $present .= ' - ';
        else $present = '';
        $present .= $about[$comment->object_type];
        $nom = '';
        if ($comment->object_type == 'QuestionEcrite')
          $nom = $object->getParlementaire()->nom;
        else if ($comment->object_type == 'Intervention') 
          $nom = $object->getIntervenant()->nom;
        if ($nom != '') {
          if (preg_match('/^[AEIOUYÉÈÊ]/', $nom)) $nom = '\''.$nom;
          else $nom = 'e '.$nom;
          $present .= $nom;
        }
        $present .= ' le '.date('d/m/Y', strtotime($object->date));
      }
      $comment->presentation = $present;
      
      $comment->updateNbCommentaires();
      
      if (isset($object->parlementaire_id)) {
        if ($object->parlementaire_id)
          $comment->addObject('Parlementaire', $object->parlementaire_id);
      } else if ($comment->object_type == 'Amendement') {
        $object->Parlementaires;
        if (isset($object->Parlementaires)) foreach($object->Parlementaires as $p)
          $comment->addObject('Parlementaire', $p->id);
        if ($section = $object->getSection())
          $comment->addObject('Section', $section->getSection(1)->id);
        if (!($seance = $object->getIntervention($object->numero))) {
          $identiques = Doctrine::getTable('Amendement')->createQuery('a')
            ->where('content_md5 = ?', $object->content_md5)
            ->orderBy('numero')->execute();
          foreach($identiques as $a) {
            if ($seance) break;
            $seance = $object->getIntervention($a->numero);
          }
        }
        if ($seance)
          $comment->addObject('Seance', $seance['seance_id']);
        if ($loi) {
          if (preg_match('/^Article\s+(.*)$/', $object->sujet, $match)) {
            $art = preg_replace('/premier/i', '1er', $match[1]);
            if ($art_obj = Doctrine::getTable('ArticleLoi')->findOneByLoiTitre($object->texteloi_id,$art))
              $comment->addObject('ArticleLoi', $art_obj->id);
          } else $comment->addObject('TitreLoi', $loi->id);
        }
      }
      if (isset($object->seance_id)) {
        if ($object->seance_id)
          $comment->addObject('Seance', $object->seance_id);
      }
      if (isset($object->section_id)) {
        if ($object->section_id)
          $comment->addObject('Section', $object->section_id);
      }
      if (isset($object->article_loi_id)) {
        if ($object->article_loi_id)
          $comment->addObject('ArticleLoi', $object->article_loi_id);
      }
      if (isset($object->titre_loi_id)) {
        if ($object->titre_loi_id)
          $comment->addObject('TitreLoi', $object->titre_loi_id);
      }

      $comment->save();
      $comment->free();
    }
  }
}

