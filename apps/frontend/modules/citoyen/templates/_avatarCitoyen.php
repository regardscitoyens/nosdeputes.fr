<?php
if (!$user or !$user->photo) return;
echo '<img src="'.url_for('@photo_citoyen?slug='.$user->slug).'" alt="avatar" />';
