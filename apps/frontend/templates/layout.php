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
?>
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
  <div id="contenu">
			<div id="top">
				<div class="initiative">
					<a href="http://www.regardscitoyens.org/" onclick="return(window.open(this.href)?false:true);"><?php echo image_tag($style.'/top_initiative.png', array('alt' => 'Une initiative de RegardsCitoyens.fr')); ?></a>
				</div>
				<div class="identification">
				     <?php if(!$sf_user->isAuthenticated()) { ?>
					<form method="post" action="<?php echo url_for('@signin'); ?>">
					<p>
					<input type="text" name="signin[login]" value="Identifiant" onfocus="if(this.value=='Identifiant')this.value ='';" onblur="if(this.value=='')this.value ='Identifiant';" />
					<input type="text" name="signin[password]" value="&#149;&#149;&#149;&#149;&#149;&#149;&#149;&#149;" onfocus="if(this.value=='&#149;&#149;&#149;&#149;&#149;&#149;&#149;&#149;')this.value ='';" onblur="if(this.value=='')this.value ='&#149;&#149;&#149;&#149;&#149;&#149;&#149;&#149;';"/>
					<button type="submit" value="login" id="bt1"></button>
					<a href="<?php echo url_for('@inscription') ?>"><button id="bt2"></button></a>
					<!-- <input type="checkbox" name="signin[remember]" id="signin_remember" title="se rappeler de moi" /> --> 
					</p>
					</form> <?php }
					if($sf_user->isAuthenticated())
					{ 
					if($sf_user->getAttribute('is_active') == true) { ?>
					<a href="<?php echo url_for('@citoyen?slug='.$sf_user->getAttribute('slug')); ?>">Mon Profil</a> - 
					<?php
					}
					?>
					Connecté en tant que <?php echo $sf_user->getAttribute('login'); ?> - 
					<a href="<?php echo url_for('@signout') ?>">Déconnexion</a>
					<?php } ?>
				</div>
			</div>
			<div id="header">
				<a href="<?php echo url_for('@homepage');?>"><?php echo image_tag($style.'/header_logo.png', array('alt' => 'NosDeput&eacute;s.fr')); ?></a>
			</div>
  			<div id="menu">
				<div class="menu_navigation">
					<div id="item1"><a href="<?php echo url_for('@homepage'); ?>"></a></div>
					<div id="item2"><a href="<?php echo url_for('@list_parlementaires'); ?>"></a></div>
					<div id="item3"><a href="<?php echo url_for('@sections?order=date')?>"></a></div>
					<div id="item4"><a href="<?php echo url_for('@list_citoyens')?>"></a></div>
					<div id="item5"><a href="#"></a></div>
				</div>
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
							if (isset($_GET['search']) && preg_match('/question/', $_SERVER['REQUEST_URI'])) $selectquestion = ' selected="selected"';
							else $selectquestion = "";
							?>
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
							<input class="bouton_ok" value="" type="submit"/>
							<input class="rechercher" name="search" type="text" size="15" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>"/>
						</p>
					</form>
				</div>
			</div>
			<div id="sous_menu">
				<div class="elements_sous_menu"> <!-- A rendre dynamique en php -->
					<ul>
						<li><a href="#">El&eacute;ment 1</a> |</li>
						<li><a href="#">El&eacute;ment 2</a> |</li>
						<li><a href="#">El&eacute;ment 3</a> |</li>
						<li><a href="#">El&eacute;ment 4</a> |</li>
						<li><a href="#">El&eacute;ment 5</a></li>
					</ul>
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
				<a href=""><?php echo image_tag($style.'/bottom_plansite.png', array('alt' => 'Plan du site')); ?></a>
				<a href=""><?php echo image_tag($style.'/bottom_qui.png', array('alt' => 'Qui sommes-nous')); ?></a>
			</div>
    </div>
  </body>
</html>
