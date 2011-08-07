<?php

class SolrListener extends Doctrine_Record_Listener
{
  /**
     * Array of timestampable options
     *
     * @var string
     */
    protected $_options = array();

    private $command = null;

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

    private function get_and_strip($obj, $field) {
      if (!$obj || !get_class($obj))
	return array();
      $f = $obj->get($field);
      if ($f) {
	if (is_array($f) || (get_class($f) == 'Doctrine_Collection')) {
	  $res = array();
	  foreach($f as $i) {
	    $res[] = $i.'';
	  }
	  return $res;
	}
	if (get_class($f) && ! $f->id)
	  return array();
	return array(strip_tags($f));
      }
      return array();
    }

  private function getObjFieldsValue($obj, $fields)
  {
    if (!is_array($fields)) {
      return implode(' ',$this->get_and_strip($obj, $fields));
    }
    $s = '';
    foreach($fields as $f) {
      $s .= implode(' ', $this->get_and_strip($obj, $f)).' ';
    }
    return $s;
  }
  
  private function getLuceneObjId($obj) 
  {
    return get_class($obj).'/'.$obj->getId();
  }
  
  // Réindexation après une création / modification
  public function postSave(Doctrine_Event $event)
  {
    $obj = $event->getInvoker();

    $t = null;
    if (isset( $this->_options['index_if']) && $t = $this->_options['index_if']) {
      if (!($obj->get($t))) {
	return $this->postDelete($event);
      }
    }
    
    $json = array();
    $json['id'] = $this->getLuceneObjId($obj);
    $json['object_id'] =  $obj->getId();
    $json['object_name'] = get_class($obj);
    if (isset($this->_options['description']) && $t = $this->_options['description']) {
      $content = $this->getObjFieldsValue($obj, $t);
      $wordcount = str_word_count($content);
    }
    
    if (isset($this->_options['extra_weight'])) 
      $extra_weight = $this->_options['extra_weight'];
    else
      $extra_weight = 1;

    if (isset($this->_options['devaluate_if_wordcount_under']) && ($wclimit = $this->_options['devaluate_if_wordcount_under'])) {
       if ($wclimit > $wordcount)
	 $extra_weight *=  0.5 ;
    }
    
    // On donne un poids plus important au titre
    if (isset($this->_options['title']) && $t = $this->_options['title']) {
      $json['title']['content'] = $this->getObjFieldsValue($obj, $t);
      $json['title']['weight'] =  1.2 * $extra_weight;
    }
    
    // La description
    if (isset($content)) {
      $json['description']['content'] = $content;
      $json['description']['weight'] = $extra_weight;
      $json['wordcount'] = $wordcount;
    }
      
    // par default la date est la created_at
    if (!isset($this->_options['date']) || !($t = $this->_options['date'])) {
      $t = 'created_at';
    }
    $date = $this->getObjFieldsValue($obj, $t);
    $d = preg_replace('/\+.*/', 'Z', date('c', strtotime($date)));
    $json['date']['content'] = $d;
    $json['date']['weight'] = $extra_weight;
    

    $json['tags']['content'] = array();
    try {
      foreach($obj->getTags() as $tag) if ($tag)  {
	$json['tags']['content'][] =  preg_replace('/:/', '=', $tag);
      }
    }catch (Exception $e) {}
    
    if (isset($this->_options['moretags']) && $t = $this->_options['moretags']) {
      if (!is_array($t)) {
	$t = array($t);
      }
      foreach ($t as $i) {
	$content = $this->get_and_strip($obj, $i);
	$i = preg_replace('/([A-Z].*)s$/', '\1', $i);
	foreach($content as $c) {
	  $s = $c;
	  if (strlen($s)) {
	    $s = strip_tags($s);
	    $json['tags']['content'][] = $i.'='.$s;
	  }
	}
      }
    }
    
    $json['tags']['weight'] = $extra_weight;
    
    SolrCommands::getInstance()->addCommand('UPDATE', $json);
  }
  
  // Désindexation après une suppression
  public function postDelete(Doctrine_Event $event)
  {
    $obj = $event->getInvoker();
    $json = new stdClass();
    $json->id = $this->getLuceneObjId($obj);
    SolrCommands::getInstance()->addCommand('DELETE', $json);
  }
  
}
