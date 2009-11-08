<?php
if ($user) {
  echo '<a href="'.url_for('@citoyen?slug='.$user->slug).'">';
  if (!$user->photo)
    echo image_tag('xneth/avatar_citoyen.png', array('alt' => 'Avatar par défaut'));
  else echo '<img src="'.url_for('@photo_citoyen?slug='.$user->slug).'" alt="avatar" />';
  echo '</a>';
} ?>
