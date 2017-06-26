<?php

class SolrConnector extends sfLogger
{
  private $solr = NULL;
  private $_options = NULL;
  private $nb_commit = 0;

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
    }

    $this->_options = $listener_options;

    return $this->solr;
  }


  public function updateFromCommands($output = 0) {
    $file = SolrCommands::getInstance()->getCommandContent();
    $nlines = 0;
    foreach(file($file) as $line) {
      $nlines++;
      if ($nlines > 301) {
        $this->commit(1);
        return false;
      }
      if (preg_match('/(UPDATE|DELETE) : (.+)/', $line, $matches)) {
        $obj = json_decode($matches[2]);
        if ($output)
          echo "ID: ".$obj->id."\n";
        if ($matches[1] == 'UPDATE') {
          $this->updateLuceneRecord($obj);
        }else{
          $this->deleteLuceneRecord($obj->id);
        }
      }
    }
    $this->commit(1);
    SolrCommands::getInstance()->releaseCommandContent();
    return true;
  }

  public function commit($force = false , $optimize = false) {
	$wait = false;
	$this->nb_commit++;
	if ($this->nb_commit > 1000 || $force) {
		$wait = true;
		$this->nb_commit = 0;
	}
	if ($this->nb_commit % 100 || $force) {
		return $this->solr->commit($optimize, $wait);
	}
	return true;
  }

  public function deleteLuceneRecord($solr_id)
  {
    if($this->solr->deleteById($solr_id) ) {
      return $this->commit(1);
    }
    return false;
  }

  public function updateLuceneRecord($obj)
  {
     $document = new Apache_Solr_Document();
     $document->addField('id', $obj->id);
     $document->addField('object_id', $obj->object_id);
     $document->addField('object_name', $obj->object_name);
     if (isset($obj->wordcount))
       $document->addField('wordcount', $obj->wordcount);
     if (isset($obj->title))
       $document->addField('title', $obj->title->content, $obj->title->weight);
     if (isset($obj->description))
       $document->addField('description', $obj->description->content, $obj->description->weight);
     if (isset($obj->date))
       $document->addField('date', $obj->date->content, $obj->date->weight);
     if (isset($obj->tags)) {
        foreach($obj->tags->content as $tag) if ($tag)  {
	  $document->setMultiValue('tag', $tag, $obj->tags->weight);
	}
     }
     $this->solr->addDocument($document);
     $this->commit();
  }

  public function deleteAll() {
    $this->solr->deleteByQuery('*:*');
    $this->commit(1);
  }

  public function search($queryString, $params = array(), $offset = 0, $maxHits = 0) {
    if($maxHits == 0)
        $maxHits = sfConfig::get('app_solr_max_hits', 256);
    if ((isset($params['not_utf8']) && $params['not_utf8']) || !sfConfig::get('app_solr_utf8', true))  {
	    $queryString = utf8_decode($queryString);
    }
    $response = $this->solr->search($queryString, $offset, $maxHits, $params);
    $results = unserialize($response->getRawResponse());
    $unset = array();
    for ($i = 0 ; $i < count($results['response']['docs']); $i++) {
      $res = $results['response']['docs'][$i];
      if ($res['object_name'] == 'NonObjectPage') {
	$results['response']['docs'][$i]['object'] = NonObjectPage::find($res['object_id']);
      }else{
	$results['response']['docs'][$i]['object'] = Doctrine::getTable($res['object_name'])->find($res['object_id']);
      }
      if (!$results['response']['docs'][$i]['object']) {
        if (sfConfig::get('app_allow_autoclean_solr'))
          $this->deleteLuceneRecord($res['id']);
        $unset[] = $i;
      }
    }
    foreach ($unset as $i) {
      unset($results['response']['docs'][$i]);
    }
    $results['response']['docs'] = array_values($results['response']['docs']);
    return $results;
  }

}
