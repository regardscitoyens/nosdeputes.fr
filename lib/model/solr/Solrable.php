<?php

class Solrable extends Doctrine_Template
{
  protected $_options = array();
  /**
   * __construct
   *
   * @param string $array 
   * @return void
   */
  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
  }
  
  public function setTableDefinition()
  {
    $this->addListener(new SolrListener($this->_options));
  }
}