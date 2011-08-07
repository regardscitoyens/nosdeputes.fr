<?php

/**
 * taggableComplete actions.
 *
 * @package    sfDoctrineActAsTaggable
 * @subpackage taggableComplete
 * @author     Tom Boutell, P'unk Avenue, www.punkave.com
 */
class taggableCompleteActions extends sfActions
{
  /**
   * Default index action: nothing to see here move along
   *
   */
  public function executeIndex()
  {
    $this->forward404();
  }
  /**
   * Tag typeahead AJAX. You might want to secure this action to prevent
   * information discovery in some cases
   *
   */
  public function executeComplete()
  {
    $this->setLayout(false);
    $current = $this->getRequestParameter('current');
    $tags = array();
    $tagsInfo = array();
    $tagsAll = array();
    while (preg_match(
      "/^(([\s\,]*)([^\,]+?)(\s*(\,|$)))(.*)$/", $current, $matches)) 
    {
      list($dummy, $all, $left, $tagName, $right, $dummy, $current) = $matches;
      $tagsInfo[] = array(
        'left' => $left,
        'name' => $tagName,
        'right' => $right
      );
      $tagsAll[] = $all;
    }
    $this->tagSuggestions = array();
    $all = '';
    $n = 0;
    $presentOrSuggested = array();
    foreach ($tagsInfo as $tagInfo) {
      $tag = Doctrine_Query::create()->
        from('Tag t')->
        where('t.name = ?', $tagInfo['name'])->
        fetchOne();
      $all .= $tagInfo['left'];
      if ($tag) {
        $presentOrSuggested[$tagInfo['name']] = true;
      } else {
        // $suggestedTags = sfTagtoolsToolkit::getBeginningWith($tagInfo['name']);
        $suggestedTags = Doctrine_Query::create()->
          from('Tag t')->
          where('t.name LIKE ?', $tagInfo['name'] . '%')->
          limit(sfConfig::get('app_sfDoctrineActAsTaggable_max_suggestions', 10))->
          execute();
        foreach ($suggestedTags as $tag) {
          if (isset($presentOrSuggested[$tag->getName()])) {
            continue;
          }
          // At least some browsers actually submitted the
          // nonbreaking spaces as ordinals distinct from regular spaces,
          // producing distinct tags. So leave the spaces alone.

          // Also, we no longer display 'left' visibly anyway because 
          // that was never compatible with a list of tags that required scrolling

          $suggestion['left'] = $all;
          $suggestion['suggested'] = $tag->getName();
          $presentOrSuggested[$tag->getName()] = true;
          $suggestion['right'] = 
            $tagInfo['right'] . implode('', array_slice($tagsAll, $n + 1));
          $this->tagSuggestions[] = $suggestion;
        }
      }
      $all .= $tagInfo['name'];
      $all .= $tagInfo['right'];
      $n++;
    }
  }
}

