<?php

/**
 * section actions.
 *
 * @package    cpc
 * @subpackage section
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class sectionActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeParlementaire(sfWebRequest $request)
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
    if (myTools::isLegislatureCloturee() && $this->parlementaire->url_nouveau_cpc)
      $this->response->addMeta('robots', 'noindex,follow');
    $this->titre = 'Dossiers parlementaires';
    $this->response->setTitle($this->titre.' de '.$this->parlementaire->nom.' - NosDéputés.fr');
  }

  public function executeParlementaireSection(sfWebRequest $request) 
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);

    if (myTools::isLegislatureCloturee() && !$this->parlementaire->url_nouveau_cpc)
      $this->response->addMeta('robots', 'noindex,follow');

    $this->section = Doctrine::getTable('Section')->find($request->getParameter('id'));
    $this->forward404Unless($this->section);

    $this->qinterventions = Doctrine::getTable('Intervention')->createQuery('i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->leftJoin('i.Section s')
      ->andWhere('(s.section_id = ? OR s.id = ?)', array($this->section->id, $this->section->id))
      ->andWhere('i.nb_mots > 20')
      ->orderBy('i.date DESC, i.timestamp ASC');
  }
  public function executeShow(sfWebRequest $request) 
  {
    $secid = $request->getParameter('id');
    $this->forward404Unless($secid);
    if ($option = Doctrine::getTable('VariableGlobale')->findOneByChamp('linkdossiers')) {
      $links = unserialize($option->getValue());
      if (isset($links[$secid]))
        $secid = $links[$secid];
    }
    $this->section = Doctrine::getTable('Section')->find($secid);
    $this->forward404Unless($this->section);
    $this->seances = $this->section->getSeances();
    if (count($this->seances) == 1 && $this->seances[0]->type === "commission")
      $this->redirect('@interventions_seance?seance='.$this->seances[0]->id.'#table_'.$this->section->id);

    $lois = $this->section->getTags(array('is_triple' => true,
                                          'namespace' => 'loi',
                                          'key' => 'numero',
                                          'return' => 'value'));
    $this->docs = array();
    if ($this->section->id_dossier_an || $lois) {
      $qtextes = Doctrine_Query::create()
        ->select('t.id, t.type, t.type_details, t.titre, t.signataires, t.nb_commentaires')
        ->from('Texteloi t')
        ->whereIn('t.numero', $lois);
      if ($this->section->id_dossier_an)
        $qtextes->orWhere('t.id_dossier_an = ?', $this->section->id_dossier_an);
      $qtextes->orderBy('t.numero, t.annexe');
      $textes = $qtextes->fetchArray();

      $textes_loi = Doctrine_Query::create()
        ->select('t.texteloi_id, t.titre, t.nb_commentaires')
        ->from('TitreLoi t')
        ->whereIn('t.texteloi_id', $lois)
        ->andWhere('t.chapitre IS NULL')
        ->andWhere('t.section is NULL')
        ->orderBy('t.texteloi_id')
        ->fetchArray();

      foreach ($textes as $texte)
        $this->docs[$texte['id']] = $texte;
      foreach ($textes_loi as $texte)
        $this->docs[$texte['texteloi_id']] = $texte;
      foreach ($lois as $loi)
        if (!isset($this->docs["$loi"]))
          $this->docs["$loi"] = 1;
    }   

    $interventions = array();

    $query = Doctrine_Query::create()
      ->select('i.id')
      ->from('Intervention i')
      ->leftJoin('i.Section s');
    if ($this->section->id == $this->section->section_id)
      $query->where('s.section_id = ?', $this->section->id);
    else $query->where('s.id = ?', $this->section->id);
    $interventions = $query->andWhere('i.nb_mots > 20')
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    //    $this->forward404Unless(count($interventions));

    $this->qtag = Doctrine_Query::create()
      ->from('Tagging tg, tg.Tag t');
    if (count($interventions))
      $this->qtag->whereIn('tg.taggable_id', $interventions);
    else
      $this->qtag->where('false');

    $this->interventions = array();
    if (count($interventions)) {
      $this->interventions = $interventions;
    }
    
    $request->setParameter('rss', array(array('link' => '@section_rss_commentaires?id='.$this->section->id, 'title'=>'Les commentaires sur '.$this->section->titre)));
  }

  public function executeList(sfWebRequest $request) 
  {
    if (!($this->order = $request->getParameter('order')))
      $this->order = 'plus';
    $query = Doctrine::getTable('Section')->createQuery('s')
      ->where('s.id = s.section_id');
    if ($this->order == 'date') {
      $query->orderBy('s.max_date DESC');
      $this->titre = 'Les derniers dossiers traités à l\'Assemblée';
    } else if ($this->order == 'plus') {
      $query->orderBy('s.nb_interventions DESC');
      $this->titre = 'Les dossiers les plus discutés à l\'Assemblée';
    } else if ($this->order == 'coms') {
      $query->orderBy('s.nb_commentaires DESC');
      $this->titre = 'Les dossiers de l\'Assemblée les plus commentés par les citoyens';
    } else if ($this->order == 'nom') {
      $query->orderBy('s.titre');
      $this->titre = 'Les dossiers de l\'Assemblée dans l\'ordre alphabétique';
    } else $this->forward404();
    $this->getResponse()->setTitle(str_replace('Assemblée', 'Assemblée nationale', $this->titre)." - NosDéputés.fr");
    $this->sections = $query->execute();

  }
}
