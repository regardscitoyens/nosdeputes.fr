<h3><?php if ($p['sexe'] == 'F') echo 'La députée du jour'; else echo 'Le député du jour'; ?></h3>
<p><a href="<?php echo url_for('@parlementaire?slug='.$p['slug']); ?>"><img src="<?php echo url_for('@flip_resized_photo_parlementaire?height=150&slug='.$p['slug']); ?>" alt="<?php echo $p['nom']; ?>" /></a><br/>
<?php echo link_to($p['nom'], '@parlementaire?slug='.$p['slug']); ?><br/>
<?php echo link_to('Un autre député au hasard', '@parlementaire_random'); ?></p>
