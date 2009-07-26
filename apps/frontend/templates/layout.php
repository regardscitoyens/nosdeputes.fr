<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>  
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="screen" href="css/fixe/style.css" />
    <link rel="stylesheet" type="text/css" media="print" href="css/fixe/print.css" />
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" media="screen" href="css/fixe/ie6.css" />
    <script type="text/javascript" src="js/fixe/fonctions_ie.js"></script>
    <script type="text/javascript" src="includes/iepngfix/iepngfix_tilebg.js"></script>  
    <style type="text/css">
      img, div { behavior: url('includes/iepngfix/iepngfix.php') }
    </style>
    <![endif]-->
    <!--[if gt IE 6]>
    <script type="text/javascript" src="js/fixe/fonctions_ie.js"></script>
    <![endif]-->
    <!--[if !IE]><-->
    <script type="text/javascript" src="js/fixe/fonctions.js"></script>
    <!--><![endif]-->
  </head>
  <body>
  <div id="navigation"><a href="#menu">Aller au menu</a> <a href="#contenu">Aller au contenu</a> <a href="#raccourcis"  accesskey="0">Racourcis clavier</a> <a href="#"  accesskey="3">Plan du site</a></div>
    <div class="haut">
      <div class="conteneur_entete">
        <div class="entete">
          <div class="logo">
            <a href="index.php"><?php echo image_tag('fixe/logo.png', 'alt=Légitruc'); ?></a>
          </div>
          <div class="haut_droite">
            <div class="boite_util">
              <div class="b_u_h"><div class="b_u_hg"></div><div class="b_u_hd"></div></div>
              <div class="b_u_cont">
                <div class="bouton_connexion" title="Se connecter"></div>
              </div>
              <div class="b_u_b"><div class="b_u_bg"></div><div class="b_u_bd"></div></div>
            </div>
          </div>
        </div>
      </div>
      <div class="conteneur_menu">
        <div class="menu" id="menu">
          <ul>
            <li class="neuf"><a href="<?php echo url_for('parlementaire/list'); ?>">Accueil</a></li>
            <li class="douze"><a href="<?php echo url_for('parlementaire/list'); ?>">Les Députés</a></li>
            <li class="neuf"><a href="lois.php">Les Lois</a></li>
            <li class="treize"><a href="citoyens.php">Les Citoyens</a></li>
            <li class="douze"><a href="assemblee.php">L'Assemblée</a></li>
          </ul>
          <div class="search_box">
						<form action="<?php echo url_for('@search_parlementaire'); ?>" method="get">
						<p>
							<input class="rechercher" name="search" type="text" size="15" value="rechercher"/>
							<input class="bouton_ok" type="submit" value="ok"/>
						</p>
						</form>
          </div>
        </div>
      </div>
    </div>
    <div class="corps">
      <div class="contenu" id="contenu">
        <?php echo $sf_content ?>
      </div>
    </div>
    <div class="bas">
      <div class="conteneur_menu_suite">
        <div class="menu">
          <div class="mini_logo">
            <a href="#" title="haut de page"><?php echo image_tag('fixe/mini_logo.png', 'alt=LGT'); ?></a>
          </div>
          <!-- <div class="style_switcher">
            <form method="post" action="<?php echo  $_SERVER['PHP_SELF'] ?>" id="select_style">
              <p>Style : 
                <select name="style" onchange="javascript:this.form.submit()">
                  <option value="defaut">par défaut</option>
                  <option value="fixe" selected="selected">fixe</option>
                </select> 
              </p>
              <noscript><p><input type="submit" value="ok" /></p></noscript>
            </form>
          </div> -->
          <div class="float_droite">
            <ul>
              <li class="dixhuit"><a href="#">Qui sommes nous ?</a></li>
              <li class="treize"><a href="#">Plan du site</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>