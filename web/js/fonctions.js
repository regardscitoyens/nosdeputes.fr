$(document).ready(function() {
    if (typeof additional_load != 'undefined')
      additional_load();
    // Ajax login
    if (!$('#header_login').attr('value')) {
      $('#header_login').attr('value', 'Identifiant');
    }
    if (!$('#header_pass').attr('value')) {
      $('#header_pass').attr('value', '______________');
    }

  // Menu
  $(".menu_navigation a").mouseover(function() {
    $(".menu_navigation a").removeClass("selected");
    for (i=1; i<=3; i++) { $('#sous_menu_'+i).css("display", "none"); }
    if ($(this).parent().attr("id") == "item2") { $(this).attr("class", "selected"); $('#sous_menu_1').css("display", "block"); }
    if ($(this).parent().attr("id") == "item3") { $(this).attr("class", "selected"); $('#sous_menu_2').css("display", "block"); }
    if ($(this).parent().attr("id") == "item4") { $(this).attr("class", "selected"); $('#sous_menu_3').css("display", "block"); }
  });

  $("input.examplevalue").focus(function() {
    if (!$(this).attr('default')) {
        $(this).attr('default', $(this).val());
    }
    if ($(this).attr('default') == $(this).val())
        $(this).val('');
  });
  $("input.examplevalue").blur(function() {
    if (!$(this).val()) {
        $(this).val($(this).attr('default'));
    }
  });

  window.jsTitle = function() {
  $('.jstitle').mousemove(function(e) {
    if ($('#jstitle').length == 0) {
      $('body').append('<div id="jstitle" style="text-align: center; display: none; position: absolute; z-index: 888; border: 1px solid black;padding: 5px; min-width: 150px; max-width: 600px;"></div>')
    }
    if ($(this).attr('title')) {
      $(this).find("title").remove();
      title = $(this).attr('title').replace(/ \-\- /g, '<br/>').replace(/^([^<]+)<br/, '<b>$1</b><br');
      if ($(this).hasClass('phototitle') && !title.match(/<img src/)) {
        title = '<img src=\'' + $(this).find('.urlphoto').attr('title').replace(/\/([^\/]+)$/, "/depute/photo/$1/70") + '\'/><br/>' + title;
      }
      $(this).attr('jstitle', title);
      $(this).attr('title', '');
    }
    $('#jstitle').html($(this).attr('jstitle'));
    $('#jstitle').css('background-color', "white");
    $('#jstitle').css('top', e.pageY+15);
    $('#jstitle').css('left', e.pageX-75);
    $('#jstitle').css('display', 'block');
  });
  $('.jstitle').mouseout(function() {
    $(this).attr('title', $(this).attr('jstitle'));
    $('#jstitle').css('display', 'none');
  });
  };
  window.jsTitle();

  //Redirection d'un lien envoyé depuis une page ajax
  url = document.location+'';
  if (url.match(/#date=/) && constructLien) {
      document.location = constructLien(url.replace(/.*#date=/, ''));
  }
  // Redimensionnement automatique des textareas
  $('textarea').scroll(function() {
    textarea_height = $(this).height();
    $(this).height(textarea_height + 10);
  });

  // Redimensionnement si page trop courte
  var minHeight = $(window).height() - 306;
  if ($("#corps_page").height() < minHeight)
    $("#corps_page").css("min-height", minHeight+"px");

}); // fin document ready

function uniqueArray(array) {
  if ($.isArray(array)){
  var duplique = {}; var len, i;
  for (i = 0, len = array.length; i < len; i++){
    var test = array[i].toString();
    if (duplique[test]) { array.splice(i,1); len--; i--; } else { duplique[test] = true; }
    }
  }
  else {
  alert(array+" n'est pas un array");
  }
  return(array);
}

// Recherche "case insensitive" pour jquery ; ex: $("div:Contains("str")")
jQuery.expr[':'].Contains = function(a, i, m) {
  return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};
