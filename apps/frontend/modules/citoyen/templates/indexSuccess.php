<?php use_helper('Text') ?>
<h1 class="list_com"><?php echo $title; ?></h1>
<?php $total = $pager->getNbResults();
      $ct = 0; 
      $types = array("date"  => "date d'inscription",
                     "alpha" => "ordre alphabétique",
                     "comm"  => "commentaires postés"); ?>
<div class="list_choix">Ordonner par&nbsp;:
<?php foreach($types as $type => $texte) {
  if (!($type === $order)) echo link_to($texte, '@list_citoyens?order='.$type);
  else echo '<strong>'.$texte.'</strong>';
  $ct++;
  if ($ct != count($types)) echo ', ';
} 
echo ', '.link_to('derniers commentaires', '@commentaires');
?>
  
</div>
<p><?php echo $total; ?> citoyens se sont inscrits sur NosDéputés.fr depuis l'ouverture du site le 14 septembre 2009. <?php echo $comments['auteurs']; ?> d'entre eux ont laissé un total de <?php echo link_to($comments['comments'].'&nbsp;commentaires', '@commentaires'); ?>.</p>
<p><?php if (!$sf_user->isAuthenticated()) echo 'Vous n\'avez pas encore de compte&nbsp;? Cliquez ici pour <strong><a href="'.url_for('@inscription').'">vous inscrire</a></strong> ou <strong><a href="'.url_for('@signin').'">vous connecter'; else echo '<strong><a href="'.url_for('@citoyen?slug='.$sf_user->getAttribute('slug')).'">Voir votre compte'; ?></a></strong>.</p>

<div class="liste">
<?php if ($pager->haveToPaginate()) include_partial('parlementaire/paginate', array('pager'=>$pager, 'link'=>'@list_citoyens?order='.$order.'&')); ?>
<div class="list_table"><table summary="Liste des citoyens inscrits"><tr>
<?php $ct = 0;
  foreach($pager->getResults() as $citoyen) {
    $ct++;
    if ($ct % 3 == 1 && $ct != 1) echo '</tr><tr>'; ?>
<td onclick="document.location='<?php echo url_for('@citoyen?slug='.$citoyen->slug); ?>'">
<div class="list_cit"><span class="list_img_left"><a href="<?php echo url_for('@citoyen?slug='.$citoyen->slug); ?>"><?php
if (!$citoyen->photo) echo ''.image_tag('xneth/avatar_citoyen.png', array('alt' => 'Avatar par défaut'));
else echo '<img src="'.url_for('@photo_citoyen?slug='.$citoyen->slug).'" alt="avatar de '.$citoyen->login.'"/>';
?></a></span>
<span class="list_nom"><a href="<?php echo url_for('@citoyen?slug='.$citoyen->slug); ?>">
<?php echo truncate_text($citoyen->login, 26);
if (!empty($citoyen->activite))
  echo '</a><br/><small><i><a href="'.url_for('@citoyen?slug='.$citoyen->slug).'">'.truncate_text(html_entity_decode(strip_tags($citoyen->activite), ENT_NOQUOTES, "UTF-8"), 25).'</a></i></small>';
else echo '</a>';?>
</span>
<span class="list_right"><a href="<?php echo url_for('@citoyen?slug='.$citoyen->slug); ?>">
<?php echo preg_replace('/membre/', 'inscrit', $citoyen->role).($citoyen->sexe === "F" ? "e" : "").'&nbsp;&nbsp;<br/>le '.myTools::displayVeryShortDate($citoyen->created_at); ?>
</a><br/>
<a href="<?php echo url_for('@citoyen?slug='.$citoyen->slug); ?>"><?php
if (!$citoyen->nb_comment)
  echo "0&nbsp;commentaire";
else {
  echo '<strong><span class="list_com">'.$citoyen->nb_comment.'&nbsp;commentaire';
  if ($citoyen->nb_comment > 1) echo 's';
  echo '</span></strong>';
}
?></a></span></div>
</td>
<?php } ?>
<?php while ($ct % 3 != 0) { $ct++; echo '<td/>'; } ?></tr></table>
</div>
<?php if ($pager->haveToPaginate()) include_partial('parlementaire/paginate', array('pager'=>$pager, 'link'=>'@list_citoyens?order='.$order.'&')); ?>
</div>
