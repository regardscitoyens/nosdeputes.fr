<?php

class tagComponents extends sfComponents 
{
  public function executeTagcloud() {
    $this->tags = PluginTagTable::getAllTagNameWithCount($this->tagquery, array('model' => $this->model, 'triple' => false, 'min_tags_count' => $this->min_tag, 'limit'=> $this->limit));

//    asort($this->tags);


    //Ici on cherche à groupes les tags qui sont très similaires
    foreach(array_keys($this->tags) as $tag) {
      $sex = soundex($tag);
      if (isset($sound[$sex])) {
	foreach (array_keys($sound[$sex]) as $word) {
	  $words = preg_split('/\|/', $word);
	  similar_text($tag, $words[0], $pc);
	  if ($pc >= 80) {
	    $ntag = $tag.'|'.$word;
	    if (isset($this->word[$word]))
	      $this->tags[$ntag] = $this->tags[$tag] + $this->word[$word];
	    unset($this->tags[$tag]);
	    unset($this->tags[$word]);
	    unset($sound[$sex][$tag]);
 	    unset($sound[$sex][$word]);
	    $sound[$sex][$ntag] = 1;
	    continue;
	  }
	}
      }
      $sound[$sex][$tag] = 1;
    }


    //On trie par ordre alpha, et inserre des infos sur l'utilisation des tags (class + count)
    $tot = count($this->tags);
    $cpt = 0;
    asort($this->tags);
    $class = array();
    foreach(array_keys($this->tags) as $tag) {
      $count = $this->tags[$tag];
      unset($this->tags[$tag]);
      $related = preg_split('/\|/', $tag);
      $tag = $related[0];
      $this->tags[$tag] = array();
      $this->tags[$tag]['count'] = $count;
      if (!isset($class[$count]))
	$class[$count] = intval($cpt * 5 / $tot);
      $cpt++;
      $this->tags[$tag]['tag'] = $tag;
      $this->tags[$tag]['class'] = $class[$count];
      $this->tags[$tag]['related'] = implode('|', $related);
    }
    uksort($this->tags, 'strcasecmp');
  }

  public function executeParlementaire() {
    $this->qtag = Doctrine_Query::create()
      ->from('Tagging tg, tg.Tag t, Intervention i')
      ->where('i.parlementaire_id = ?', $this->parlementaire->id)
      ->andWhere('i.date > ?', date('Y-m-d', time()-60*60*24*365))
      ->andWhere('i.id = tg.taggable_id');
  }

  public function executeGlobalActivite() {
    $inter = Doctrine::getTable('Intervention')->createQuery('i')
      ->orderBy('i.date DESC')
      ->limit(5000)->fetchArray();
    $ids = array();
    foreach($inter as $i) {
      $ids[] = $i['id'];
    }
    $this->itag = Doctrine_Query::create()
      ->from('Tagging tg, tg.Tag t')
      ->andwhere('tg.taggable_model = ?', 'Intervention')
      ->whereIn('tg.taggable_id', $ids);
  }
}
