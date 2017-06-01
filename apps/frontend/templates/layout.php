<?php $style = 'xneth'; ?>
<!doctype html>
<html class="no-js" lang="fr">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    
    <!-- this should be placed in metas but, from what I understand, current symphonie doesn't write this html5 meta elements ... -->
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Facebook metas -->
    <meta property="og:type" content="website" />
    <meta property="og:title" content="NosDéputés.fr - Regards Citoyens" />
    <meta property="og:site_name" content="NosDéputés.fr" />
    <meta property="og:description" content="Observatoire citoyen de l'activité parlementaire à l'Assemblée nationale" />
    <meta property="og:url" content="http://www.NosDéputés.fr" />
    <meta property="og:locale" content="fr_FR" />
    <meta property="og:image" content="http://www.regardscitoyens.org/wp-content/uploads/2009/10/logo_nosdeputes.png" />
    <meta property="og:image:type" content="image/png" />

    <?php include_title() ?>
<?php
    $rss = $sf_request->getParameter('rss');
if ($rss) {
  foreach($rss as $r) {
    echo '<link rel="alternate" type="application/rss+xml" title="'.$r['title'].'" href="'.url_for($r['link']).'"/>';
  }
 }
$uri = strip_tags($_SERVER['REQUEST_URI']);
$selectdepute = "";$selectcirco = "";$selectprof = ""; $selectinterv = "";$selectamdmt = "";$selectquestion = ""; $selectcitoyen = '';
if ( preg_match('/\/circonscription[\/\?]/', $uri))
  $selectcirco = ' selected="selected"';
 else  if ( preg_match('/\/profession[\/\?]/', $uri))
   $selectprof = ' selected="selected"';
 else if ( preg_match('/\/(interventions?|seance|dossiers?)[\/\?]/',$uri))
   $selectinterv = ' selected="selected"';
 else if ( preg_match('/\/amendements?[\/\?]/', $uri))
   $selectamdmt = ' selected="selected"';
 else if ( preg_match('/\/question[\/\?]/', $uri))
   $selectquestion = ' selected="selected"';
 else if (preg_match('/(\/citoyens?[\/\?]?|\/compterendu|\/commentaires?)/', $uri))
   $selectcitoyen = 1;
 else if ( !preg_match('/\/(faq|$)/i', $uri))
   $selectdepute = ' selected="selected"';

$menu_depute = $selectquestion || $selectdepute || $selectprof || $selectcirco;
$menu_dossier = $selectinterv || $selectamdmt;
$menu_citoyen = $selectcitoyen;
?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="search" href="<?php echo $sf_request->getRelativeUrlRoot(); ?>/nosdeputesfr.xml" title="Rechercher sur NosDéputés.fr" type="application/opensearchdescription+xml" />
    <?php echo stylesheet_tag($style.'/jquery-ui-1.8.5.custom.css'); ?>
    
    <?php echo stylesheet_tag($style.'/style.css'); ?>
    <?php echo stylesheet_tag($style.'/print', array('media' => 'print')); ?>
    
    <!-- New CSS with Foundation -->
    <?php echo stylesheet_tag($style.'/app.css'); ?>
    <?php echo stylesheet_tag($style.'/fonts/foundation-icons/foundation-icons.css'); ?>
    
    <!--[if lte IE 6]>
      <?php echo stylesheet_tag($style.'/ie6'); ?>
      <script type="text/javascript" src="<?php echo $sf_request->getRelativeUrlRoot(); ?>/iepngfix/iepngfix_tilebg.js"></script>
      <style type="text/css">
        img, div { behavior: url('<?php echo $sf_request->getRelativeUrlRoot(); ?>/iepngfix/iepngfix.php') }
      </style>
    <![endif]-->
    <?php include_partial('parlementaire/cssCouleursGroupes'); ?>
  </head>
  <body>
  
  <!--
  <section id="login-bar">
    <div class="row columns">
      <ul class="menu">
        <li id="initiative"><a href="http://www.regardscitoyens.org/" onclick="return(window.open(this.href)?false:true);">
          <?php echo image_tag($style.'/new_regards-citoyens_small.png', array('alt' => 'Regards Citoyens')); ?> Une initiative de RegardsCitoyens.org
        </a></li>
      </ul>
      <div id="connected" class="identification">
        <p id="loggued_top">
          <a href="/login">Se connecter</a> -
          <a href="/login">Mon compte</a>
        </p>
      </div>-->
        <script type="text/javascript"><!--
        $('#connected').load("<?php echo url_for('@identification_ajax'); ?>");
      --></script><!--
    </div>
  </section> -->
  
