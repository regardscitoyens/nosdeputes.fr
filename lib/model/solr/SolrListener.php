<?php

class SolrConnector extends sfLogger
{
  private $solr = NULL;
  private $_options = NULL;

  protected function doLog($message, $priority)
  {
    error_log(sprintf('%s (%s)', $message, sfLogger::getPriorityName($priority)));
  }

  public function __construct( $listener_options = NULL)
  {
    $host = sfConfig::get('app_solr_host', 'localhost');
    $port = sfConfig::get('app_solr_port', '8983');
    $url = sfConfig::get('app_solr_url', '/solr');
    $this->solr = new Apache_Solr_Service($host, $port, $url);
    
    if(!$this->solr->ping()) {
      throw new Exception('Search is not available right now.');
//	$this->doLog('SolrConnector: Arg, Search is not available right now', sfLogger::ERR);
    }
    
    $this->_options = $listener_options;

    return $this->solr;
  }
  
  private function get_and_strip($obj, $field) {
    $f = $obj->get($field);
    if ($f) {
      if (get_class($f) && ! $f->id)
	return ;
      return strip_tags($f);
    }
    return ;
  }

  private function getObjFieldsValue($obj, $fields)
  {
    if (!is_array($fields)) {
      return $this->get_and_strip($obj, $fields);
    }
    $s = '';
    foreach($fields as $f) {
      $s .= $this->get_and_strip($obj, $f).' ';
    }
    return $s;
  }
  
  private function getLuceneObjId($obj) 
  {
    return get_class($obj).'/'.$obj->getId();
  }
  
  public function deleteLuceneRecord($obj)
  {
    if($this->solr->deleteById($this->getLuceneObjId($obj))) 
      return $this->solr->commit();
    return false;
  }

  public function updateLuceneRecord($obj)
  {
    $t = NULL;
    if ($t = $this->_options['index_if'] && $t && $obj->get($t))
      return ;
    $document = new Apache_Solr_Document();
    $document->addField('id', $this->getLuceneObjId($obj));
    $document->addField('object_id', $obj->getId());
    $document->addField('object_name', get_class($obj));


    if ($t = $this->_options['description']) {
      $content = $this->getObjFieldsValue($obj, $t);
      $wordcount = str_word_count($content);
    }

    if (isset($this->_options['extra_weight'])) 
      $extra_weight = $this->_options['extra_weight'];
    else
      $extra_weight = 1;

    if (isset($this->_options['devaluate_if_wordcount_under']) && ($wclimit = $this->_options['devaluate_if_wordcount_under'])) {
       if ($wclimit > $wordcount)
	 $extra_weight *= ($wordcount*0.5) / $wclimit + 0.5 ;
    }
    
    // On donne un poids plus important au titre
    if (isset($this->_options['title']) && $t = $this->_options['title']) {
      $document->addField('title', $this->getObjFieldsValue($obj, $t), 1.2 * $extra_weight);
    }
    // La description
      if (isset($content)) {
      $document->addField('description', $content, $extra_weight);
      $document->addField('wordcount', $wordcount);
    }
    
    // par default la date est la created_at
    if ( !($t = $this->_options['date'])) {
      $t = 'created_at';
    }
    $d = preg_replace('/\+.*/', 'Z', date('c', strtotime($this->getObjFieldsValue($obj, $t))));
    $document->addField('date', $d, $extra_weight);
    
    try {
      foreach($obj->getTags() as $tag) if ($tag)  {
	$document->setMultiValue('tag', preg_replace('/:/', '=', $tag), $extra_weight);
      }
    }catch (Exception $e) {}
    
    if ($t = $this->_options['moretags']) {
      if (!is_array($t)) {
	$s = $this->get_and_strip($obj, $t);
	if ($s)
	  $document->setMultiValue('tag', $t.'='.$s, $extra_weight);
      }else{
	foreach ($t as $i) {
	  $s = $this->get_and_strip($obj, $i);
	  if (strlen($s)) {
	    $s = strip_tags($s);
	    $document->setMultiValue('tag', $i.'='.$s, $extra_weight);
	  }
	}
      }
    }
    
    $this->solr->addDocument($document);
    $this->solr->commit();
  }

  public function deleteAll() {
    $this->solr->deleteByQuery('*:*');
    $this->solr->commit();
  }

  public function search($queryString, $params = array(), $offset = 0, $maxHits = 0) {
    if($maxHits == 0)
        $maxHits = sfConfig::get('app_solr_max_hits', 256);
    $response = $this->solr->search($queryString, $offset, $maxHits, $params);
    return unserialize($response->getRawResponse());
  }
  
}

class SolrListener extends Doctrine_Record_Listener
{
  /**
     * Array of timestampable options
     *
     * @var string
     */
    protected $_options = array();
    /**
     * __construct
     *
     * @param string $options 
     * @return void
     */
    public function __construct($options)
    {
      $this->_options = $options;
    }

    protected $solr = NULL;
    protected function getSolrConnector() {
      if (!$this->solr)
	$this->solr = new SolrConnector($this->_options);
      return $this->solr;
    }
    
    // Réindexation après une création / modification
    public function postSave(Doctrine_Event $event)
    {
      $obj = $event->getInvoker();
      $this->getSolrConnector()->updateLuceneRecord($obj);
    }
    
    // Désindexation après une suppression
    public function postDelete(Doctrine_Event $event)
    {
      $this->getSolrConnector()->deleteLuceneRecord($event->getInvoker());
    }
    
}
