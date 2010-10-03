$('.jstitle').mousemove(function(e) {
    if ($('#jstitle').length == 0) {
      $('body').append('<div id="jstitle" style="text-align: center; display: none; position: absolute; z-index: 888; border: 1px solid black;padding: 5px;"></div>')
    }
    if ($(this).attr('title')) {
      title = $(this).attr('title').replace(/ \-\- /g, '<br/>').replace(/^([^<]+)<br/, '<b>$1</b><br');
      if ($(this).hasClass('phototitle') && !title.match(/<img src/)) {
        title = '<img src=\'' + $(this).children('.urlphoto')[0].href.replace(/\/([^\/]+)$/, "/depute/photo/$1/70") + '\'/><br/>' + title;
      }
      $(this).attr('jstitle', title);
      $(this).attr('title', '');
    }
    $('#jstitle').html($(this).attr('jstitle'));
    $('#jstitle').css('background-color', "white");
    $('#jstitle').css('top', e.pageY+10);
    $('#jstitle').css('left', e.pageX+10);
    $('#jstitle').css('display', 'block');
  });
$('.jstitle').mouseout(function() {
    $(this).attr('title', $(this).attr('jstitle'));
    $('#jstitle').css('display', 'none');
  });
