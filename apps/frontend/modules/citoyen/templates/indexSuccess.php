<?php use_helper('Text') ?>
<h1 class="list_com"><?php echo $title; ?></h1>
<?php $total = $pager->getNbResults();
      $ct = 0; 
      $types = array("date"  => "date d'inscription",
                     "alpha" => "ordre alphabétique",
                     "comm"  => "commentaires postés",
                     "last"  => "derniers commentaires"); ?>
<div class="list_choix">Ordonner par&nbsp;:
<?php foreach($types as $type => $texte) {
  if (!($type === $order)) echo link_to($texte, '@list_citoyens?order='.$type);
  else echo '<strong>'.$texte.'</strong>';
  $ct++;
  if ($ct != count($types)) echo ', ';
} ?>
</div>
<p><?php echo $total; ?> citoyens se sont inscrits sur NosDéputés.fr depuis l'ouverture du site le 14 septembre 2009. <?php echo $comments['auteurs']; ?> d'entre eux ont laissé un total de <?php echo link_to($comments['comments'].'&nbsp;commentaires', '@commentaires'); ?>.<?php if (!$sf_user->isAuthenticated()) echo '<br/>Vous n\'avez pas encore de compte&nbsp;? Cliquez <a href="'.url_for('@inscription').'">ici pour vous inscrire</a> ou <a href="'.url_for('@signin').'">vous connecter</a>.'; ?></p>

<div class="liste">
<?php if ($pager->haveToPaginate()) include_partial('parlementaire/paginate', array('pager'=>$pager, 'link'=>'@list_citoyens?order='.$order.'&')); ?>
<div class="list_table"><table><tr>
<?php $ct = 0;
  foreach($pager->getResults() as $citoyen) {
    $ct++; ?>
    <td><a href="<?php echo url_for('@citoyen?slug='.$citoyen->slug); ?>"><div class="list_cit">
      <div class="list_img_left"><?php
        if (!$citoyen->photo) echo ''.image_tag('xneth/avatar_citoyen.png', array('alt' => 'Avatar par défaut', 'height' => '50px'));
        else echo '<img src="'.url_for('@photo_citoyen?slug='.$citoyen->slug).'" alt="avatar de '.$citoyen->login.'" height="50px"/>';
      ?></div>
      <div class="list_nom">
        <?php echo $citoyen->login; ?>
      </div>
      <div class="list_right">
        <?php echo preg_replace('/membre/', 'inscrit', $citoyen->role).($citoyen->sexe === "F" ? "e" : "").'&nbsp;&nbsp;<br/>le '.myTools::displayVeryShortDate($citoyen->created_at); ?>
      </div>
      <div class="list_left"><?php
        if (!empty($citoyen->activite))
          echo '<i>'.truncate_text(html_entity_decode(strip_tags($citoyen->activite), ENT_NOQUOTES, "UTF-8"), 25).'</i>';
      ?></div>
      <div class="list_right"><?php
        if (!$citoyen->nb_comment)
          echo "0&nbsp;commentaire";
        else {
          echo '<span class="list_com">'.$citoyen->nb_comment.'&nbsp;commentaire';
          if ($citoyen->nb_comment > 1) echo 's';
          echo '</span>';
        }
      ?></div>
    </div></a></td>
    <?php if ($ct % 3 == 0 && $ct != $total) echo '</tr><tr>';
  } ?>
  </td><?php while ($ct % 3 != 0) { $ct++; echo '<td/>'; } ?></tr></table>
</div>
<?php if ($pager->haveToPaginate()) include_partial('parlementaire/paginate', array('pager'=>$pager, 'link'=>'@list_citoyens?order='.$order.'&')); ?>
</div>
