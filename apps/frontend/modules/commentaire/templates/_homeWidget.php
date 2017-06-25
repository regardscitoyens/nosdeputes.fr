<?php if (!count($commentaires)) return; ?>
<script type="text/javascript">
// Widget page d'accueil
var commentaires = new Array(),
  commentaires_update = new Array(),
  i = 0,
  timerWidget = null;

$(document).ready(function() {
  getWidget(true);
  setInterval(function(){getWidget(false);}, 60000);
});

function getWidget(firstTime) {
  $.ajax({
    url: "<?php echo url_for('@commentaires_widget'); ?>",
    async: true,
    cache: false,
    beforeSend: function(xhr) {
      xhr.setRequestHeader("If-Modified-Since", "0");
      if (firstTime) {
        $(".box_widget").css("overflow", "hidden");
        $("#coms_widget").css("overflow", "hidden");
        $("#coms_widget").css("display", "none");
      } else clearTimeout(timerWidget);
      $(".box_widget h2").append('<div id="chargement_widget"><\/div>');
    },
    success: function(html) {
      var elid = "coms_widget" + (firstTime ? '' : '_update');
      if ($.browser.msie == true) {
        var obj = document.getElementById(elid);
  	    obj.innerHTML = html;
      } else {
        $("#"+elid).text("");
  	    $("#"+elid).append(html);
      }
      if (firstTime) {
        $(".commentaire_widget").each(function() {
          commentaires[i] = $(this).attr("id");
          $(this).css("display", "none");
          i++;
        });
        changeCommentaire(0);
      } else {
        var q = 0;
        $("#coms_widget_update div[class='commentaire_widget']").each(function() {
          commentaires_update[q] = $(this).attr("id");
          $("#"+commentaires_update[q]).css("display", "none");
          q++;
        });
        commentaires = commentaires_update;
        commentaires_update = new Array();
      }
    },
    complete: function() {
      if (firstTime)
        $("#coms_widget").fadeIn(1000);
      $("#chargement_widget").remove();
      if (!firstTime) {
        $("#coms_widget").text("");
        $(".commentaire_widget").css("display", "none");
        i = 0;
        changeCommentaire(i);
      }
    },
    error: function(e) {
      $("#chargement_widget").remove();
    }
  });
}

function changeCommentaire(i) {
  if (i == commentaires.length) { i = 0; }
  var cache = i - 2;
  if (i == 0) { cache = commentaires.length - 2; }
  if (i == 1) { cache = commentaires.length - 1; }
  var com_suivant = $("#"+commentaires[i]).detach();
  $("#coms_widget").prepend(com_suivant);
  com_suivant = null;
  $("#"+commentaires[cache]).fadeOut(300);
  $("#"+commentaires[i]).slideDown(500);
  i++;
  timerWidget = setTimeout(function(){ changeCommentaire(i); }, 10000);
}
</script>
<div class="box_widget">
  <h2><span style="margin-right: 5px;"><img alt="comments" src="<?php echo $sf_request->getRelativeUrlRoot(); ?>/images/xneth/ico_comment.png" /></span><a href="<?php echo url_for('@commentaires'); ?>">Les derniers commentaires</a><span class="rss"><a href="<?php echo url_for('@commentaires_rss'); ?>"><?php echo image_tag('xneth/rss.png', 'alt="Flux rss"'); ?></a></span></h2>
  <div id="coms_widget">
    <noscript><?php include_component('commentaire', 'showWidget'); ?></noscript>
  </div>
  <div style="display: hidden;" id="coms_widget_update"></div>
</div>
