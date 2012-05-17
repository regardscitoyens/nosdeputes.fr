<?php if (!$parl) {
 echo "<p>$depute</p>";
 return;
}
$url = url_for('@parlementaire?slug='.$parl->slug); ?>
<style type="text/css">
 .nosdeputes_widget { width: 935px; text-align: center; font-size: 11px; }
 .nosdeputes_widget a { text-decoration: none; color: inherit; }
 .nosdeputes_widget #overDiv { position: absolute; visibility: hidden; z-index: 1000; }
 .nosdeputes_widget .graph_depute { float: left; height: 170px; margin-right: 3px; width: 800px; }
 .nosdeputes_widget .graph_depute p { margin: 0; padding: 0; }
 .nosdeputes_widget .barre_activite { text-align: center; background-color: #BFBFBF; float: left; margin-top: 10px; padding-top: 3px; width: 920px; text-align: left; }
 .nosdeputes_widget .barre_activite ul, .barre_activite li { background-color: transparent; display: inline; font-weight: bold; line-height: 24px; margin: -3px; padding: 0; }
 .nosdeputes_widget .barre_activite h3 { background-color: transparent; color: #6F6F6F; display: inline; font-size: 1.3em; line-height: 24px; margin: 0; padding-left: 5px; }
 .nosdeputes_widget .barre_activite li img { margin-left: 25px; }
</style>
<div class="nosdeputes_widget">
 <?php if ($options['titre']) : ?>
  <div style="text-align:center;">
   <h2><a href="<?php echo $url; ?>"><?php echo $parl->nom; ?>, <?php echo $parl->getLongStatut(1); ?></h2>
  </div>
 <?php endif;
 if ($options['photo']) : ?>
  <div style="float: left; border: 2px solid #DCD6CA; height: 160px; width: 125px;">
   <a href="<?php echo $url; ?>"><?php include_partial('parlementaire/photoParlementaire', array('parlementaire' => $parl, 'height' => 160)); ?></a>
  </div>
 <?php endif;
 if ($options['graphe']) : ?>
  <div class="graph_depute">
   <?php echo include_component('plot', 'parlementaire', array('parlementaire' => $parl, 'options' => array('plot' => 'total', 'questions' => 'true', 'link' => 'true'))); ?>
  </div>
  <div style="clear: both;"></div>
 <?php endif;
 if ($options['activite']) : ?>
  <div class="barre_activite">
   <?php include_partial('top', array('parlementaire'=>$parl)); ?>
  </div>
  <div style="clear: both;"></div>
 <?php endif; ?>
</div>
