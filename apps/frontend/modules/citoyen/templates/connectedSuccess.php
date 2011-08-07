<?php if(!$sf_user->isAuthenticated()) { ?>
    <form method="post" id="form_header_login" action="<?php echo url_for('@signin'); ?>">
      <p>
      <input type="text" name="signin[login]" id='header_login' class="examplevalue" value="Identifiant" />
      <input type="password" name="signin[password]" id='header_pass' value="______________" class="examplevalue"/>
      <input type="checkbox" name="signin[remember]" id="header_remember" title="se rappeler de moi" />
      <button type="submit" value="login" id="bt1"></button>
      <a href="<?php echo url_for('@inscription') ?>"><span id="bt2"></span></a>
      </p>
      </form>
      <?php } else
	{ 
	  echo '<div style="padding-top: 7px;"><span id="loggued_top">';
	  if($sf_user->getAttribute('is_active') == true) { 
	    echo link_to($sf_user->getAttribute('login'),'@citoyen?slug='.$sf_user->getAttribute('slug')).' - ';
	  } else {
            echo $sf_user->getAttribute('login').' (e-mail non-validé) - ';
          }
	  echo link_to('Déconnexion','@signout'); echo '</span></div>';
	}
