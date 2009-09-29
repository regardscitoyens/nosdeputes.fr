<?php
if (!isset($surtitre)) $surtitre = ''; ?>
<div class="info_depute">
  <div class="depute_gauche">
<?php if ($parlementaire->hasPhoto()) {
    echo '<a href="'.url_for($parlementaire->getPageLink()).'"><img alt="'.$parlementaire->nom.'" src="'.url_for('@resized_photo_parlementaire?height=90&slug='.$parlementaire->slug).'" /></a>';
} ?>
  </div>
  <div class="depute_droite"><div>
<h1><?php if ($surtitre) { ?>
    <?php echo $surtitre; ?></h1><h2>
<?php } ?>
  <?php if (isset($deputefirst)) $titre = $parlementaire->nom.' <br/>'.$titre;
  else {
    $titre .=' ';
    if (preg_match('/^(A|E|É|I|O|U|Y)/', $parlementaire->nom))
      $titre.= "d'";
    else $titre.= 'de ';
    $titre.= '<a href="'.url_for($parlementaire->getPageLink()).'">'.$parlementaire->nom.'</a>';
  }
echo $titre;
  if ($surtitre) echo '</h2>'; else echo '</h1>'; ?>
  </div></div>
</div>
<div class="stopfloat"></div>
<br/>