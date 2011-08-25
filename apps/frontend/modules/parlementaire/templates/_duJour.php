<h2><?php if ($parlementaire->sexe == 'F') echo 'La sénatrice du jour'; else echo 'Le sénateur du jour'; ?></h2>
<p><a href="<?php echo url_for('@parlementaire?slug='.$parlementaire->slug); ?>">
   <?php   include_partial('parlementaire/photoParlementaire', array('parlementaire' => $parlementaire, 'height' => 150, 'flip' => 1)); ?>
</a></p>
<h3><?php echo link_to($parlementaire->nom, '@parlementaire?slug='.$parlementaire->slug); ?></h3>
<p><?php echo link_to('Un autre sénateur au hasard', '@parlementaire_random'); ?></p>
