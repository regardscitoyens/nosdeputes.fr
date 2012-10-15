<div>
<?php $abs = false;
if (isset($absolute) && $absolute)
  $abs = true;
if (!isset($limit))
  $limit = 100;
if (!isset($target))
  $target = '';
echo include_component('tag', 'tagcloud', array('tagquery' => $qtag, 'model' => 'Intervention', 'min_tag' => 2, 'parlementaire' => $parlementaire->nom, 'limit' => $limit, 'absolute' => $abs, 'target' => $target)); ?>
</div>
