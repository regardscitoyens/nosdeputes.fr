<?php $style = 'fixe'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>  
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php echo stylesheet_tag($style.'/style'); ?>  
    <?php echo stylesheet_tag($style.'/print'); ?>
    <!--[if lte IE 6]>
    <?php echo stylesheet_tag($style.'/ie6'); ?>
    <script type="text/javascript" src="js/fixe/fonctions_ie.js"></script>
    <script type="text/javascript" src="includes/iepngfix/iepngfix_tilebg.js"></script>  
    <style type="text/css">
      img, div { behavior: url('includes/iepngfix/iepngfix.php') }
    </style>
    <![endif]-->
  </head>
  <body>
  <div id="navigation"><a href="#menu">Aller au menu</a> <a href="#contenu">Aller au contenu</a> <a href="#raccourcis"  accesskey="0">Racourcis clavier</a> <a href="#"  accesskey="3">Plan du site</a></div>
    <div class="haut">
      <div class="conteneur_entete">
        <div class="entete">
          <div class="logo">  
            <a href="<?php echo url_for('@homepage');?>"><?php echo image_tag($style.'/logo2.png', array('alt' => 'NosDéputés.fr')); ?></a>
          </div>
          <div class="haut_droite">
            <div class="boite_util">
              <div class="b_u_h"><div class="b_u_hg"></div><div class="b_u_hd"></div></div>
              <div class="b_u_cont">
                <?php 
                if($sf_user->isAuthenticated()) { 
                  if($sf_user->hasCredential('admin')) { echo '<a href="'.$sf_request->getRelativeUrlRoot().'/backend.php">Backend</a> - '; }
                ?>
                Connecté en tant que <?php echo $sf_user->getAttribute('login'); ?> - <a href="<?php echo url_for('@signout') ?>">Déconnexion</a>
                <?php
                }
                else { ?>
                <a href="<?php echo url_for('@signin') ?>">Connexion</a> - <a href="<?php echo url_for('@inscription') ?>">S'inscrire</a>
                <?php
                }
                ?>
              </div>
              <div class="b_u_b"><div class="b_u_bg"></div><div class="b_u_bd"></div></div>
            </div>
          </div>
        </div>
      </div>
      <div class="conteneur_menu">
        <div class="menu" id="menu">
          <ul>
            <li class="neuf"><a href="<?php echo url_for('@homepage'); ?>">Accueil</a></li>
            <li class="douze"><a href="<?php echo url_for('@list_parlementaires'); ?>">Les Députés</a></li>
            <li class="neuf"><a href="<?php echo url_for('@sections')?>">Les Lois</a></li>
            <li class="treize"><a href="<?php echo url_for('@list_citoyens')?>">Les Citoyens</a></li>
            <li class="douze"><a href="http://www.assemblee-nationale.fr/">L'Assemblée</a></li>
          </ul>
          <div class="search_box">
            <form action="<?php echo url_for('@search'); ?>" method="get">
            <p>
              <input class="rechercher" name="search" type="text" size="15" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>"/>
              <?php
                if (isset($_GET['search']) && preg_match('/parlementaire/', $_SERVER['REQUEST_URI'])) $selectdepute = ' selected="selected"';
                else $selectdepute = "";
                if (isset($_GET['search']) && preg_match('/circonscription/', $_SERVER['REQUEST_URI'])) $selectcirco = ' selected="selected"';
                else $selectcirco = "";
                if (isset($_GET['search']) && preg_match('/profession/', $_SERVER['REQUEST_URI'])) $selectprof = ' selected="selected"';
                else $selectprof = "";
                if (isset($_GET['search']) && preg_match('/intervention/', $_SERVER['REQUEST_URI'])) $selectinterv = ' selected="selected"';
                else $selectinterv = "";
                if (isset($_GET['search']) && preg_match('/amendement/', $_SERVER['REQUEST_URI'])) $selectamdmt = ' selected="selected"';
                else $selectamdmt = "";
              ?>
              <select class="type_recherche" name="type">
                <option value="depute"<?php echo $selectdepute; ?>>Députés</option>
                <option value="departement"<?php echo $selectcirco; ?>>Départements</option>
                <option value="profession"<?php echo $selectprof; ?>>Profession</option>
                <option value="intervention"<?php echo $selectinterv; ?>>Interventions</option>
                <option value="amendement"<?php echo $selectamdmt; ?>>Amendements</option>
              </select>
              <input class="bouton_ok" type="submit" value="ok"/>
            </p>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="corps">
      <div class="contenu" id="contenu">
<?php if ($sf_user->hasFlash('notice')) :?>
<p class='flash_notice'><?php echo $sf_user->getFlash('notice'); ?></p>
<?php endif;?>
<?php if ($sf_user->hasFlash('error')) :?>
<p class='flash_error'><?php echo $sf_user->getFlash('error'); ?></p>
<?php endif;?>
        <?php echo $sf_content ?>
      </div>
    </div>
    <div class="bas">
      <div class="conteneur_menu_suite">
        <div class="menu2">
          <div class="up">
            <a href="#" title="haut de page"><?php echo image_tag($style.'/mini_logo.png', 'alt=LGT'); ?></a>
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
          <div class="menu_bas">
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