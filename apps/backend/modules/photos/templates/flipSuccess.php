<form action="<?php echo url_for('@flip'); ?>" method="post">
<div style="clear:both;text-align:center;"><input type="submit" value="Flip" /></div>
<div style="clear:both; margin:4px;">
<?php $ct = 0;
foreach ($parlementaires as $parlementaire) if ($parlementaire->slug) { $ct++ ?>
  <div class="photo">
    <?php if ($parlementaire->fin_mandat != null && $parlementaire->fin_mandat >= $parlementaire->debut_mandat)
      $groupe = "ancien".($parlementaire->sexe == "F" ? "ne" : "")." sénat".($parlementaire->sexe == "F" ? "rice" : "eur");
    else $groupe = "Groupe parlementaire : ".$parlementaire->groupe_acronyme;
    echo '<a href="/'.$parlementaire->slug.'" target="_blank"><img title="'.$parlementaire->nom.' -- ('.$groupe.')" src="/depute/photo/'.$parlementaire->slug.'/100?_sf_ignore_cache='.rand().'" class="jstitle photo_fiche" alt="Photo de '.$parlementaire->nom.'"/></a>'; ?>
    <br/><input type="checkbox" name="flip<?php echo $parlementaire->id; ?>" value="<?php echo $parlementaire->id; ?>" /><?php echo $parlementaire->id; 
    if ($ct % 50 == 0)
      echo '</div><div style="clear:both;text-align:center;"><input type="submit" value="Flip" />&nbsp;'.$ct.'</div><div class="photo">'; ?>
  </div>
<?php } ?>
<div style="clear:both;text-align:center;"><input type="submit" value="Flip" /></div>
</form>
</div>
