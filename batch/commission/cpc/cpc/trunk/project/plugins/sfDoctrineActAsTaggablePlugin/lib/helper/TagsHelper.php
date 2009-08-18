<?php
/*
 * This file is part of the sfPropelActAsTaggableBehavior package.
 *
 * (c) 2007 Xavier Lacot <xavier@lacot.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generates a tag cloud from a tags array.
 *
 * @param   array   $tags     The tags array. This array must associate the tags
 * to their weight. The metgod TaggableToolkit::normalize() is here
 * to help generate such an array
 *
 * @param   mixed   $route    The route name to be used in the tag. If using the&
 * link_to_remote function, this parameter acts as the remote options array
 *
 * @param   array   $options  Optionnal options array for formating the link.
 * Supported keys are:
 *  * class:  html class to be applied to the tags list
 *  * link_function: custom function to be used for generating each tag link
 *  * link_options: html options to be applied to the generated link
 */
function tag_cloud($tags, $route, $options = array())
{
  $result = '';

  if(count($tags) > 0)
  {
    $emphasizers_begin = array(-2 => '<small><small>',
                               -1 => '<small>',
                                0 => '',
                                1 => '<big>',
                                2 => '<big><big>');
    $emphasizers_end = array(-2 => '</small></small>',
                             -1 => '</small>',
                              0 => '',
                              1 => '</big>',
                              2 => '</big></big>');

    $custom_route = is_string($route) && false !== strpos($route, '%s');

    if (!$custom_route)
    {
      $route_link = $route;
    }

    $class = isset($options['class']) ? $options['class'] : 'tag-cloud';
    $link_function = isset($options['link_function']) ? $options['link_function'] : 'link_to';
    $link_options = isset($options['link_options']) ? $options['link_options'] : array();
    $link_options['rel'] = 'tag';
    $result = '<ul class="'.$class.'">';

    foreach ($tags as $name => $count)
    {
      if ($custom_route)
      {
        $route_link = sprintf($route, $name);
      }

      $link = $link_function($name, $route_link, $link_options);
      $result .= '
                  <li>'.$emphasizers_begin[$count].$link.$emphasizers_end[$count].'</li>';
    }

    $result .= '</ul>';
  }

  return $result;
}

function tag_list($tags, $route, $options = array())
{
  $result = '';

  if (count($tags) > 0)
  {
    $class = isset($options['class']) ? $options['class'] : 'tags-list';

    if (isset($options['ordered']))
    {
      $result = '<ol class="'.$class.'">';
    }
    else
    {
      $result = '<ul class="'.$class.'">';
    }

    $i = 1;

    foreach ($tags as $tag)
    {
      $link = link_to($tag,
                      $route.$tag,
                      array('rel' => 'tag'));

      $result .= '
                  <li>'.$link;
      if (isset($options['separator']) && ($i != count($tags)))
      {
        $result .= $options['separator'];
      }

      $result .= '</li>';
      $i++;
    }

    if (isset($options['ordered']))
    {
      $result .= '</ol>';
    }
    else
    {
      $result .= '</ul>';
    }
  }

  return $result;
}

function related_tag_cloud($tags, $route, $tag, $options = array())
{
  $result = '';

  if(count($tags) > 0)
  {
    if (is_array($tag))
    {
      $tag = implode(',', $tag);
    }

    $emphasizers_begin = array(-2 => '<small><small>',
                               -1 => '<small>',
                                0 => '',
                                1 => '<big>',
                                2 => '<big><big>');
    $emphasizers_end = array(-2 => '</small></small>',
                             -1 => '</small>',
                              0 => '',
                              1 => '</big>',
                              2 => '</big></big>');

    $add = isset($options['add']) ? $options['add'] : '(+)';
    $class = isset($options['class']) ? $options['class'] : 'tag-cloud';
    $result = '<ul class="'.$class.'">';

    foreach ($tags as $name => $count)
    {
      $link = link_to($name,
                      $route.$name,
                      array('rel' => 'tag'));
      $related_link = link_to($add,
                              $route.$tag.','.$name);

      $result .= '
                  <li>'.$emphasizers_begin[$count]
                  .$link.'&nbsp;'
                  .$related_link.$emphasizers_end[$count]
                  .'</li>';
    }

    $result .= '</ul>';
  }

  return $result;
}