<!--  <nav>  
        <ul class="menu">
          <li class="topbar-title"><a href="<?php echo url_for('@homepage'); ?>" title="Accueil"><?php echo image_tag($style.'/new_nosdeputes_menu', array('alt' => 'Nos Députés')); ?> NosDéputés.FR</a></li>
        </ul>
      
        <ul role="menubar" class="dropdown menu" data-dropdown-menu="dropdown-menu" data-click-open="false">
          <li class="deputes-bleu has-dropdown <?php if ($menu_depute) echo 'active '; ?>">
              <a href="<?php echo url_for('@list_parlementaires'); ?>">Les Députés</a>
              <ul class="menu submenu is-dropdown-submenu">
                <li class="is-submenu-item is-dropdown-submenu-item"><a href="<?php echo url_for('@list_parlementaires'); ?>">Par ordre alphabétique</a></li>
                <li class="is-submenu-item is-dropdown-submenu-item"><a href="<?php echo url_for('@list_parlementaires_circo'); ?>">Par circonscription</a></li>
                <li class="is-submenu-item is-dropdown-submenu-item"><a href="<?php echo url_for('@parlementaires_tags'); ?>">Par mots clés</a></li>
                <li class="is-submenu-item is-dropdown-submenu-item"><a href="<?php echo url_for('@top_global'); ?>">Synthèse</a></li>
                <li class="is-submenu-item is-dropdown-submenu-item"><a href="<?php echo url_for('@parlementaire_random'); ?>">Au hasard</a></li>
              </ul>
            </li>
            <li class="dossiers-orange has-dropdown <?php if ($menu_dossier) echo 'active'; ?>">
              <a href="<?php echo url_for('@sections?order=date')?>">Les Dossiers</a>
              <ul class="menu submenu is-dropdown-submenu">
                <li class="is-submenu-item is-dropdown-submenu-item"><a href="<?php echo url_for('@sections?order=date'); ?>">Les derniers dossiers</a></li>
                <li class="is-submenu-item is-dropdown-submenu-item"><a href="<?php echo url_for('@sections?order=plus'); ?>">Les dossiers les plus discutés</a></li>
                <li class="is-submenu-item is-dropdown-submenu-item"><a href="<?php echo url_for('@sections?order=coms'); ?>">Les dossiers les plus commentés</a></li>
              </ul>
            </li>
            <li class="citoyens-vert has-dropdown <?php if ($menu_citoyen) echo 'active'; ?>">
              <a href="<?php echo url_for('@list_citoyens?order=date')?>">Les Citoyens</a>
              <ul class="menu submenu is-dropdown-submenu">
                <li class="is-submenu-item is-dropdown-submenu-item"><a href="<?php echo url_for('@list_citoyens?order=date'); ?>">Tous les citoyens</a></li>
                <li class="is-submenu-item is-dropdown-submenu-item"><a href="<?php echo url_for('@commentaires'); ?>">Les derniers commentaires</a></li>
                <li class="is-submenu-item is-dropdown-submenu-item"><a href="<?php echo url_for('@assister'); ?>">Assister aux débats</a></li>
              </ul>
            </li>
            <li class="contribuer">
              <a href="http://www.regardscitoyens.org/nous-aider">Contribuer</a>
            </li>
            <li class="extra">
              <a title="Questions fréquemment posées" href="<?php echo url_for('@faq')?>">FAQ</a>
            </li>
            <li class="extra">
              <a href="#">Se connecter</a>
            </li>
            <li class="rechercher">            
              <form action="<?php echo url_for('@recherche_solr'); ?>" method="get">
                <div class="input-group">
                  <input class="input-group-field" name="search" type="search" placeholder="Rechercher un député,une ville, un mot... "/>
                  <div class="input-group-button">
                    <input class="button " value="Rechercher" type="submit"/>
                  </div>
                </div>
              </form>
            </li>
          </ul>
      </nav>-->
  
    <nav class="top-bar stacked-for-medium">
      <div class="top-bar-title">
        <span data-responsive-toggle="responsive-menu" data-hide-for="medium">
          <button class="menu-icon dark" type="button" data-toggle></button>
        </span>
        <strong><a href="<?php echo url_for('@homepage'); ?>" title="Accueil">
          <?php echo image_tag($style.'/new_nosdeputes_menu', array('alt' => 'Nos Députés')); ?> NosDéputés.FR</a></strong>
      </div>
      <div id="responsive-menu">
        <div class="top-bar-left">
          <ul class="menu dropdown" data-dropdown-menu>
            <li class="deputes-bleu has-dropdown <?php if ($menu_depute) echo 'active '; ?>">
              <a href="<?php echo url_for('@list_parlementaires'); ?>">Les Députés</a>
              <ul class="menu submenu">
                <li><a href="<?php echo url_for('@list_parlementaires'); ?>">Par ordre alphabétique</a></li>
                <li><a href="<?php echo url_for('@list_parlementaires_circo'); ?>">Par circonscription</a></li>
                <li><a href="<?php echo url_for('@parlementaires_tags'); ?>">Par mots clés</a></li>
                <li><a href="<?php echo url_for('@top_global'); ?>">Synthèse</a></li>
                <li><a href="<?php echo url_for('@parlementaire_random'); ?>">Au hasard</a></li>
              </ul>
            </li>
            <li class="dossiers-orange has-dropdown <?php if ($menu_dossier) echo 'active'; ?>">
              <a href="<?php echo url_for('@sections?order=date')?>">Les Dossiers</a>
              <ul class="menu submenu">
                <li><a href="<?php echo url_for('@sections?order=date'); ?>">Les derniers dossiers</a></li>
                <li><a href="<?php echo url_for('@sections?order=plus'); ?>">Les dossiers les plus discutés</a></li>
                <li><a href="<?php echo url_for('@sections?order=coms'); ?>">Les dossiers les plus commentés</a></li>
              </ul>
            </li>
            <li class="citoyens-vert has-dropdown <?php if ($menu_citoyen) echo 'active'; ?>">
              <a href="<?php echo url_for('@list_citoyens?order=date')?>">Les Citoyens</a>
              <ul class="menu submenu">
                <li><a href="<?php echo url_for('@list_citoyens?order=date'); ?>">Tous les citoyens</a></li>
                <li><a href="<?php echo url_for('@commentaires'); ?>">Les derniers commentaires</a></li>
                <li><a href="<?php echo url_for('@assister'); ?>">Assister aux débats</a></li>
              </ul>
            </li>
          </ul>
        </div>
        <div class="top-bar-right">
          <ul class="menu">
            <li class="contribuer">
              <a href="http://www.regardscitoyens.org/nous-aider">Contribuer</a>
            </li>
            <li class="extra">
              <a title="Questions fréquemment posées" href="<?php echo url_for('@faq')?>">FAQ</a>
            </li>
            <li class="extra">
              <a href="#">Se connecter</a>
            </li>
            <li class="rechercher">            
              <form action="<?php echo url_for('@recherche_solr'); ?>" method="get">
                <div class="input-group">
                  <input class="input-group-field" name="search" type="search" placeholder="Rechercher un député,une ville, un mot... "/>
                  <div class="input-group-button">
                    <input class="button " value="Rechercher" type="submit"/>
                  </div>
                </div>
              </form>
            </li>
          </ul>
        </div>
      </div>
    </nav><!-- /.top-bar -->
	   
 
    <!--<div class="contenu_page">-->
      <?php if ($sf_user->hasFlash('notice')) :?>
      <p class='flash_notice'><?php echo $sf_user->getFlash('notice'); ?></p>
      <?php endif;?>
      <?php if ($sf_user->hasFlash('error')) :?>
      <p class='flash_error'><?php echo $sf_user->getFlash('error'); ?></p>
      <?php endif;?>
      <?php echo $sf_content ?>
    <!--</div>-->
 
    <footer id="footer">
      <div class="row expanded">
        <div class="menu-centered small-12 columns">
          <ul class="menu">
            <li><a href="http://cpc.regardscitoyens.org/trac"><img src="/images/agpl.png" /></a></li>
            <li><a href="http://cpc.regardscitoyens.org/trac/wiki/API"><img src="/images/opendata.png" /></a></li>
            <li><a href="<?php echo url_for("@faq"); ?>">Questions fréquentes</a></li>
            <li><a href="http://www.regardscitoyens.org/publication/">Données</a></li>
            <li><a href="http://cpc.regardscitoyens.org/trac/wiki/API">API</a></li>
            <li>  
              <?php if (myTools::getPreviousHost()) {
	              echo '<a href="http://'.myTools::getPreviousHost().'">Législature précédente</a>';
              }?>
            </li>
            <li><a href="http://www.regardscitoyens.org/mentions-legales/">Mentions légales</a></li>
            <li><a href="http://www.regardscitoyens.org/nous-contacter/">Contact</a></li>
            <li class="regards-citoyens-link"><a href="http://www.regardscitoyens.org/" onclick="return(window.open(this.href)?false:true);">
              <?php echo image_tag($style.'/new_regards-citoyens_small.png', array('alt' => 'Regards Citoyens')); ?> Une initiative de RegardsCitoyens.org
            </a><li>
          </ul>
        </div>
      </div><!-- /.row-->
    </footer>
    
    
    <?php echo javascript_include_tag('jquery.min.js'); ?>
    <?php echo javascript_include_tag('jquery-ui-1.8.5.custom.min.js'); ?>
    <?php echo javascript_include_tag('fonctions.js'); ?>
    <?php echo javascript_include_tag('foundation.min.js'); ?>    
    <script>
      $(document).foundation();
    </script>
      
  </body>
</html>
