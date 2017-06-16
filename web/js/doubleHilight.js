$(document).ready(function(){
/* survol du txt */
  $(".dep_map").on("mouseover", function() {
    dep = $(this).attr("id").substring(3);
    $(".map"+dep).mouseover();
    $(".departement"+dep).attr('class', $(".departement"+dep).attr('class') + " hover");
  });
  $(".dep_map").on("mouseout", function() {
    dep = $(this).attr("id").substring(3);
    $(".map"+dep).mouseout();
    $(".departement"+dep).attr('class', $(".departement"+dep).attr('class').replace(/ hover/, ''));
  });
  /* survol de la map ou du svg */
  var d = { running: '0' };
  $("area, .departement").on("mouseover", d, function(e) {
    $(this).addClass("hover");
    dep = $(this).attr("id").replace(/^(d|map)/, "").replace(/-0$/, "");
    $(".dep"+dep+", #dep"+dep).css("background-color", "#D1EA74");
    $(".dep"+dep).css("opacity", 0.8);
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
    $(".dep"+dep).css("opacity", 1);
    $(".map"+dep).filter(".hover").mouseout();
  });
});
