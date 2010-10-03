<?php $sf_response->setTitle('NosDéputés.fr : Observatoire citoyen de l\'activité parlementaire');  $style = "xneth"; ?>
<script type="text/javascript">
<!--
	// preload img fond sous-menu
	$('<img />').attr('src', '<?php echo $sf_request->getRelativeUrlRoot()."/css/".$style."/images/sous_menu_combined.png"; ?>');
	
     // Widget page d'accueil
	commentaires = new Array ();
	commentaires_update = new Array ();
	apparait = 500;
	disparait = 300;
	change = 7000;
	update = 60000;
	reprends_a = 0;
	timerWidget = null;
	i = 0;
	
	$(document).ready(function() {
	  getWidget();
	  timerUpdate = setInterval(function(){updateWidget();}, update);
	});
	
	function getWidget() {
	  $.ajax({
	    url: "<?php echo url_for('@commentaires_widget'); ?>",
        async: true,
		cache: false,
		beforeSend: function(xhr) {
		  xhr.setRequestHeader("If-Modified-Since","0");
		  commentaires = new Array (); // à virer
		  i = 0;
		  $(".box_widget").css("overflow", "hidden");
		  $("#coms_widget").css("overflow", "hidden");
		  $("#coms_widget").css("display", "none");
		  $(".box_widget h2").append('<div id="chargement_widget"><\/div>');
		},
		success: function(html){
		  if ($.browser.msie == true) {
		    obj = document.getElementById("coms_widget");
			obj.innerHTML = html;
		  }
		  else {
		    $("#coms_widget").text("");
			$("#coms_widget").append(html);
		  }
		  launchWidget();
		},
		complete: function() {
		  $("#coms_widget").fadeIn(1000);
		  $("#chargement_widget").remove();
		}
	  });
    }
	
	function launchWidget() {
	  $(".commentaire_widget").each(function () {
	    commentaires[i] = $(this).attr("id");
		$(this).css("display", "none");
		i++;
	  });
	  changeCommentaire(0);
	}
	
	function changeCommentaire(i) {
	  if(i == commentaires.length) { i = 0; }
	  cache = i - 2;
	  if(i == 0) { cache = commentaires.length -2; }
	  if(i == 1) { cache = commentaires.length -1; }
	  com_suivant = $("#"+commentaires[i]).detach();
	  $("#coms_widget").prepend(com_suivant);
	  com_suivant = null;
	  $("#"+commentaires[cache]).fadeOut(disparait);
	  $("#"+commentaires[i]).slideDown(apparait);
	  i++;
	  timerWidget = setTimeout(function(){changeCommentaire(i);}, change);
    }
	
	function updateWidget() {
	  $.ajax({
	    url: "<?php echo url_for('@commentaires_widget'); ?>",
		async: true,
		cache: false,
		beforeSend: function(xhr) {
		  xhr.setRequestHeader("If-Modified-Since","0");
		  clearTimer();
		  $(".box_widget h2").append('<div id="chargement_widget"><\/div>');
		},
		success: function(html){
		  if ($.browser.msie == true) {
		    obj = document.getElementById("coms_widget_update");
			  obj.innerHTML = html;
		  }
		  else {
		    $("#coms_widget_update").text("");
			  $("#coms_widget_update").append(html);
		  }
		  reorderWidget();
		},
		complete: function() {
	    $("#chargement_widget").remove();
		  $("#coms_widget").text("");
		  $(".commentaire_widget").css("display", "none");
		  i = 0;
		  changeCommentaire(i);
		},
		error: function() {
		  $("#chargement_widget").remove();
		}
	  });
	}
	
	function reorderWidget() {
	  q = 0;
	  $("#coms_widget_update div[class='commentaire_widget']").each(function () {
	    commentaires_update[q] = $(this).attr("id");
		$("#"+commentaires_update[q]).css("display", "none");
		q++;
	  });
    commentaires = commentaires_update;
	  commentaires_update = new Array ();
    
	  /* $.merge(commentaires, commentaires_update);
	  commentaires_update = null;
	  commentaires_temp = uniqueArray(commentaires);
	  commentaires_temp.sort();
	  commentaires_temp.reverse();
	  commentaires = new Array();
	  q = 0;
	  $(commentaires_temp).each(function () {
	    commentaires[q] = $("#"+this).attr("id"); q++;
	  });
	  commentaires_temp = null; */
	}
	
  function clearTimer() {
	  clearTimeout(timerWidget);
	}
	
	$(".commentaire_widget").live("mouseover", function() {
	  clearInterval(timerUpdate);
	  clearTimeout(timerWidget);
	  reprends_a = i;
	});
	
	$(".commentaire_widget").live("mouseout", function() {
	  timerUpdate = setInterval(function(){updateWidget();}, update);
	  timerWidget = setTimeout(function(){changeCommentaire(reprends_a);}, 1000);
	});
