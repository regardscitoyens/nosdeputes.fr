<?php


class SolrConnector extends sfLogger
{
  private $solr = NULL;
  private $_options = NULL;

  public static function getFileCommands() {
    return sfConfig::get('sf_log_dir').'/solr/commands.log';
  }

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
  

  public function updateFromCommands() {
    if (!file_exists(self::getFileCommands().'.lock') && file_exists(self::getFileCommands()))
      rename(self::getFileCommands(), self::getFileCommands().'.lock');
    if (!file_exists(self::getFileCommands().'.lock'))
      return ;
    foreach(file(self::getFileCommands().'.lock') as $line) {
      if (preg_match('/\] (UPDATE|REMOVE): (.+)/', $line, $matches)) {
	if ($matches[1] == 'UPDATE') {
	  $obj = Doctrine::getTable($matches[2])->find($matches[3]);
	  if ($obj)
	    $this->updateLuceneRecord($obj);
	  else
	    echo $matches[2].'/'.$matches[3]." not found\n";
	}else{
	  $this->solr->deleteById($matches[2].'/'.$matches[3]);
	}
      }
    }
    unlink(self::getFileCommands().'.lock');
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
    $obj_options = $obj->getListener()->getOptions();

    print_r($obj->getOptions());
    exit;
    
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

    private function sendCommand($status, $json) {
      self::getFileCommand()->log($status.': '.json_encode($json));
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
      //      $this->getSolrConnector()->updateLuceneRecord($obj);

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
	 $extra_weight *= ($wordcount*0.5) / $wclimit + 0.5 ;
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

    
    $this->sendCommand('UPDATE', $json);
    }
    
    // Désindexation après une suppression
    public function postDelete(Doctrine_Event $event)
    {
      $obj = $event->getInvoker();
      //      $this->getSolrConnector()->deleteLuceneRecord($obj);
      $json = new stdClass();
      $json->id = $this->getLuceneObjId($obj);
      $this->sendCommand('DELETE', $json);
    }
    
}
