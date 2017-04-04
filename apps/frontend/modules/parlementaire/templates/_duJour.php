<h4 class="deputes"><?php if ($parlementaire->sexe == 'F') echo 'La députée du jour'; else echo 'Le député du jour'; ?></h4>
<div class="text-center">
  <p><a href="<?php echo url_for('@parlementaire?slug='.$parlementaire->slug); ?>">
     <?php   include_partial('parlementaire/photoParlementaire', array('parlementaire' => $parlementaire, 'height' => 150, 'flip' => 1)); ?>
  </a></p>
  <p><strong><?php echo link_to($parlementaire->nom, '@parlementaire?slug='.$parlementaire->slug); ?></strong></p>
  <p><?php echo link_to('Un autre député au hasard', '@parlementaire_random', array('class' => 'button secondary expanded')) ?></p>
</div>
