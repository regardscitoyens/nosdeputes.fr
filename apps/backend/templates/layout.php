<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>  
    <?php include_title() ?>
    <script type="text/javascript">
     /* <![CDATA[ */
	function open_sf_admin_bar() {
	  var selection = document.getElementById('sf_admin_bar');
      if (selection.style.display != 'block') {selection.style.display = 'block';} else {selection.style.display = 'none';}
	}
	/* ]]> */
	</script>
  </head>
  <body>
    <div id="backend">
      <div id="header">
        
      </div>
    
      <div id="menu">
        <ul>
          <li><a href="<?php echo url_for('main/index'); ?>">Accueil</a></li>
		  <?php $appRoutingFile = sfConfig::get("sf_root_dir").DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.'backend'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'routing.yml' ;
		$yml = sfYaml::load($appRoutingFile) ;
		foreach($yml as $element)
        {
          if(isset($element['options']['module']))
		  {
		    echo '<li><a href="'.url_for($element['options']['module'].'/index').'">'.ucfirst($element['options']['module']).'s</a></li>';
		  }
        }
		?>
          <li><a href="#">Outils</a></li>
        </ul>
      </div>
        
      <div id="corps_page">
        <div class="contenu_page">
          <?php echo $sf_content ?>
        </div>
		<div id="open_sf_admin_bar" onclick="open_sf_admin_bar();"></div>
      </div>
      
      <div id="bottom">
        <span class="arrow"><a href="#"><img src="<?php echo $sf_request->getRelativeUrlRoot() ?>/css/backend/images/up_arrow.png" alt="Haut de page" title="Haut de page" /></a></span>
      </div>
    </div>
  </body>
</html>
