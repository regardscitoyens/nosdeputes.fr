<div class="info_depute">
  <div class="depute_gauche">
<?php if ($parlementaire->hasPhoto()) {
    echo '<a href="'.url_for($parlementaire->getPageLink()).'"><img alt="'.$parlementaire->nom.'" src="'.url_for('@resized_photo_parlementaire?height=90&slug='.$parlementaire->slug).'" /></a>';
} ?>
  </div>
  <div class="depute_droite"><div>
<h1><?php if (isset($surtitre)) { ?>
    <?php echo $surtitre; ?></h1><h2>
<?php } ?>
  <?php echo $titre.' '; if (preg_match('/^(A|E|É|I|O|U|Y)/', $parlementaire->nom)) echo "d'"; else echo 'de '; echo '<a href="'.url_for($parlementaire->getPageLink()).'">'.$parlementaire->nom.'</a>';
  if (isset($surtitre)) echo '</h1>'; else echo '</h2>'; ?>
  </div></div>
</div>
<div class="stopfloat"></div>
<br/>
