<?php
/*
 * This file has been copied from the sfPropelActAsTaggableBehavior package.
 *
 * (c) 2007 Xavier Lacot <xavier@lacot.org>
 * (c) 2007 Michael Nolan
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TaggableToolkit
{
    /**
    * "Cleans" a string in order it to be used as a tag. Intended for strings
    * representing a single tag
    *
    * @param      String    $tag
    * @return     bool
    */
    public static function cleanTagName($tag, $options = array())
    {
        $tag = trim(rtrim(str_replace(',', ' ', $tag)));
        
        if(isset($options['case']))
        {
          $tag = call_user_func($options['case'], $tag);
        }
        return $tag;
    }

    /**
    * "Cleans" a string in order it to be used as a tag
    * Intended for strings representing a single tag
    *
    * @param      mixed     $tag
    * @return     mixed -> array or the $tag passed as reference...
    */
    public static function explodeTagString($tag, $options = array())
    {
        if (is_string($tag)
            && (false !== strpos($tag, ',') || preg_match('/\n/', $tag)))
        {
          $tag = preg_replace('/\r?\n/', ',', $tag);
          $tag = explode(',', $tag);
          $tag = array_map('trim', $tag);
          $tag = array_map('rtrim', $tag);
        }

        return $tag;
    }

    /**
    * Extract triple tag values from tag.  Returned array will contain four
    * elements: tagname (same as input), namespace, key and value.
    *
    * @param      string     $tag
    * @return     array
    */
    public static function extractTriple($tag)
    {
        $match = preg_match('/^([A-Za-z][A-Za-z0-9_]*):([A-Za-z][A-Za-z0-9_]*)=(.*)$/', $tag, $triple);

        if ($match)
        {
          return $triple;
        }
        else
        {
          return array($tag, null, null, null);
        }
    }

    /**
    * Formats a tag string/array in a pretty string. For instance, will convert
    * tag3,tag1,tag2 into the following string : "tag1", "tag2" and "tag3"
    *
    * @param      array/string    $tags
    * @return     String
    */
    public static function formatTagString($tags)
    {
        $result = '';
        // FIXME: if using sfContext testcase fails
        //$sf_i18n = sfContext::getInstance()->getI18n();

        if (is_string($tags))
        {
            $tags = explode(',', $tags);
        }

        $nb_tags = count($tags);

        if ($nb_tags > 0)
        {
            sort($tags, SORT_LOCALE_STRING);
            $i = 0;

            foreach ($tags as $tag)
            {
                $result .= '"'.$tag.'"';
                $i++;

                if ($i == $nb_tags - 1)
                {
                    //$result .= ' '.$sf_i18n->__('and').' ';
                    $result .= ' and ';
                }
                elseif ($i < $nb_tags)
                {
                    $result .= ', ';
                }
            }
        }

        return $result;
    }

    /**
    * Returns true if the passed model name is taggable
    *
    * @param      mixed     $model
    * @return     boolean
    */
    public static function isTaggable($model)
    {
        if(!Doctrine::isValidModelClass($model))
        {
            throw new Exception(sprintf('%s is not a doctrine class...', $model));
        }
        
        if(is_string($model))
        {
            $table = Doctrine::getTable($model);
        }
        
        if(is_object($model))
        {
            $table = $model->getTable();
        }
        
        return $table->hasTemplate('Taggable');
    }

    /**
    * Normalizes a tag cloud, ie. changes a (tag => weight) array into a
    * (tag => normalized_weight) one. Normalized weights range from -2 to 2.
    *
    * @param      array  $tag_cloud
    * @return     array
    */
    public static function normalize($tag_cloud)
    {
        $tags = array();
        $levels = 5;
        $power = 0.7;

        if (count($tag_cloud) > 0)
        {
            $max_count = max($tag_cloud);
            $min_count = min($tag_cloud);
            $max = intval($levels / 2);

            if ($max_count != 0)
            {
                foreach ($tag_cloud as $tag => $count)
                {
                    $tags[$tag] = round(.9999 * $levels * (pow($count/$max_count, $power) - .5), 0);
                }
            }
        }

        return $tags;
    }
    
    /**
	   * Transform every $tagname in $tags to namespace:key=$tagname:
	   * array_walk($tags, 'TaggableToolkit::triplify', 'namespace:key');
     * 
     * @param String $tagname
     * @param String $array_key
     * @param String $ns_key
     * @return String
     */
    public static function triplify(&$tagname, $array_key, $ns_key) {
      $tagname = trim(rtrim($tagname));
      $tagname = $ns_key.'='.$tagname;
      return $tagname;
    }
}
