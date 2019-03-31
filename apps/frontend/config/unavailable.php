<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="fr" />
<meta name="robots" content="index, follow" />
  
    <title></title>
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="screen" href="/css/xneth/jquery-ui-1.8.5.custom.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="/css/xneth/style.css" />

    <link rel="stylesheet" type="text/css" media="print" href="/css/xneth/print.css" />
    <!--[if lte IE 6]>
    <link rel="stylesheet" type="text/css" media="screen" href="/css/xneth/ie6.css" />
	<script type="text/javascript" src="/iepngfix/iepngfix_tilebg.js"></script>
    <style type="text/css">
    img, div { behavior: url('/iepngfix/iepngfix.php') }
    </style> 
    <![endif]-->
	<script type="text/javascript" src="/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="/js/jquery-ui-1.8.5.custom.min.js"></script>
	<script type="text/javascript" src="/js/fonctions.js"></script>
  </head>
  <body>

  <div id="contenu">
      <div id="top">
        <div class="initiative">
          <a href="http://www.regardscitoyens.org/" onclick="return(window.open(this.href)?false:true);">Une initiative de RegardsCitoyens.org</a>
        </div>
<div id="connected" class="identification">
<p id="loggued_top">
<a href="/login">Se connecter</a> - 
<a href="/login">Mon compte</a>

</p>
</div>
  <script type="text/javascript"><!-- 
  $('#connected').load("/ajax/identification"); 
--></script>
      </div>
      <div id="header" style="width: 971px; margin-left: 45px">
        <a href="/"><img alt="NosDeput&eacute;s.fr" src="/images/xneth/header_indispo.png" /></a>
      </div>
        <div id="menu">
        <div class="menu_navigation">

            <div id="item1"><a href="/" title="Accueil"></a></div>
          <div id="item2"><a class="selected" href="/deputes"><span class="gris">Les</span> <span class="vert">D</span><span class="gris">&eacute;put&eacute;s</span></a></div>
          <div id="item3"><a href="/dossiers/date"><span class="gris">Les</span> <span class="orange">D</span><span class="gris">ossiers</span></a></div>
          <div id="item4"><a href="/citoyens/date"><span class="gris">Les</span> <span class="bleu">C</span><span class="gris">itoyens</span></a></div>

          <div id="item5"><a href="/faq"><span class="gris">FAQ</span></a></div>
        </div>
                <div class="menu_recherche">
          <form action="/recherche" method="get">
            <p>
              <input class="rechercher " name="search" type="text" size="25" value=""/>
              <input class="bouton_ok" value="" type="submit"/>
                        </p>

          </form>
        </div>
      </div>
      <div id="sous_menu">
        <div id="sous_menu_1" style="display:block">
        <div class="elements_sous_menu">
          <ul>
            <li><a href="/deputes">Par ordre alphabétique</a> <strong>|</strong></li>

            <li><a href="/circonscription">Par circonscription</a> <strong>|</strong></li>
            <li><a href="/deputes/tags">Par mots clés</a> <strong>|</strong></li>
            <li><a href="/synthese">Synthèse</a> <strong>|</strong></li>
            <li><a href="/hasard">Au hasard</a></li>

          </ul>
        </div>
        </div>
        <div id="sous_menu_2" style="display:none">
              <div class="elements_sous_menu">
          <ul>
            <li><a href="/dossiers/date">Les derniers dossiers</a> <strong>|</strong></li>

            <li><a href="/dossiers/plus">Les dossiers les plus discutés</a> <strong>|</strong></li>
            <li><a href="/dossiers/coms">Les dossiers les plus commentés</a></li>
          </ul>
        </div>
        </div>
        
        <div id="sous_menu_3" style="display:none">
              <div class="elements_sous_menu">

          <ul>
            <li><a href="/citoyens/date">Tous les citoyens</a> <strong>|</strong></li>
            <li><a href="/commentaires">Les derniers commentaires</a> <strong>|</strong></li>
            <li><a href="/compterendu">Les comptes rendus citoyens</a></li>
          </ul>

        </div>
	</div>
	     <div style="text-align: center; margin-top: 27px;"><h2>&nbsp;</h2></div>
      </div>
      <div id="corps_page">
<style>
.contenu_page h1 {font-size: 3em;}
.contenu_page p {font-size: 1.8em;}
</style>
        <div class="contenu_page">
		<h1>    NosDéputés.fr, c'est fini. </h1>

<p>Décidant pour la première fois, depuis près de 10 ans d'existence, de se pencher sérieusement sur ses propres données, l'équipe de <a href="https://www.RegardsCitoyens.org">Regards Citoyens</a>, alertée par de nombreux nouveaux députés, a découvert atterrée, que son site <a href="https://www.nosdeputes.fr">NosDéputés.fr</a> a des effets pervers sur la démocratie parlementaire. Les chiffres montrent ainsi, sans ambiguïté, que, très loin d'intéresser les citoyens à l'activité de leurs représentants, le site a eu pour seul et unique impact depuis sa création une pollution du travail législatif par une augmentation éhontée du nombre d'amendements déposés.</p>

<center><img class="alignnone size-medium wp-image-6892" src="https://www.regardscitoyens.org/wp-content/uploads/2019/03/amendements.png" alt="évolution du nombre d'amendements dans le temps" width="640" height="345"><br/><small>( Sources&nbsp;: <a href="https://www.regardscitoyens.org/wp-content/uploads/2019/03/AN_amendements_sessions.csv">données</a> - <a href="https://www.regardscitoyens.org/wp-content/uploads/2019/03/poisson-amendements-xkcdplots.tar.gz">code source</a> )</small></center>

<p><strong>Les membres de l'association ont donc voté à l'unanimité en assemblée générale samedi dernier la fermeture définitive et irrévocable du site NosDéputés.fr</strong>, afin d'encourager les députés à cesser de s'investir inutilement en commission ou hémicycle au risque de devenir de véritables députés «&nbsp;hors sol&nbsp;». Prenant enfin ses responsabilités face à une réalité incontestable, l'association espère que cet acte fort permettra aux députés de se concentrer sur leur activité essentielle&nbsp;: le travail de terrain en circonscription.</p>

<p>Le site <a href="https://www.nossenateurs.fr">NosSénateurs.fr</a> n'ayant pas de tels effets néfastes sur l'activité des sénateurs, le site dédié à l'activité du palais du Luxembourg reste ouvert jusqu'à nouvel ordre.</p>

        <div>
      </div>
      </div>
      <div id="bottom">
        <div class="regardscitoyens">
		<a href="http://www.regardscitoyens.org"><span class="RC">R</span>egards<span class="RC">C</span><span style="color: #C1272D;">i</span>toyens.org</a>
		</div>

      </div>
    </div>
  </body>
</html>

