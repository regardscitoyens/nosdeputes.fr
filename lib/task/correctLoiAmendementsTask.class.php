<?php

class correctLoiAmendementsTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'correct';
    $this->name = 'LoiAmendements';
    $this->briefDescription = 'Correct Loi Amendements';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }
 

  protected function changeAmendement($idsql) {
    foreach($idsql->execute() as $i) {
      if (isset($i['cpt']) && $i['cpt'] <= 1)
	return 0;

      $amds = Doctrine::getTable('Amendement')->createQuery('a')
	->where('a.numero = ?', $i['numero'])
	->andWhere('a.texteloi_id = ?', $i['texteloi_id'])
	->execute();

      foreach($amds as $a) {
	if (preg_match('/(\d{4})\/(\d{4})([A-Z]?)(\d+)/', $a->source, $match) && $match[1] == $match[2]) {
	  echo $a->texteloi_id.' '.$a->numero." => ";
	  $a->texteloi_id = $match[2]+0;
	  $a->numero = ($match[4] + 0).$match[3];
	  echo $a->texteloi_id.' '.$a->numero." | ";
          $a->addTag('loi:amendement='.$a->numero);
	  $a->save();
	}
      }
    }
    return 1;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $dir = dirname(__FILE__).'/../../batch/depute/out/';
    $manager = new sfDatabaseManager($this->configuration);    
    $ids = Doctrine_Query::create()->from('Amendement a')
      ->where("a.source REGEXP 'amendements.*[A-Z].*asp'")
      ->orWhere("a.source NOT REGEXP CONCAT('amendements.0{0,3}',a.texteloi_id)")
      ->select('a.id, a.texteloi_id, a.numero')
      ;
    $this->changeAmendement($ids);

    while (1) {
      $ids = Doctrine_Query::create()->from('Amendement a')->groupBy('a.texteloi_id, a.numero')
	->select('a.id, a.texteloi_id, a.numero, count(*) as cpt')
	->orderBy('cpt desc')
	->limit(100)
	;
      if(!$this->changeAmendement($ids))
	break;
    }
    echo "\n";
  }
}
