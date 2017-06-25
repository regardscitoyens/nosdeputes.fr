$(document).ready(function(){
/* survol du txt */
  $(".dep_map").on("mouseover", function() {
    var dep = $(this).attr("id").substring(3),
      map = $(".departement"+dep+", .circo"+dep);
    if (map.length)
      map.attr('class', map.attr('class')+' hover');
    $(".map"+dep).mouseover();
  });
  $(".dep_map").on("mouseout", function() {
    var dep = $(this).attr("id").substring(3),
      map = $(".departement"+dep+", .circo"+dep);
    if (map.length)
      map.attr('class', map.attr('class').replace(/ hover/, ''));
    $(".map"+dep).mouseout();
  });
  /* survol de la map ou du svg */
  var d = { running: '0' };
  $("g.circonscription, area, .departement").on("mouseover", d, function(e) {
    $(this).addClass("hover");
    var dep = $(this).attr("id").replace(/^(d|map)/, "").replace(/-\d$/, "").replace(/^c/, "999-"),
      map = $("#carte .departement"+dep);
    if (map.length)
      map.attr('class', map.attr('class')+' hover');
    $(".dep"+dep+", #dep"+dep).addClass('hover');
    if (e.data.running == 0) {
      d.running = 1;
      $(".map"+dep).filter(":not(.hover)").mouseover();
      d.running = 0;
    }
  });
  $("g.circonscription, area, .departement, .departement path").on("mouseout", function() {
    $(this).removeClass("hover");
    var dep = $(this).attr("id").replace(/^(d|map)/, "").replace(/-\d$/, "").replace(/^c/, "999-"),
      map = $("#carte .departement"+dep);
    if (map.length)
      map.attr('class', map.attr('class').replace(/ hover/, ''));
    $(".dep"+dep+", #dep"+dep).removeClass('hover');
    $(".map"+dep).filter(".hover").mouseout();
  });
});
