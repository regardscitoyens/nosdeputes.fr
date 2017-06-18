$(document).ready(function(){
/* survol du txt */
  $(".dep_map").on("mouseover", function() {
    dep = $(this).attr("id").substring(3);
    $(".map"+dep).mouseover();
    $(".departement"+dep).addClass('hover');
  });
  $(".dep_map").on("mouseout", function() {
    dep = $(this).attr("id").substring(3);
    $(".map"+dep).mouseout();
    $(".departement"+dep).removeClass('hover');
  });
  /* survol de la map ou du svg */
  var d = { running: '0' };
  $("area, .departement").on("mouseover", d, function(e) {
    $(this).addClass("hover");
    dep = $(this).attr("id").replace(/^(d|map)/, "").replace(/-0$/, "");
    $(".dep"+dep+", #dep"+dep).css("background-color", "#D1EA74");
    if (e.data.running == 0) {
      d.running = 1;
      $(".map"+dep).filter(":not(.hover)").mouseover();
      d.running = 0;
    }
  });
  $("area, .departement, .departement path").on("mouseout", function() {
    $(this).removeClass("hover");
    dep = $(this).attr("id").replace(/^(d|map)/, "").replace(/-0$/, "");
    $(".dep"+dep+", #dep"+dep).css("background-color", "#fff");
    $(".map"+dep).filter(".hover").mouseout();
  });
});
