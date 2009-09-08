<?php
if (!$user) {
  echo 'Anonyme';
  return ;
 }
echo '<a href="'.url_for('@citoyen?slug='.$user->slug).'">'.$user->login.'</a>';
