<div class="bas">
      <div class="conteneur_menu_suite">
        <div class="menu">
          <div class="mini_logo">
            <a href="#" title="haut de page"><img alt="LGT" src="css/<?php echo $style; ?>/images/mini_logo.png" /></a>
          </div>
          <div class="style_switcher">
            <form method="post" action="<?php echo  $_SERVER['PHP_SELF'] ?>" id="select_style">
              <p>Style : 
                <select name="style" onchange="javascript:this.form.submit()">
                  <?php 
                  foreach ($styles as $option) { echo '<option value="'.$option.'" '; if ($style === $option) { echo 'selected="selected"'; } echo '>'.$option.'</option>';  }
                  ?>
                </select> 
              </p>
              <noscript><p><button class="bouton_ok">ok</button></p></noscript>
            </form>
          </div>
          <div class="float_droite">
            <ul>
              <li class="dixhuit"><a href="#">Qui sommes nous ?</a></li>
              <li class="treize"><a href="#">Plan du site</a></li>
            </ul>
          </div>
        </div>
      </div>
			<div class="stopfloat"></div>
    </div>