//-->
</script>
<div class="clear"> 
<div class ="accueil_message">
<div class="accueil_message_content">
    <h1>Bienvenue sur NosDéputés.fr</h1>
    <p>NosDéputés.fr est un site qui cherche à mettre en valeur l'activité parlementaire des députés de l'Assemblée Nationale Française.</p>
    <p>En synthétisant les différentes activités législatives et de contrôle du gouvernement des élus de la nation, ce site essaie de donner aux citoyens de nouveaux outils pour comprendre et analyser le travail de leurs représentants.</p>
    <p>Conçu comme une plateforme de médiation entre citoyens et députés, le site propose à chacun de participer et de s'exprimer sur les débats parlementaires. Au travers de leurs commentaires, les utilisateurs sont invités à créer le débat en partageant leur expertise lorsque cela leur semble utile. Peut-être pourront-ils ainsi nourrir le travail de leurs élus ?</p>
  </div>
  <div class="accueil_message_signature">
    <p>Toute l'équipe du collectif <a href="http://www.regardscitoyens.org/">RegardsCitoyens.org</a>.</p>
  </div>
</div>

	<div class="accueil_deputes_jour">
	<?php echo include_component('parlementaire', 'duJour'); ?>
	</div>
</div>
<div class="clear"></div>
<div class="clear accueil">
  <div class="box_news">
  <div class="carte">
  <h2><span style="margin-right: 5px;"><img alt="actu" src="<?php echo $sf_request->getRelativeUrlRoot(); ?>/images/xneth/ico_nosdeputes.png" /></span>Touver son député</h2>
    <div class="cont_box_news">
	  <p><?php include_partial('circonscription/map', array('circo' => "full", 'width'=>400, 'height'=>0)); ?></p>
	  </div>
	  <div class="message">
	  <p>Pour savoir consulter la fiche de votre député, saisissez son nom. Si vous ne le connaissez pas indiquez votre code postal ou le nom de votre ville, nous essayerons de le trouver pour vous:</p>
	  <form action="<?php echo url_for('solr/search?object_name=Parlementaire'); ?>">
	  <input name="search"/><input type="submit" value="Rechercher"/>
	  </form>
	  </div>
    </div>
  </div>
  <div class="clear"></div>
<div class="box_container">
  <div class="box_repartition aligncenter"><div style="margin: auto;">
  <h2><span style="margin-right: 5px;"><img alt="activite" src="<?php echo $sf_request->getRelativeUrlRoot(); ?>/images/xneth/ico_graph.png" /></span><a href="<?php echo url_for('@top_global'); ?>">Activité parlementaire des 12 derniers mois</a></h2>
  <?php echo include_component('plot', 'newGroupes', array('type' => 'home')); ?>
  <?php // echo include_component('plot', 'groupes', array('plot' => 'total')); ?>
  </div></div>
  <div class="box_tags">
  <h2><span style="margin-right: 5px;"><img alt="tags" src="<?php echo $sf_request->getRelativeUrlRoot(); ?>/images/xneth/assemblee-nationale.png" /></span><?php echo link_to('En ce moment à l\'Assemblée nationale', '@parlementaires_tags'); ?></h2>
  <?php echo include_component('tag', 'globalActivite'); ?>
  </div>
</div>
  <div class="clear"></div>
  <div class="box_widget">
    <h2><span style="margin-right: 5px;"><img alt="comments" src="<?php echo $sf_request->getRelativeUrlRoot(); ?>/images/xneth/ico_comment.png" /></span><a href="<?php echo url_for('@commentaires'); ?>">Les derniers commentaires</a><span class="rss"><a href="<?php echo url_for('@commentaires_rss'); ?>"><?php echo image_tag('xneth/rss.png', 'alt="Flux rss"'); ?></a></span></h2>
    <div id="coms_widget">
      <noscript>
      <?php include_component('commentaire', 'showWidget'); ?>
      </noscript>
    </div>
    <div style="display: hidden;" id="coms_widget_update"></div>
  </div>
</div>
