<?php
if (!isset($surtitre)) $surtitre = ''; ?>
<div class="info_depute">
  <div class="depute_gauche">
<?php if ($parlementaire->hasPhoto()) {
    echo '<a href="'.url_for($parlementaire->getPageLink()).'">';
    include_partial('parlementaire/photoParlementaire', array('parlementaire' => $parlementaire, 'height' => 90));
    echo '</a>';
} ?>
  </div>
  <div class="depute_droite"><div>
<h1><?php if ($surtitre) { ?>
    <?php echo $surtitre; ?></h1><br/><h2>
<?php } ?>
  <?php if (isset($deputefirst)) $titre = '<a href="'.url_for($parlementaire->getPageLink()).'">'.$parlementaire->nom.'</a> <br/>'.$titre;
  else {
    $titre .=' ';
    if (preg_match('/^(A|E|É|I|O|U|Y)/', $parlementaire->nom))
      $titre.= "d'";
    else $titre.= 'de ';
    $titre.= '<a href="'.url_for($parlementaire->getPageLink()).'">'.$parlementaire->nom.'</a>';
  }
  if (isset($rss)) $titre .= '<span class="rss"><a href="'.url_for($rss).'"><img src="'.$sf_request->getRelativeUrlRoot().'/images/xneth/rss.png" alt="Les derniers commentaires sur '.$parlementaire->nom.' en RSS"/></a></span>';
echo $titre;
  if ($surtitre) echo '</h2>'; else echo '</h1>'; ?>
  </div></div>
</div>
<div class="stopfloat"></div>
<br/>
