<?php if (!$parl) {
 return;
}
$target = '';
if ($options['iframe'])
  $target = ' target="_blank"';
if (!$options['photo'])
  $widthrate = $options['width']/800.;
else $widthrate = $options['width']/935.;
$url = url_for('@parlementaire?slug='.$parl->slug, 'absolute=true'); ?>
<style type="text/css">
 .nosdeputes_widget { width: <?php echo $options['width']; ?>px; text-align: center; font-size: <?php echo max(8, floor(15*$options['width']/935)); ?>px; }
 .nosdeputes_widget a { text-decoration: none; color: inherit; }
 .nosdeputes_widget .clearBoth { clear: both; }
<?php if ($options['photo']) :
  if (!$options['graphe']) {
    $photow = $options['width']-2;
    $photoh = floor(160/125*$photow);
  } else {
    $photoh = floor(160*($options['width']-4)/935);
    $photow = floor(125*($options['width']-4)/935);
  } ?>
 .nosdeputes_widget .photo_depute { <?php echo ($options['graphe'] ? 'float: left; ' : ''); ?>border: <?php echo floor(2*$options['width']/935); ?>px solid #DCD6CA; height: <?php echo $photoh; ?>px; width: <?php echo $photow; ?>px; }
<?php endif;
if ($options['graphe']) : ?>
 .nosdeputes_widget .graph_depute { float: left; height: <?php echo floor(160*$widthrate); ?>px; width: <?php echo floor(800*$widthrate); ?>px; margin: auto; <?php echo ($widthrate > 1/3 ? 'margin-bottom: 10px;' : ''); ?>}
 .nosdeputes_widget .graph_depute p { font-size: <?php echo floor(12*$options['width']/935); ?>px; margin: 0; padding: 0; }
<?php endif;
if ($options['activite']) : ?>
 .nosdeputes_widget .barre_activite { font-size: <?php echo floor(12*$options['width']/935); ?>px; background-color: #EBEBEB; float: left; margin-top: <?php echo floor(10*$options['width']/935); ?>px; padding-top: <?php echo floor(3*$options['width']/935); ?>px; width: <?php echo floor($options['width']); ?>px; text-align: left; }
 .nosdeputes_widget .barre_activite ul, .barre_activite li { background-color: transparent; display: inline; font-weight: bold; line-height: <?php echo floor(24*$options['width']/935); ?>px; margin: -<?php echo floor(3*$options['width']/935); ?>px; padding: 0; }
 .nosdeputes_widget .barre_activite h3 { background-color: transparent; color: #6B6B6B; display: inline; font-size: 1.2em; line-height: <?php echo floor(24*$options['width']/935); ?>px; margin: 0; padding-left: <?php echo floor(5*$options['width']/935); ?>px; }
 .nosdeputes_widget .barre_activite li img { margin-left: <?php echo floor(25*$options['width']/935); ?>px; }
<?php endif;
if ($options['tags']) : ?>
 .nosdeputes_widget .tags_depute { text-align: justify; border: 2px solid #EBEBEB; }
 .nosdeputes_widget .internal_tag_cloud { margin: <?php echo floor(2*$options['width']/935); ?>px; position: relative; text-align: center; font-size: 12px; }
 .nosdeputes_widget .tag_level_0 { font-size: 0.5em; }
 .nosdeputes_widget .tag_level_1 { font-size: 0.7em; }
 .nosdeputes_widget .tag_level_2 { font-size: 0.9em; }
 .nosdeputes_widget .tag_level_3 { font-size: 1.1em; }
 .nosdeputes_widget .tag_level_4 { font-size: 1.3em; }
<?php endif; ?>
</style>
<div class="nosdeputes_widget">
<?php if ($options['titre']) : ?>
  <h2><a<?php echo $target; ?> href="<?php echo $url; ?>"><?php echo $parl->nom; ?><?php if ($options['width'] >= 580) echo ', '.$parl->getLongStatut(); else if ($options['width'] >= 280) echo ', '.$parl->getStatut(); ?></a></h2>
<?php endif;
if ($options['photo']) : ?>
  <div class="photo_depute">
    <a<?php echo $target; ?> href="<?php echo $url; ?>"><?php include_partial('photoParlementaire', array('parlementaire' => $parl, 'height' => $photoh, 'absolute' => true)); ?></a>
  </div>
<?php endif;
if ($options['graphe']) :
  if (!$options['photo'])
    $widthrate = $options['width']/800.;
  else $widthrate = $options['width']/935.; ?>
  <div class="graph_depute">
    <?php echo include_component('plot', 'parlementaire', array('parlementaire' => $parl, 'options' => array('plot' => 'total', 'questions' => 'true', 'link' => 'true', 'absolute' => true, 'widthrate' => $widthrate, 'target' => $target))); ?>
  </div>
  <div class="clearBoth"></div>
<?php endif;
if ($options['activite']) : ?>
  <div class="barre_activite">
    <?php include_partial('top', array('parlementaire'=>$parl, 'absolute' => true, 'widthrate' => $options['width']/935., 'target' => $target)); ?>
  </div>
  <div class="clearBoth"></div>
<?php endif;
if ($options['tags']) : ?>
  <div class="tags_depute">
<?php echo include_component('tag', 'parlementaire', array('parlementaire'=>$parl, 'absolute' => true, 'limit' => $options['maxtags'], 'target' => $target)); ?>
  </div>
<?php endif; ?>
</div>

