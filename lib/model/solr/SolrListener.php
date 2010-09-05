<?php

class SolrListener extends Doctrine_Record_Listener
{
  /**
     * Array of timestampable options
     *
     * @var string
     */
    protected $_options = array();

    protected static $fileCommand = null;
    protected static $fileDispatcher = null;

    protected static function getFileCommand() {
      if (!self::$fileDispatcher) {
	self::$fileDispatcher = new sfEventDispatcher();
      }
      if (!self::$fileCommand) {
	self::$fileCommand = new sfFileLogger(self::$fileDispatcher, array('file' => SolrConnector::getFileCommands()));
      }
      return self::$fileCommand;
    }

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
  
  // Réindexation après une création / modification
  public function postSave(Doctrine_Event $event)
  {
    $obj = $event->getInvoker();
    
    if ($t = $this->_options['index_if'] && $t && $obj->get($t))
      return ;
    
    $json = array();
    $json['id'] = $this->getLuceneObjId($obj);
    $json['object_id'] =  $obj->getId();
    $json['object_name'] = get_class($obj);
    
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
    if ( !($t = $this->_options['date'])) {
      $t = 'created_at';
    }
    $d = preg_replace('/\+.*/', 'Z', date('c', strtotime($this->getObjFieldsValue($obj, $t))));
    $json['date']['content'] = $d;
    $json['date']['weight'] = $extra_weight;
    

    $json['tags']['content'] = array();
    try {
      foreach($obj->getTags() as $tag) if ($tag)  {
	$json['tags']['content'][] =  preg_replace('/:/', '=', $tag);
      }
    }catch (Exception $e) {}
    
    if ($t = $this->_options['moretags']) {
      if (!is_array($t)) {
	$s = $this->get_and_strip($obj, $t);
	if ($s)
	  $json['tags']['content'][] = $t.'='.$s;
      }else{
	foreach ($t as $i) {
	  $s = $this->get_and_strip($obj, $i);
	  if (strlen($s)) {
	    $s = strip_tags($s);
	    $json['tags']['content'][] = $i.'='.$s;
	  }
	}
      }
    }
    
    $json['tags']['weight'] = $extra_weight;
    
    SolrCommands::addCommand('UPDATE', $json);
  }
  
  // Désindexation après une suppression
  public function postDelete(Doctrine_Event $event)
  {
    $obj = $event->getInvoker();
    $json = new stdClass();
    $json->id = $this->getLuceneObjId($obj);
    SolrCommands::addCommand('DELETE', $json);
  }
  
}
