<?php use_helper('Text') ?>
<div id="commentaire_<?php echo $c->id; ?>" class="commentaire">
<p><span class="titre_commentaire"><?php $pres = ""; if (isset($presentation)) $pres .= $c->getPresentation($presentation, 1); if (isset($presentation) && $presentation == 'noarticle' && preg_match('/(amendement n°)(\d.*),\s$/', $pres, $match)) $pres = preg_replace('/'.$match[1].$match[2].'/', link_to($match[1].$match[2], '@find_amendements_by_loi_and_numero?loi=2271&numero='.preg_replace('/premier/i', '1er', preg_replace('/\s+/', '-', $match[2]))), $pres); echo $pres; if ($pres != '') echo 'le'; else echo 'Le'; ?> <a href="#commentaire_<?php echo $c->id;?>"><?php echo myTools::displayDateTime($c->created_at); ?></a>, <?php
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id));
?> a dit :</span></p>
<div class="clear"></div>
<div class="commentaire_avatar"><?php include_component('citoyen','avatarCitoyen', array('citoyen_id'=>$c->citoyen_id)); ?></div>
<p><?php echo myTools::escape_blanks($c->commentaire); ?></p>
<?php if(!$c->is_public)
  echo "<p><strong>Attention, ce commentaire est en attente de validation par email. Les autres utilisateurs ne peuvent pas le voir.</strong></p>";
?>
<p class="clear" align="right">Vous trouvez ce commentaire constructif&nbsp;: <?php include_component('rate', 'show', array('object' => $c)); ?> </p>
</div>
