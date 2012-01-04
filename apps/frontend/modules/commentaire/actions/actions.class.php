<?php

/**
 * commentaire actions.
 *
 * @package    cpc
 * @subpackage commentaire
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class commentaireActions extends sfActions
{
  
  public function executePost(sfWebRequest $request)
  {
    $redirect_url = array('Intervention' => '@intervention?id=', 'Amendement' => '@amendement_id?id=', 'QuestionEcrite' => '@question_id?id=', 'ArticleLoi' => '@loi_article_id?id=', 'Alinea' => '@loi_alinea?id=', 'Texteloi' => '@document?id=');
    $about = array('Intervention' => "Suite aux propos d", 'Amendement' => "Au sujet d'un amendement déposé", 'QuestionEcrite' => "À propos d'une question écrite d");
	
    $this->type = $request->getParameter('type');
    $this->id = $request->getParameter('id');
    $this->follow_talk = $request->getParameter('follow_talk');
    
    $values = $request->getParameter('commentaire');
    $this->commentaire = myTools::clearHtml($values['commentaire']);
    $this->unique_form = $request->getParameter('unique_form');

    if ($this->getUser()->getAttribute('commentaire_'.$this->type.'_'.$this->id) != $this->unique_form) {
      $this->getUser()->setFlash('error', 'Vous avez déjà posté ce commentaire...');
      return $this->redirect($redirect_url[$this->type].$this->id);
    }

    if (!$request->isMethod('post')) {
      $this->getUser()->setFlash('error', 'Un problème technique est survenu');
      return $this->redirect($redirect_url[$this->type].$this->id);
    }

    /** On logue l'utilisateur si il a donné un login et mot de passe correct sinon creation du form et template**/
    $isAuthenticated = $this->getUser()->isAuthenticated();
    /** Pas loggué on s'assure que quelqu'un n'a pas trouvé notre hack */
    $_GET['isAuthenticated'] = $isAuthenticated;
    if (isset($values['login']) && isset($values['password']) && $values['login'] != "" && $values['password'] != "") {
      if (!($citoyen_id = myUser::SignIn($values['login'], $values['password'], false, $this))) {
	$this->form = new CommentaireForm();
	$this->form->bind($values);
	return ;
      }
      $is_active = true;
      $isAuthenticated = 1;
      //Pour communication avec CommentaireForm->configure()
      $_GET['isAuthenticated'] = 1;
      //Invalide les champs dont on a plus besoin come l'utilisateur est connecté
      unset($values['login']);
      unset($values['password']);
      unset($values['email']);
      unset($values['nom']);
    }

    /** Creation du form et validation */
    $this->form = new CommentaireForm();
    $this->form->bind($values);	
    if (!$request->getParameter('ok') || !$this->form->isValid())
      return ;


    if ($isAuthenticated) {
      $citoyen_id = $this->getUser()->getAttribute('user_id');
      $is_active = $this->getUser()->getAttribute('is_active');
    } else if ($values['nom'] && $values['email']) {
      $this->getUser()->setAttribute('partial', 'inscriptioncom');
      if (!($citoyen_id = myUser::CreateAccount($values['nom'], $values['email'], $this)))
	return ;
      $is_active = false;
    } else { //Si pas de (login et mdp) ou (email, login)
      $this->getUser()->setFlash('error', 'Vous devez avoir un compte et y être connecté pour poster un commentaire.<br />Le formulaire ci-dessous vous permet de vous identifier ou de vous inscrire sur le site.');
      return;
    }
    $ip = '';
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    $ip = $ip . ',';
    if(isset($_SERVER['HTTP_CLIENT_IP'])) 
	    $ip = $ip . $_SERVER['HTTP_CLIENT_IP'];
    $ip = $ip . ',';
    if(isset($_SERVER['REMOTE_ADDR'])) 
	    $ip = $ip . $_SERVER['REMOTE_ADDR']; 

  // On teste l'existence préalable du même commentaire
    if ($existing = Doctrine::getTable('Commentaire')->createQuery('c')
      ->select('created_at')
      ->where('citoyen_id = ?', $this->getUser()->getAttribute('user_id'))
      ->andWhere('commentaire LIKE ?', $this->commentaire)
      ->andWhere('object_type = ?', $this->type)
      ->andWhere('object_id = ?', $this->id)
      ->fetchArray()) {
      $this->getUser()->setFlash('error', 'Vous avez déjà posté ce même commentaire le '.myTools::displayShortDate($existing[0]['created_at']));
      return;
    }

  
    $commentaire = $this->form->getObject();
    //Pas trouvé d'autre moyen que de bypasser le form pour conserver la presentation htmlisée
    $commentaire->commentaire = $this->commentaire;
    $commentaire->object_type = $this->type;
    $commentaire->object_id = $this->id;
    $commentaire->lien = $redirect_url[$this->type].$this->id;
    $object = Doctrine::getTable($this->type)->find($this->id);
    if ($this->type === 'Texteloi') 
      $present = $object->getShortTitre();
    else if (isset($object->texteloi_id)) {
      $titreloi = Doctrine::getTable('TitreLoi')->findLightLoi($object->texteloi_id);
      $loi = Doctrine::getTable('Texteloi')->findLoi($object->texteloi_id);
      if ($this->type != 'Amendement') {
        if ($titreloi)
          $present = preg_replace('/<br\/>.*$/', '', $titreloi['titre']).' - À propos de l\'article ';
        else if($loi)
          $present = $loi->getTitre().' - À propos de l\'article n°'.$object->numero;
        if ($this->type == 'Alinea') {
          $article = Doctrine::getTable('ArticleLoi')->createQuery('a')
            ->select('titre')
            ->where('texteloi_id = ?', $object->texteloi_id)
            ->andWhere('id = ?', $object->article_loi_id)
            ->fetchOne();
          $present .= $article['titre'].' alinéa '.$object->numero;
        } else $present .= $object->titre;
      } else if ($titreloi)
        $present = preg_replace('/<br\/>.*$/', '', $titreloi['titre']).' - À propos de l\'amendement n°'.$object->numero;
      else if ($loi)
        $present = $loi->getShortTitre().' - À propos de l\'amendement n°'.$object->numero;
      else $present = $about[$this->type].' le '.date('d/m/Y', strtotime($object->date));
    } else {
      $present = '';
      if ($this->type != 'QuestionEcrite') {
        if ($section = $object->getSection())
          $present = $section->getSection(1)->getTitre();
        if ($present == '' && $this->type == 'Intervention' && $object->type == 'commission')
          $present = $object->getSeance()->getOrganisme()->getNom();
      }
      if ($present != '') $present .= ' - ';   
      else $present = '';
      $present .= $about[$this->type];
      $nom = '';
      if ($this->type == 'QuestionEcrite')
        $nom = $object->getParlementaire()->nom;
      else if ($this->type == 'Intervention')
        $nom = $object->getIntervenant()->nom;
      if ($nom != '') {
        if (preg_match('/^[AEIOUYÉÈÊ]/', $nom)) $nom = '\''.$nom;
        else $nom = 'e '.$nom;
        $present .= $nom;
      }
      $present .= ' le '.date('d/m/Y', strtotime($object->date));
    }
    $commentaire->presentation = $present;
    $commentaire->citoyen_id = $citoyen_id;
    $commentaire->is_public = $is_active;
    $commentaire->ip_address = $ip;
    $commentaire->save();

    if ($this->follow_talk) {
	$alerte = new Alerte();
	$alerte->citoyen_id = $citoyen_id;
    	$alerte->query = "object_name:Commentaire tag:object_type=".$commentaire->object_type." tag:object_id=".$commentaire->object_id;
    	$alerte->no_human_query = 1;
    	$alerte->period = 'HOUR';
    	$alerte->titre = "Suivi de votre conversation sur $present";
    	$alerte->save();
    }
    $object->updateNbCommentaires();
    $object->save();

    if (isset($object->parlementaire_id)) {
      if ($object->parlementaire_id)
        $commentaire->addObject('Parlementaire', $object->parlementaire_id);
    } else if ($this->type === 'Amendement' || $this->type === 'Texteloi') {
      $parlementaires = $object->getParlementaires();
      if ($parlementaires) foreach($parlementaires as $p)
        $commentaire->addObject('Parlementaire', $p->id);
    }
    if ($this->type === 'Amendement' && !($seance = $object->getIntervention($object->numero))) {
      $identiques = Doctrine::getTable('Amendement')->createQuery('a')
        ->where('content_md5 = ?', $object->content_md5)
        ->orderBy('numero')->execute();
      foreach($identiques as $a) {
        if ($seance) break;
        $seance = $object->getIntervention($a->numero);
      }
    }
    if (isset($seance))
      $commentaire->addObject('Seance', $seance['seance_id']);
    if ($this->type === 'Amendement' && isset($titreloi)) {
      if (preg_match('/^Article\s+(.*)$/', $object->sujet, $match)) {
        $art = preg_replace('/premier/i', '1er', $match[1]);
        if ($art_obj = Doctrine::getTable('ArticleLoi')->findOneByLoiTitre($object->texteloi_id,$art))
          $commentaire->addObject('ArticleLoi', $art_obj->id);
      } else $commentaire->addObject('TitreLoi', $titreloi->id);
      $commentaire->addObject('Texteloi', $titreloi->texteloi_id);
    }
    if (($this->type === "Titreloi" && $section = $object->getDossier()) || (($this->type === "TexteLoi" || $this->type === "Amendement") && $section = $object->getSection()))
      $commentaire->addObject('Section', $section->getSection(1)->id);
    if (isset($object->seance_id)) {
      if ($object->seance_id)
        $commentaire->addObject('Seance', $object->seance_id);
    }
    if (isset($object->section_id)) {
      if ($object->section_id)
        $commentaire->addObject('Section', $object->section_id);
    }
    if (isset($object->article_loi_id)) {
      if ($object->article_loi_id)
        $commentaire->addObject('ArticleLoi', $object->article_loi_id);
    }
    if (isset($object->titre_loi_id)) {
      if ($object->titre_loi_id)
        $commentaire->addObject('TitreLoi', $object->titre_loi_id);
    }
    if (isset($object->texteloi_id)) {
      if ($object->texteloi_id)
        $commentaire->addObject('Texteloi', $object->texteloi_id);
    }

      
    $pas_confirme_mail = '';
    if (!$is_active) {
      $pas_confirme_mail = ', pour le rendre public, cliquez sur le lien d\'activation contenu dans l\'email que nous vous avons envoyé afin de terminer votre inscription.';
    }
    $this->getUser()->setFlash('notice', 'Votre commentaire a été enregistré'.$pas_confirme_mail);
    $this->getUser()->getAttributeHolder()->remove('commentaire_'.$this->type.'_'.$this->id);

    return $this->redirect($commentaire->lien);
  }

  public function executeJson(sfWebRequest $request) {
    $this->setLayout(false);
    if ($seance_id = $request->getParameter('seance'))
      $query = Doctrine::getTable('Intervention')->createQuery('i');
    else if ($article_id = $request->getParameter('article'))
      $query = Doctrine::getTable('Alinea')->createQuery('i');
    else $this->forward404();
    $query->select('i.id, i.nb_commentaires');
    if ($seance_id)
      $query->where('seance_id = ?', $seance_id);
    else $query->where('article_loi_id = ?', $article_id);
    $this->commentaires = $query->fetchArray();
    $this->getResponse()->setHttpHeader('content-type', 'text/plain');
  }

  public function executeShowAjax(sfWebRequest $request) {
    $this->setLayout(false);
    $this->id = $request->getParameter('id');
    $this->forward404Unless($this->id);
    $query = Doctrine::getTable('Commentaire')->createQuery('c')
      ->where('object_id = ?', $this->id)
      ->andWhere('is_public = ?', 1);
    if ($request->getParameter('intervention')) {
      $query->andWhere('object_type = ?', 'Intervention');
      $this->type = 'Intervention';
    } else if ($request->getParameter('alinea')) {
      $query->andWhere('object_type = ?', 'Alinea');
      $this->type = 'Alinea';
    } else $this->forward404();
    if ($this->limit = $request->getParameter('limit'))
      $query->orderBy('created_at DESC')
        ->limit($this->limit);
    else $query->orderBy('created_at');
    $this->comments = $query->execute();
    $this->getResponse()->setHttpHeader('content-type', 'text/plain');
  }

  public function executeList(sfWebRequest $request) {
    $this->titre = 'Les derniers commentaires';
    $this->url_link = '';
    if ($slug = $request->getParameter('slug')) {
      $this->type = 'Parlementaire';
      $this->object = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
      $this->presentation = 'noauteur';
      $this->linkrss = '@parlementaire_rss_commentaires?slug='.$slug;
      $this->titre .= ' sur l\'activité';
    } else if ($id = $request->getParameter('id')) {
      $this->type = 'Section';
      $this->object = Doctrine::getTable('Section')->find($id);
      $this->presentation = 'nodossier';
      $this->linkrss = '@section_rss_commentaires?id='.$id;
      $this->url_link = '@section?id=';
    } else if ($loi = $request->getParameter('loi')) {
      $this->type = 'TitreLoi';
      $this->object = Doctrine::getTable('TitreLoi')->findLoi($loi);
      $this->presentation = 'noloi';
      $this->linkrss = '@loi_rss_commentaires?loi='.$loi;
      $this->url_link = '@loi?loi=';
    } else {
      $this->type = 'all';
      $this->linkrss = '@commentaires_rss';
      $this->presentation = '';
    }
    $this->commentaires = Doctrine::getTable('Commentaire')->createQuery('c')
      ->leftJoin('c.Objects co')
      ->orderBy('created_at DESC');
    if ($this->type == 'all') {
      $commentsids = array();
      foreach (Doctrine_Query::create()
        ->select('c.id')
        ->from('Commentaire c')
        ->where('c.id = (SELECT c2.id FROM commentaire c2 WHERE c.citoyen_id = c2.citoyen_id ORDER BY c2.created_at DESC LIMIT 1)')
        ->andWhere('c.is_public = 1')
        ->orderBy('c.created_at DESC')
        ->fetchArray() as $com)
          $commentsids[] = $com['id'];
      $this->commentaires->andWhere('id IN ('.implode(',', array_values($commentsids)).')');
    } else {
      $this->forward404Unless($this->object);
      if ($this->type != 'Parlementaire') {
        $this->titre .= ' sur '.$this->object->titre;
        if ($this->type == 'Section')
          $this->url_link .= $this->object->id;
        else if ($this->type == 'TitreLoi')
          $this->url_link .= $this->object->texteloi_id;
      }
      $this->commentaires->andWhere('is_public = 1')
        ->andWhere('co.object_type = ?', $this->type)
        ->andWhere('co.object_id = ?', $this->object->id);
    }
    if ($request->getParameter('rss')) {
      if ($this->type == 'all') $this->titre .= ' de NosDéputés.fr';
      $this->comments = $this->commentaires->limit(20)->execute();
      $this->setTemplate('rss');
      $this->feed = new sfRssFeed();
    } else {
      $request->setParameter('rss', array(array('link' => $this->link, 'title'=>'Les derniers commentaires en RSS')));
      if ($this->type == 'Parlementaire')
        $this->response->setTitle($this->titre.' de '.$this->object->nom.' - NosDéputés.fr');
      else $this->response->setTitle(strip_tags($this->titre).' - NosDéputés.fr');
    }

  }
  public function executeWidget() {
    $this->setLayout(false);
  }

}
