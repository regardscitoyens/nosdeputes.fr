<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head><?php
$style = 'xneth';
include_http_metas();
include_metas();
include_title();
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

$gitversion = file_get_contents('../.git/' . trim(substr(file_get_contents('../.git/HEAD'), 5)));
?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="search" href="<?php echo $sf_request->getRelativeUrlRoot(); ?>/nosdeputesfr.xml" title="Rechercher sur NosDéputés.fr" type="application/opensearchdescription+xml" />
    <?php echo stylesheet_tag($style.'/jquery-ui-1.8.24.min.css'); ?>
    <?php echo stylesheet_tag($style.'/style.css?'.$gitversion); ?>
    <?php echo stylesheet_tag('fonts/liberation-sans.css'); ?>
    <?php echo stylesheet_tag($style.'/print', array('media' => 'print')); ?>
    <!--[if lte IE 6]>
      <?php echo stylesheet_tag($style.'/ie6'); ?>
      <script type="text/javascript" src="<?php echo $sf_request->getRelativeUrlRoot(); ?>/iepngfix/iepngfix_tilebg.js"></script>
      <style type="text/css">
        img, div { behavior: url('<?php echo $sf_request->getRelativeUrlRoot(); ?>/iepngfix/iepngfix.php') }
      </style>
    <![endif]-->
    <?php include_partial('parlementaire/cssCouleursGroupes'); ?>
    <?php echo javascript_include_tag('jquery-1.8.3.min.js'); ?>
    <?php echo javascript_include_tag('jquery-ui-1.8.24.min.js'); ?>
    <?php echo javascript_include_tag('fonctions.js?'.$gitversion); ?>
  </head>
  <body>
    <div id="contenu">
      <div id="top">
        <div class="initiative">
          <a target="_blank" href="https://www.regardscitoyens.org/" onclick="return(window.open(this.href)?false:true);">Une initiative de RegardsCitoyens.org</a>
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
        <a style="float:left;" href="<?php echo url_for('@homepage');?>"><?php echo image_tag($style.'/header_logo.png', array('id' => 'logo', 'alt' => 'NosDeput&eacute;s.fr')); ?></a>
        <a style="float:right; margin-right: 55px; margin-top: 2px;" target="_blank" href="https://www.regardscitoyens.org/nous-aider/"><?php echo image_tag('contribuer.png', array('alt' => 'Nous soutenir')); ?></a>
      </div>
      <div id="menu">
        <div class="menu_navigation">
          <div id="item1"><a href="<?php echo url_for('@homepage'); ?>" title="Accueil"></a></div>
            <div id="item2"><a <?php if ($menu_depute) echo 'class="selected" '; ?>href="<?php echo url_for('@list_parlementaires'); ?>"><span class="gris">Les</span> <span class="vert">D</span><span class="gris">&eacute;put&eacute;s</span></a></div>
            <div id="item3"><a <?php if ($menu_dossier) echo 'class="selected" '; ?>href="<?php echo url_for('@sections?order=date')?>"><span class="gris">Les</span> <span class="orange">D</span><span class="gris">ossiers</span></a></div>
            <div id="item4"><a <?php if ($menu_citoyen) echo 'class="selected" '; ?>href="<?php echo url_for('@list_citoyens?order=date')?>"><span class="gris">Les</span> <span class="bleu">C</span><span class="gris">itoyens</span></a></div>
            <div id="item5"><a title="Questions fréquemment posées" href="<?php echo url_for('@faq')?>"><span class="gris">FAQ</span></a></div>
          </div>
          <?php $search = strip_tags($sf_request->getParameter('query'));
                $extraclass = '' ;
                if (!$search) {$extraclass="examplevalue"; $search = "Rechercher un député, une ville, un mot...";} ?>
          <div class="menu_recherche">
            <form action="<?php echo url_for('@recherche_solr'); ?>" method="get">
              <p>
                <input class="rechercher<?php echo " ".$extraclass; ?>" name="search" type="text" size="25" value="<?php echo str_replace('"', '&quot;', $search); ?>"/>
                <input title="Rechercher sur NosDéputés.fr" class="bouton_ok" value="" type="submit"/>
              </p>
            </form>
          </div>
        </div>
        <div id="sous_menu">
          <div id="sous_menu_1" style="display:<?php if ($menu_depute) echo 'block'; else echo 'none'; ?>">
          <div class="elements_sous_menu">
            <ul>
              <li><a href="<?php echo url_for('@list_parlementaires'); ?>">Tous les députés</a> <strong>|</strong></li>
              <li><a href="<?php echo url_for('@list_parlementaires_circo'); ?>">Par circonscription</a> <strong>|</strong></li>
              <li><a href="<?php echo url_for('@list_organismes'); ?>">Par organisme</a> <strong>|</strong></li>
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
              <li><a href="<?php echo url_for('@sections?order=plus'); ?>">Les plus discutés</a> <strong>|</strong></li>
              <li><a href="<?php echo url_for('@sections?order=coms'); ?>">Les plus commentés</a> <strong>|</strong></li>
              <li><a href="<?php echo url_for('@scrutins'); ?>">Les scrutins publics</a></li>
            </ul>
          </div>
        </div>

        <div id="sous_menu_3" style="display:<?php if ($menu_citoyen) echo 'block'; else echo 'none'; ?>">
          <div class="elements_sous_menu">
            <ul>
              <li><a href="<?php echo url_for('@list_citoyens?order=date'); ?>">Tous les citoyens</a> <strong>|</strong></li>
              <li><a href="<?php echo url_for('@commentaires'); ?>">Les derniers commentaires</a> <strong>|</strong></li>
              <li><a href="<?php echo url_for('@assister'); ?>">Assister aux débats</a></li>
            </ul>
          </div>
  	    </div>
      </div>
      <div id="corps_page">
        <div class="contenu_page">
          <?php if (myTools::hasAnnounce()) : ?>
          <div id="announce"><h2>
              <?php if (myTools::getAnnounceLink()): ?>
	      <a <?php if (!preg_match('/^\//', myTools::getAnnounceLink())) echo 'target="_blank" '; ?>href="<?php echo myTools::getAnnounceLink(); ?>"><?php echo myTools::getAnnounceText(); ?> : <span>Cliquez ici !</span></a>
              <?php else: ?>
              <?php echo myTools::getAnnounceText(); ?>
              <?php endif; ?></h2></div>
            <?php if (!isset($_GET['nodelay']) || !$_GET['nodelay']): ?>
            <script type="text/javascript">
                $(document).ready(function() {
                  $('#announce h2').fadeOut();
                  $('#announce h2').delay(250).fadeIn('slow');
                });
            </script>
            <?php endif; ?>
          <?php endif ?>
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
        <div class="legal">
          <span id="licences">
            <a target="_blank" href="https://github.com/regardscitoyens/nosdeputes.fr"><img src="/images/agpl.png" height="15"/></a>
            <a target="_blank" href="https://github.com/regardscitoyens/nosdeputes.fr/blob/master/doc/opendata.md"><img src="/images/opendata.png" height="15"/></a>
          </span>
          <span id="legalinks">
            <a href="<?php echo url_for("@faq"); ?>">Questions fréquentes</a>&nbsp; &mdash; &nbsp;
            <a target="_blank" href="https://github.com/regardscitoyens/nosdeputes.fr/blob/master/doc/opendata.md">Données</a>&nbsp; &mdash; &nbsp;
            <a target="_blank" href="https://github.com/regardscitoyens/nosdeputes.fr/blob/master/doc/api.md">API</a>&nbsp; &mdash; &nbsp;
            <?php if (myTools::getPreviousHost()) :?>
            <a target="_blank" href="https://<?php echo myTools::getPreviousHost(); ?>">Législature précédente</a>&nbsp; &mdash; &nbsp;
            <?php endif;?>
            <?php if (myTools::getNextHost()) :?>
            <a target="_blank" href="https://<?php echo myTools::getNextHost(); ?>">Législature suivante</a>&nbsp; &mdash; &nbsp;
            <?php endif;?>
            <a target="_blank" href="https://stats.regardscitoyens.org/">Stats de visites</a>&nbsp; &mdash; &nbsp;
            <a target="_blank" href="https://www.regardscitoyens.org/mentions-legales/">Mentions légales</a>&nbsp; &mdash; &nbsp;
            <a target="_blank" href="https://www.regardscitoyens.org/nous-contacter/">Contact</a>
          </span>
        </div>
        <div class="regardscitoyens">
		  <a target="_blank" href="https://www.regardscitoyens.org"><span class="RC">R</span>egards<span class="RC">C</span><span style="color: #C1272D;">i</span>toyens.org</a>
		</div>
      </div>
    </div>
  </body>

<?php $piwik = myTools::getPiwik();
if ($piwik["domain"] && $piwik["id"]) :?>
  <script type="text/javascript">
// Piwik
var _paq = _paq || [];
_paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
_paq.push(["setCookieDomain", "*.nosdeputes.fr"]);
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(function() {
  var u="//<?php echo $piwik["domain"]; ?>/";
  _paq.push(['setTrackerUrl', u+'piwik.php']);
  _paq.push(['setSiteId', '<?php echo $piwik["id"]; ?>']);
  var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
  g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
})();
  </script>
  <noscript><p style="height:0; margin:0"><img src="//<?php echo $piwik["domain"]; ?>/piwik.php?idsite=<?php echo $piwik["id"]; ?>&rec=1" style="border:0; height:0" alt="" /></p></noscript>
<?php endif;?>

</html>
