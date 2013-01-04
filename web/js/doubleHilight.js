/* survol du txt */
$(".dep_map").live("mouseover", function() {
  dep = $(this).attr("id").substring(3);
  $(".map"+dep).mouseover();
})
$(".dep_map").live("mouseout", function() {
  dep = $(this).attr("id").substring(3);
  $(".map"+dep).mouseout();
})
/* survol de la map */
var d = { running: '0' };
$("area").live("mouseover", d, function(e) {
  $(this).addClass("hover");
  dep = $(this).attr("id").substring(3,9).replace(/-0$/, "");
  $(".dep"+dep).css("background-color", "#D1EA74");
  $(".dep"+dep).css("opacity", 0.8);
  if (e.data.running == 0) {
    d.running = 1;
    $(".map"+dep).filter(":not(.hover)").mouseover();
    d.running = 0;
  }
})
$("area").live("mouseout", function() {
  $(this).removeClass("hover");
  dep = $(this).attr("id").substring(3,9).replace(/-0$/, "");
  $(".dep"+dep).css("background-color", "#fff");
  $(".dep"+dep).css("opacity", 1);
  $(".map"+dep).filter(".hover").mouseout();
})
