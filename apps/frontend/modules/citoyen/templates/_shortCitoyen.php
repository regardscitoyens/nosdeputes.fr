<?php
if (!$user) {
  echo 'Anonyme';
  return ; 
 }
if (!isset($nolink)) echo '<a href="'.url_for('@citoyen?slug='.$user->slug).'">';
echo $user->login;
if (isset($user->activite) && $user->activite != "") echo ' ('.$user->activite.')';
if (!isset($nolink)) echo '</a>';
