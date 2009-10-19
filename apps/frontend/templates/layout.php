<?php $style = 'xneth'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>  
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
    <?php echo stylesheet_tag($style.'/style'); ?>
    <?php echo stylesheet_tag($style.'/print'); ?>
    <!--[if lte IE 6]>
    <?php echo stylesheet_tag($style.'/ie6'); ?>
    <style type="text/css">
    img, div { behavior: url('iepngfix/iepngfix.htc') }
    </style> 
    <![endif]-->
  </head>
  <body>
  <div id="contenu">
      <div id="top">
        <div class="initiative">
          <a href="http://www.regardscitoyens.org/" onclick="return(window.open(this.href)?false:true);"><?php echo image_tag($style.'/top_initiative.png', array('alt' => 'Une initiative de RegardsCitoyens.fr')); ?></a>
        </div>
<div id="connected" class="identification">
<p id="loggued_top">
<a href="/login">Se connecter</a> - 
<a href="/login">Mon compte</a>
</p>
</div>
  <script type="text/javascript"><!-- 
  $('#connected').load("<?php echo url_for('@identification_ajax'); ?>"); 
--></script>
      </div>
      <div id="header">
        <a href="<?php echo url_for('@homepage');?>"><?php echo image_tag($style.'/header_logo.png', array('alt' => 'NosDeput&eacute;s.fr')); ?></a>
      </div>
        <div id="menu">
        <div class="menu_navigation">
            <div id="item1"><a href="<?php echo url_for('@homepage'); ?>" title="Accueil"></a></div>
          <div id="item2"><a <?php if ($menu_depute) echo 'class="selected" '; ?>href="<?php echo url_for('@list_parlementaires'); ?>"><span class="gris">Les</span> <span class="vert">D</span><span class="gris">&eacute;put&eacute;s</span></a></div>
          <div id="item3"><a <?php if ($menu_dossier) echo 'class="selected" '; ?>href="<?php echo url_for('@sections?order=date')?>"><span class="gris">Les</span> <span class="orange">D</span><span class="gris">ossiers</span></a></div>
          <div id="item4"><a <?php if ($menu_citoyen) echo 'class="selected" '; ?>href="<?php echo url_for('@commentaires')?>"><span class="gris">Les</span> <span class="bleu">C</span><span class="gris">itoyens</span></a></div>
          <div id="item5"><a href="<?php echo url_for('@faq')?>"><span class="gris">FAQ</span></a></div>
        </div>
                    <?php $search = strip_tags($sf_request->getParameter('search'));?>
        <div class="menu_recherche">
          <form action="<?php echo url_for('@search'); ?>" method="get">
            <p>
                            <select class="type_recherche" name="type">
                <option value="depute"<?php echo $selectdepute; ?>>Députés</option>
                <option value="departement"<?php echo $selectcirco; ?>>Départements</option>
                <option value="profession"<?php echo $selectprof; ?>>Profession</option>
                <option value="intervention"<?php echo $selectinterv; ?>>Interventions</option>
                <option value="question"<?php echo $selectquestion; ?>>Questions écrites</option>
                <option value="amendement"<?php echo $selectamdmt; ?>>Amendements</option>
              </select>
              <?php echo image_tag($style.'/recherche_fleche.png', array('alt' => '')); ?>
                  <input class="rechercher" name="search" type="text" size="15" value="<?php echo $search; ?>"/>
              <input class="bouton_ok" value="" type="submit"/>
                        </p>
          </form>
        </div>
      </div>
      <div id="sous_menu">
        <div id="sous_menu_1" style="display:<?php if ($menu_depute) echo 'block'; else echo 'none'; ?>">
        <div class="elements_sous_menu">
          <ul>
            <li><a href="<?php echo url_for('@list_parlementaires'); ?>">Par ordre alphabétique</a> <strong>|</strong></li>
            <li><a href="<?php echo url_for('@list_parlementaires_circo'); ?>">Par circonscription</a> <strong>|</strong></li>
            <li><a href="<?php echo url_for('@parlementaires_tags'); ?>">Par mots clés</a> <strong>|</strong></li>
            <li><a href="<?php echo url_for('@top_global'); ?>">Synthèse</a> <strong>|</strong></li>
            <li><a href="<?php echo url_for('@parlementaire_random'); ?>">Au hasard</a></li>
          </ul>
        </div>
        </div>
        <div id="sous_menu_2" style="display:<?php if ($menu_dossier) echo 'block'; else echo 'none'; ?>">
              <div class="elements_sous_menu">
          <ul>
            <li><a href="<?php echo url_for('@sections?order=date'); ?>">Les derniers dossiers</a> <strong>|</strong></li>
            <li><a href="<?php echo url_for('@sections?order=plus'); ?>">Les dossiers les plus discutés</a></li>
          </ul>
        </div>
        </div>
        
        <div id="sous_menu_3" style="display:<?php if ($menu_citoyen) echo 'block'; else echo 'none'; ?>">
              <div class="elements_sous_menu">
          <ul>
            <li><a href="<?php echo url_for('@list_citoyens'); ?>">Tous les citoyens</a> <strong>|</strong></li>
            <li><a href="<?php echo url_for('@commentaires'); ?>">Les derniers commentaires</a> <strong>|</strong></li>
            <li><a href="<?php echo url_for('@compterendu_list'); ?>">Les comptes rendus citoyens</a></li>
          </ul>
        </div>
        </div>
      </div>
      <div id="corps_page">
        <div class="contenu_page">
          <?php if ($sf_user->hasFlash('notice')) :?>
          <p class='flash_notice'><?php echo $sf_user->getFlash('notice'); ?></p>
          <?php endif;?>
          <?php if ($sf_user->hasFlash('error')) :?>
          <p class='flash_error'><?php echo $sf_user->getFlash('error'); ?></p>
          <?php endif;?>
          <?php echo $sf_content ?>
        </div>
      </div>
      <div id="bottom">
        <a href="<?php echo url_for('@faq')?>"><?php echo image_tag($style.'/bottom_qui.png', array('alt' => 'Qui sommes-nous')); ?></a>
      </div>
    </div>
<script type="text/javascript">
$(document).ready(function() {
    if (typeof additional_load != 'undefined')
      additional_load();
    if (!$('#header_login').attr('value')) {
      $('#header_login').attr('value', 'Identifiant');
    }
    if (!$('#header_pass').attr('value')) {
      $('#header_pass').attr('value', '______________');
    }
  });
</script>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-10423931-2");
pageTracker._trackPageview();
} catch(err) {}</script>
  </body>
</html>
